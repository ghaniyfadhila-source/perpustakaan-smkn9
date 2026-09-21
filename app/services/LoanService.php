<?php

require_once __DIR__ . '/../helpers/Phone.php'; // sesuaikan path jika beda

class LoanService {

  public function borrow(string $memberId, array $itemCodes, int $staffId): array {
    $member = DB::selectOne("SELECT member_id, member_name, expire_date, member_type_id
                            FROM member WHERE member_id=? LIMIT 1", "s", [$memberId]);
    if (!$member) return ['ok'=>false, 'error'=>'Anggota tidak ditemukan'];

    if (strtotime($member['expire_date']) < strtotime(date('Y-m-d'))) {
      return ['ok'=>false, 'error'=>'Masa aktif anggota sudah habis'];
    }

    $results = [];
    foreach ($itemCodes as $code) {
      $code = trim($code);
      if ($code === '') continue;

      $item = DB::selectOne("SELECT i.item_code, i.biblio_id, i.coll_type_id, b.gmd_id, b.title, b.call_number, b.classification
                             FROM item i JOIN biblio b ON b.biblio_id=i.biblio_id
                             WHERE i.item_code=? LIMIT 1", "s", [$code]);
      if (!$item) return ['ok'=>false, 'error'=>"Barcode tidak valid: $code"];

      $onLoan = DB::selectOne("SELECT loan_id FROM loan
                              WHERE item_code=? AND is_lent=1 AND is_return=0 LIMIT 1", "s", [$code]);
      if ($onLoan) return ['ok'=>false, 'error'=>"Buku sedang dipinjam: $code"];

      $rule = $this->resolveRule((int)$member['member_type_id'], (int)$item['coll_type_id'], (int)$item['gmd_id']);

      $cnt = DB::selectOne("SELECT COUNT(*) c FROM loan
                            WHERE member_id=? AND is_lent=1 AND is_return=0", "s", [$memberId]);
      if ((int)$cnt['c'] >= (int)$rule['loan_limit']) {
        return ['ok'=>false, 'error'=>"Limit pinjam tercapai (max {$rule['loan_limit']})"];
      }

      $loanDate = date('Y-m-d');
      $dueDate  = date('Y-m-d', strtotime($loanDate." +{$rule['loan_periode']} day"));

      DB::exec("INSERT INTO loan (item_code, member_id, loan_date, due_date, loan_rules_id, is_lent, is_return, input_date, uid)
                VALUES (?, ?, ?, ?, ?, 1, 0, NOW(), ?)",
                "ssssii", [$code, $memberId, $loanDate, $dueDate, (int)$rule['loan_rules_id'], $staffId]);
      $loanId = DB::lastId();

      DB::exec("INSERT INTO loan_history (loan_id, item_code, biblio_id, title, call_number, classification, member_id, member_name, loan_date, due_date, is_lent, is_return, input_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, NOW())",
                "isisssssss", [
                  $loanId, $code, (int)$item['biblio_id'], $item['title'],
                  $item['call_number'], $item['classification'],
                  $memberId, $member['member_name'], $loanDate, $dueDate
                ]);

      AuditService::log('staff', (string)$staffId, 'circulation', 'borrow', "BORROW loan_id=$loanId member=$memberId item=$code due=$dueDate");

      $results[] = ['loan_id'=>$loanId, 'item_code'=>$code, 'due_date'=>$dueDate];
    }

    return ['ok'=>true, 'data'=>$results];
  }

  public function returnBook(string $itemCode, int $staffId): array {
    $loan = DB::selectOne("SELECT * FROM loan
                           WHERE item_code=? AND is_lent=1 AND is_return=0 LIMIT 1", "s", [$itemCode]);
    if (!$loan) return ['ok'=>false, 'error'=>'Tidak ada pinjaman aktif untuk barcode ini'];

    $rule = $this->getRuleById((int)$loan['loan_rules_id']);
    if (!$rule) $rule = ['fine_each_day'=>0, 'grace_periode'=>0];

    $returnDate = date('Y-m-d');
    $lateDays = $this->lateDays($loan['due_date'], $returnDate, (int)$rule['grace_periode']);
    $fine = $lateDays * (int)$rule['fine_each_day'];

    DB::exec("UPDATE loan SET is_return=1, return_date=?, last_update=NOW(), uid=? WHERE loan_id=?",
             "sii", [$returnDate, $staffId, (int)$loan['loan_id']]);

    if ($fine > 0) {
      $desc = "Denda terlambat $lateDays hari (barcode {$loan['item_code']})";

      // 1) simpan denda
      DB::exec("INSERT INTO fines (fines_date, member_id, debet, credit, description)
               VALUES (CURDATE(), ?, ?, 0, ?)",
               "sis", [$loan['member_id'], $fine, $desc]);

      // 2) setelah berhasil simpan denda -> antrikan WA
      $this->enqueueFineWhatsApp(
        (string)$loan['member_id'],
        (int)$fine,
        $desc
      );
    }

    AuditService::log('staff', (string)$staffId, 'circulation', 'return', "RETURN loan_id={$loan['loan_id']} item={$itemCode} late=$lateDays fine=$fine");
    return ['ok'=>true, 'late_days'=>$lateDays, 'fine'=>$fine, 'member_id'=>$loan['member_id'], 'loan_id'=>$loan['loan_id']];
  }

  /**
   * Buat antrian WA kategori FINE ke wa_outbox
   * - nomor diambil dari member.member_phone
   * - kalau kolom kamu beda (no_hp/phone), ganti querynya.
   */
  private function enqueueFineWhatsApp(string $memberId, int $amount, string $desc): void
  {
    // ambil data member + hp
    $m = DB::selectOne("SELECT member_name, member_phone AS phone
                        FROM member
                        WHERE member_id=? LIMIT 1", "s", [$memberId]);
    if (!$m) return;

    $phone = Phone::normalizeID($m['phone'] ?? '');
    if ($phone === '') return;

    $msg =
      "💰 *Informasi Denda*\n".
      "Nama: ".$m['member_name']."\n".
      "ID: ".$memberId."\n".
      "Nominal: Rp ".number_format($amount, 0, ',', '.')."\n".
      "Keterangan: ".$desc."\n\n".
      "Perpustakaan SMKN 9 Semarang";

    // anti double: kalau dalam 1 hari sudah ada pesan dengan deskripsi yang sama, skip
    $exists = DB::selectOne("
      SELECT id FROM wa_outbox
      WHERE category='FINE'
        AND member_id=?
        AND message=?
        AND DATE(created_at)=CURDATE()
      LIMIT 1
    ", "ss", [$memberId, $msg]);

    if ($exists) return;

    DB::exec("
      INSERT INTO wa_outbox(member_id, phone, category, message, scheduled_at)
      VALUES(?,?,?,?,?)
    ", "sssss", [
      $memberId,
      $phone,
      'FINE',
      $msg,
      date('Y-m-d H:i:s') // kirim langsung
    ]);
  }

  public function editLoanTypo(int $loanId, array $newData, int $staffId): array {
    $before = DB::selectOne("SELECT * FROM loan WHERE loan_id=? LIMIT 1", "i", [$loanId]);
    if (!$before) return ['ok'=>false, 'error'=>'Loan tidak ditemukan'];

    DB::exec("UPDATE loan SET member_id=?, item_code=?, due_date=?, last_update=NOW(), uid=? WHERE loan_id=?",
             "sssii", [$newData['member_id'], $newData['item_code'], $newData['due_date'], $staffId, $loanId]);

    $after = DB::selectOne("SELECT * FROM loan WHERE loan_id=? LIMIT 1", "i", [$loanId]);

    AuditService::log('staff', (string)$staffId, 'circulation', 'edit_loan',
      "EDIT_LOAN loan_id=$loanId BEFORE=".json_encode($before)." AFTER=".json_encode($after));

    return ['ok'=>true];
  }

  private function resolveRule(int $memberTypeId, int $collTypeId, int $gmdId): array {
    $r = DB::selectOne("SELECT * FROM mst_loan_rules
                        WHERE member_type_id=? AND coll_type_id=? AND gmd_id=? LIMIT 1",
                        "iii", [$memberTypeId, $collTypeId, $gmdId]);
    if ($r) return $r;

    $mt = DB::selectOne("SELECT * FROM mst_member_type WHERE member_type_id=? LIMIT 1", "i", [$memberTypeId]);
    if (!$mt) return ['loan_rules_id'=>0,'loan_limit'=>1,'loan_periode'=>7,'fine_each_day'=>0,'grace_periode'=>0];

    return [
      'loan_rules_id'=>0,
      'loan_limit'=>(int)$mt['loan_limit'],
      'loan_periode'=>(int)$mt['loan_periode'],
      'fine_each_day'=>(int)$mt['fine_each_day'],
      'grace_periode'=>(int)$mt['grace_periode'],
    ];
  }

  private function getRuleById(int $id): ?array {
    if ($id <= 0) return null;
    return DB::selectOne("SELECT * FROM mst_loan_rules WHERE loan_rules_id=? LIMIT 1", "i", [$id]);
  }

  private function lateDays(string $due, string $ret, int $grace): int {
    $dd = strtotime($due); $rd = strtotime($ret);
    if ($rd <= $dd) return 0;
    $days = (int)floor(($rd - $dd)/86400);
    return max(0, $days - $grace);
  }
}