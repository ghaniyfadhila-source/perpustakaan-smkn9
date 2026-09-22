<?php
require_once __DIR__ . '/../helpers/Phone.php';

class BookRequestModel {

  public function create(array $data): int {
    $sql = "INSERT INTO book_requests
              (member_id, biblio_id, item_code, request_date, status)
            VALUES
              (?, ?, ?, NOW(), 'PENDING')";
    DB::exec($sql, "sis", [
      $data['member_id'],
      (int)$data['biblio_id'],
      $data['item_code'] ?? null
    ]);
    return DB::lastId();
  }

  public function find(int $requestId): ?array {
    return DB::selectOne("SELECT * FROM book_requests WHERE request_id=? LIMIT 1", "i", [$requestId]);
  }

  public function findWithDetails(int $requestId): ?array {
    $sql = "SELECT br.*, b.title, b.isbn_issn, b.publish_year, b.call_number, b.classification,
                   m.member_name, m.username, i.item_code as actual_item_code
            FROM book_requests br
            JOIN biblio b ON b.biblio_id = br.biblio_id
            JOIN member m ON m.member_id = br.member_id
            LEFT JOIN item i ON i.item_code = br.item_code AND i.biblio_id = br.biblio_id
            WHERE br.request_id=? LIMIT 1";
    return DB::selectOne($sql, "i", [$requestId]);
  }

  public function getByMember(string $memberId, string $status = ''): array {
    $sql = "SELECT br.*, b.title, b.isbn_issn, b.publish_year, b.call_number, b.classification,
                   i.item_code as actual_item_code
            FROM book_requests br
            JOIN biblio b ON b.biblio_id = br.biblio_id
            LEFT JOIN item i ON i.item_code = br.item_code AND i.biblio_id = br.biblio_id
            WHERE br.member_id = ?";
    $params = [$memberId];
    $types = "s";

    if ($status !== '') {
      $sql .= " AND br.status = ?";
      $types .= "s";
      $params[] = $status;
    }

    $sql .= " ORDER BY br.request_date DESC";
    return DB::select($sql, $types, $params);
  }

  public function getAll(string $status = '', int $limit = 100): array {
    $sql = "SELECT br.*, b.title, b.isbn_issn, b.publish_year, b.call_number, b.classification,
                   m.member_name, m.username, i.item_code as actual_item_code
            FROM book_requests br
            JOIN biblio b ON b.biblio_id = br.biblio_id
            JOIN member m ON m.member_id = br.member_id
            LEFT JOIN item i ON i.item_code = br.item_code AND i.biblio_id = br.biblio_id";
    $params = [];
    $types = "";

    if ($status !== '') {
      $sql .= " WHERE br.status = ?";
      $types .= "s";
      $params[] = $status;
    }

    $sql .= " ORDER BY br.request_date DESC LIMIT ?";
    $types .= "i";
    $params[] = $limit;

    return DB::select($sql, $types, $params);
  }

  public function approve(int $requestId, int $staffId, string $itemCode, string $customDueDate = ''): array {
    $req = $this->find($requestId);
    if (!$req) return ['ok'=>false, 'error'=>'Request tidak ditemukan'];
    if ($req['status'] !== 'PENDING') return ['ok'=>false, 'error'=>'Request sudah diproses'];

    // Validate item availability
    $item = DB::selectOne("SELECT i.item_code, i.biblio_id, b.gmd_id
                           FROM item i JOIN biblio b ON b.biblio_id=i.biblio_id
                           WHERE i.item_code=? AND i.biblio_id=? LIMIT 1", "si", [$itemCode, $req['biblio_id']]);
    if (!$item) return ['ok'=>false, 'error'=>'Eksemplar tidak valid untuk buku ini'];

    $onLoan = DB::selectOne("SELECT loan_id FROM loan WHERE item_code=? AND is_lent=1 AND is_return=0 LIMIT 1", "s", [$itemCode]);
    if ($onLoan) return ['ok'=>false, 'error'=>'Buku sedang dipinjam'];

    $member = DB::selectOne("SELECT member_id, member_name, member_type_id, expire_date FROM member WHERE member_id=? LIMIT 1", "s", [$req['member_id']]);
    if (!$member) return ['ok'=>false, 'error'=>'Anggota tidak ditemukan'];
    if (strtotime($member['expire_date']) < strtotime(date('Y-m-d'))) {
      return ['ok'=>false, 'error'=>'Masa aktif anggota sudah habis'];
    }

    $svc = new LoanService();
    $res = $svc->borrow($req['member_id'], [$itemCode], $staffId, $customDueDate);
    if (empty($res['ok'])) return ['ok'=>false, 'error'=>$res['error']];

    $loanId = $res['data'][0]['loan_id'] ?? 0;
    $dueDate = $res['data'][0]['due_date'] ?? '';

    DB::exec("UPDATE book_requests SET status='APPROVED', approved_by=?, approved_at=NOW(), loan_id=?, item_code=? WHERE request_id=?",
             "iisi", [$staffId, $loanId, $itemCode, $requestId]);

    // Send WhatsApp notification
    $this->notifyApproved($req['member_id'], $req['biblio_id'], $itemCode, $dueDate);

    return ['ok'=>true, 'loan_id'=>$loanId, 'due_date'=>$dueDate];
  }

  public function reject(int $requestId, int $staffId, string $reason): array {
    $req = $this->find($requestId);
    if (!$req) return ['ok'=>false, 'error'=>'Request tidak ditemukan'];
    if ($req['status'] !== 'PENDING') return ['ok'=>false, 'error'=>'Request sudah diproses'];

    DB::exec("UPDATE book_requests SET status='REJECTED', rejected_by=?, rejected_at=NOW(), rejection_reason=? WHERE request_id=?",
             "isi", [$staffId, $reason, $requestId]);

    // Send WhatsApp notification
    $this->notifyRejected($req['member_id'], $req['biblio_id'], $reason);

    return ['ok'=>true];
  }

  public function cancel(int $requestId, string $memberId): array {
    $req = $this->find($requestId);
    if (!$req) return ['ok'=>false, 'error'=>'Request tidak ditemukan'];
    if ($req['member_id'] !== $memberId) return ['ok'=>false, 'error'=>'Tidak berhak membatalkan request ini'];
    if ($req['status'] !== 'PENDING') return ['ok'=>false, 'error'=>'Hanya request PENDING yang bisa dibatalkan'];

    DB::exec("UPDATE book_requests SET status='CANCELLED' WHERE request_id=?", "i", [$requestId]);
    return ['ok'=>true];
  }

  private function notifyApproved(string $memberId, int $biblioId, string $itemCode, string $dueDate): void {
    $m = DB::selectOne("SELECT member_name, member_phone FROM member WHERE member_id=? LIMIT 1", "s", [$memberId]);
    $b = DB::selectOne("SELECT title FROM biblio WHERE biblio_id=? LIMIT 1", "i", [$biblioId]);
    if (!$m || !$b) return;

    $phone = Phone::normalizeID($m['member_phone'] ?? '');
    if ($phone === '') return;

    $msg = "✅ *Request Peminjaman DISETUJUI*\n\n"
         . "Yth. {$m['member_name']}\n"
         . "Request Anda untuk buku:\n"
         . "• Judul : {$b['title']}\n"
         . "• Kode  : {$itemCode}\n"
         . "• Jatuh tempo: {$dueDate}\n\n"
         . "Silakan ambil buku di perpustakaan.\n"
         . "Terima kasih.\n\n"
         . "Perpustakaan SMKN 9 Semarang";

    DB::exec("
      INSERT INTO wa_outbox(member_id, phone, category, message, status, scheduled_at, created_at)
      VALUES(?,?,?,?, 'PENDING', NOW(), NOW())
    ", "ssss", [$memberId, $phone, 'REQUEST_APPROVED', $msg]);
  }

  private function notifyRejected(string $memberId, int $biblioId, string $reason): void {
    $m = DB::selectOne("SELECT member_name, member_phone FROM member WHERE member_id=? LIMIT 1", "s", [$memberId]);
    $b = DB::selectOne("SELECT title FROM biblio WHERE biblio_id=? LIMIT 1", "i", [$biblioId]);
    if (!$m || !$b) return;

    $phone = Phone::normalizeID($m['member_phone'] ?? '');
    if ($phone === '') return;

    $msg = "❌ *Request Peminjaman DITOLAK*\n\n"
         . "Yth. {$m['member_name']}\n"
         . "Request Anda untuk buku:\n"
         . "• Judul : {$b['title']}\n\n"
         . "Alasan: {$reason}\n\n"
         . "Anda bisa mengajukan request lain.\n"
         . "Terima kasih.\n\n"
         . "Perpustakaan SMKN 9 Semarang";

    DB::exec("
      INSERT INTO wa_outbox(member_id, phone, category, message, status, scheduled_at, created_at)
      VALUES(?,?,?,?, 'PENDING', NOW(), NOW())
    ", "ssss", [$memberId, $phone, 'REQUEST_REJECTED', $msg]);
  }
}