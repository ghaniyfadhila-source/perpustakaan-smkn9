<?php
require __DIR__ . '/../../vendor/autoload.php'; // sesuaikan jika perlu
require __DIR__ . '/../helpers/Pdf.php';

class ReportsController {

  public function index() {
    require __DIR__ . '/../views/reports/index.php';
  }

  public function loans() {
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $rows = DB::select("
      SELECT l.loan_id, l.item_code, l.member_id, l.loan_date, l.due_date,
             u.realname AS staff_name,
             b.title
      FROM loan l
      LEFT JOIN user u ON u.user_id=l.uid
      LEFT JOIN item i ON i.item_code=l.item_code
      LEFT JOIN biblio b ON b.biblio_id=i.biblio_id
      WHERE l.loan_date BETWEEN ? AND ?
      ORDER BY l.loan_id DESC
      LIMIT 1000
    ", "ss", [$from, $to]);

    require __DIR__ . '/../views/reports/loans.php';
  }

  public function returns() {
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $rows = DB::select("
      SELECT l.loan_id, l.item_code, l.member_id, l.return_date,
             u.realname AS staff_name,
             b.title
      FROM loan l
      LEFT JOIN user u ON u.user_id=l.uid
      LEFT JOIN item i ON i.item_code=l.item_code
      LEFT JOIN biblio b ON b.biblio_id=i.biblio_id
      WHERE l.is_return=1 AND l.return_date BETWEEN ? AND ?
      ORDER BY l.loan_id DESC
      LIMIT 1000
    ", "ss", [$from, $to]);

    require __DIR__ . '/../views/reports/returns.php';
  }

  public function overdue() {
    $rows = DB::select("
      SELECT l.loan_id,
             l.member_id,
             m.member_name,
             l.item_code,
             b.title,
             l.due_date,
             DATEDIFF(CURDATE(), l.due_date) AS late_days,
             u.realname AS staff_name
      FROM loan l
      JOIN member m ON m.member_id = l.member_id
      JOIN item i ON i.item_code = l.item_code
      JOIN biblio b ON b.biblio_id = i.biblio_id
      LEFT JOIN `user` u ON u.user_id = l.uid
      WHERE l.is_return = 0
        AND CURDATE() > l.due_date
      ORDER BY l.due_date ASC
    ");

    require __DIR__ . '/../views/reports/overdue.php';
  }

  public function fines() {
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $rows = DB::select("
      SELECT fines_date, member_id, debet, credit, description
      FROM fines
      WHERE fines_date BETWEEN ? AND ?
      ORDER BY fines_id DESC
      LIMIT 1000
    ", "ss", [$from, $to]);

    require __DIR__ . '/../views/reports/fines.php';
  }

  /* =========================================================
     PDF METHODS (PROFESSIONAL, NO URL, COPYRIGHT FOOTER)
     ========================================================= */

  /**
   * Blok tanda tangan SUPER RAPI (Dompdf friendly)
   * - Ada "Semarang, dd/mm/yyyy" di kanan
   * - Ada Nama & NIP (placeholder) di bawah garis
   */
  private function signatureBlock(array $opts = []): string
  {
    $city = $opts['city'] ?? 'Semarang';
    $date = $opts['date'] ?? date('d/m/Y');

    $leftRole  = $opts['leftRole']  ?? 'Petugas';
    $midRole   = $opts['midRole']   ?? 'Mengetahui';
    $rightRole = $opts['rightRole'] ?? 'Kepala Perpustakaan';

    $leftName  = $opts['leftName']  ?? '________________________';
    $leftNip   = $opts['leftNip']   ?? 'NIP. ____________________';

    $midName   = $opts['midName']   ?? '________________________';
    $midNip    = $opts['midNip']    ?? 'NIP. ____________________';

    $rightName = $opts['rightName'] ?? '________________________';
    $rightNip  = $opts['rightNip']  ?? 'NIP. ____________________';

    return '
  <div style="margin-top:22px; font-size:10.5px;">
    <div style="text-align:right; margin-bottom:10px;">'.$city.', '.$date.'</div>

    <table class="sign-table" style="width:100%; border-collapse:collapse; table-layout:fixed;">
      <tr>
        <td style="width:33.33%; text-align:center; vertical-align:top; padding:0; border:none;">
          <div style="margin-bottom:56px;">'.$leftRole.'</div>
          <div style="display:inline-block; min-width:220px; padding-top:6px; border-top:1px solid #111; font-weight:700;">
            '.$leftName.'
          </div>
          <div style="margin-top:2px; font-size:10px; color:#333;">'.$leftNip.'</div>
        </td>

        <td style="width:33.33%; text-align:center; vertical-align:top; padding:0; border:none;">
          <div style="margin-bottom:56px;">'.$midRole.'</div>
          <div style="display:inline-block; min-width:220px; padding-top:6px; border-top:1px solid #111; font-weight:700;">
            '.$midName.'
          </div>
          <div style="margin-top:2px; font-size:10px; color:#333;">'.$midNip.'</div>
        </td>

        <td style="width:33.33%; text-align:center; vertical-align:top; padding:0; border:none;">
          <div style="margin-bottom:56px;">'.$rightRole.'</div>
          <div style="display:inline-block; min-width:220px; padding-top:6px; border-top:1px solid #111; font-weight:700;">
            '.$rightName.'
          </div>
          <div style="margin-top:2px; font-size:10px; color:#333;">'.$rightNip.'</div>
        </td>
      </tr>
    </table>
  </div>
';
  }

  // 1) PDF LOANS
  public function loans_pdf() {
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $rows = DB::select("
      SELECT l.loan_id, l.item_code, l.member_id, l.loan_date, l.due_date,
             b.title,
             m.member_name
      FROM loan l
      LEFT JOIN item i ON i.item_code=l.item_code
      LEFT JOIN biblio b ON b.biblio_id=i.biblio_id
      LEFT JOIN member m ON m.member_id=l.member_id
      WHERE l.loan_date BETWEEN ? AND ?
      ORDER BY l.loan_id DESC
      LIMIT 2000
    ", "ss", [$from, $to]);

    $printedAt = ['date' => date('d/m/Y'), 'time' => date('H:i')];
    $title = "LAPORAN PEMINJAMAN";
    $subtitle = "Periode: <b>".htmlspecialchars($from)."</b> s/d <b>".htmlspecialchars($to)."</b> — Total: <b>".count($rows)."</b>";

    ob_start(); ?>
      <table>
        <thead>
          <tr>
            <th class="center" style="width:55px">ID</th>
            <th style="width:200px">Member</th>
            <th style="width:120px">Barcode</th>
            <th>Judul</th>
            <th style="width:95px">Pinjam</th>
            <th style="width:95px">Jatuh Tempo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="6" class="center muted" style="padding:16px">DATA TIDAK DITEMUKAN</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td class="center"><?= (int)$r['loan_id'] ?></td>
              <td>
                <b><?= htmlspecialchars(($r['member_name'] ?? '') !== '' ? $r['member_name'] : $r['member_id']) ?></b><br>
                <span class="muted">ID: <?= htmlspecialchars($r['member_id']) ?></span>
              </td>
              <td><?= htmlspecialchars($r['item_code']) ?></td>
              <td><?= htmlspecialchars($r['title'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['loan_date']) ?></td>
              <td><?= htmlspecialchars($r['due_date']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>

      <?= $this->signatureBlock([
        'city' => 'Semarang',
        'date' => date('d/m/Y'),
        'leftRole'  => 'Petugas',
        'midRole'   => 'Mengetahui',
        'rightRole' => 'Kepala Perpustakaan',
      ]); ?>
    <?php
    $contentHtml = ob_get_clean();

    ob_start();
    require __DIR__ . '/../views/reports/_pdf_layout.php';
    $html = ob_get_clean();

    Pdf::stream($html, "laporan-peminjaman-$from-sd-$to.pdf", false);
  }

  // 2) PDF RETURNS
  public function returns_pdf() {
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $rows = DB::select("
      SELECT l.loan_id, l.item_code, l.member_id, l.return_date,
             b.title,
             m.member_name
      FROM loan l
      LEFT JOIN item i ON i.item_code=l.item_code
      LEFT JOIN biblio b ON b.biblio_id=i.biblio_id
      LEFT JOIN member m ON m.member_id=l.member_id
      WHERE l.is_return=1 AND l.return_date BETWEEN ? AND ?
      ORDER BY l.loan_id DESC
      LIMIT 2000
    ", "ss", [$from, $to]);

    $printedAt = ['date' => date('d/m/Y'), 'time' => date('H:i')];
    $title = "LAPORAN PENGEMBALIAN";
    $subtitle = "Periode: <b>".htmlspecialchars($from)."</b> s/d <b>".htmlspecialchars($to)."</b> — Total: <b>".count($rows)."</b>";

    ob_start(); ?>
      <table>
        <thead>
          <tr>
            <th class="center" style="width:55px">ID</th>
            <th style="width:220px">Member</th>
            <th style="width:120px">Barcode</th>
            <th>Judul</th>
            <th style="width:110px">Tgl Kembali</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="5" class="center muted" style="padding:16px">DATA TIDAK DITEMUKAN</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td class="center"><?= (int)$r['loan_id'] ?></td>
              <td>
                <b><?= htmlspecialchars(($r['member_name'] ?? '') !== '' ? $r['member_name'] : $r['member_id']) ?></b><br>
                <span class="muted">ID: <?= htmlspecialchars($r['member_id']) ?></span>
              </td>
              <td><?= htmlspecialchars($r['item_code']) ?></td>
              <td><?= htmlspecialchars($r['title'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['return_date']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>

      <?= $this->signatureBlock([
        'city' => 'Semarang',
        'date' => date('d/m/Y'),
        'leftRole'  => 'Petugas',
        'midRole'   => 'Mengetahui',
        'rightRole' => 'Kepala Perpustakaan',
      ]); ?>
    <?php
    $contentHtml = ob_get_clean();

    ob_start();
    require __DIR__ . '/../views/reports/_pdf_layout.php';
    $html = ob_get_clean();

    Pdf::stream($html, "laporan-pengembalian-$from-sd-$to.pdf", false);
  }

  // 3) PDF OVERDUE
  public function overdue_pdf() {
    $rows = DB::select("
      SELECT l.loan_id, l.member_id, m.member_name, l.item_code, b.title, l.due_date,
             DATEDIFF(CURDATE(), l.due_date) AS late_days
      FROM loan l
      JOIN member m ON m.member_id = l.member_id
      JOIN item i ON i.item_code = l.item_code
      JOIN biblio b ON b.biblio_id = i.biblio_id
      WHERE l.is_return = 0 AND CURDATE() > l.due_date
      ORDER BY l.due_date ASC
    ");

    $printedAt = ['date' => date('d/m/Y'), 'time' => date('H:i')];
    $title = "LAPORAN TERLAMBAT";
    $subtitle = "Pinjaman aktif yang melewati due date — Total: <b>" . count($rows) . "</b>";

    ob_start(); ?>
      <table>
        <thead>
          <tr>
            <th class="center" style="width:55px">ID</th>
            <th style="width:210px">Member</th>
            <th style="width:110px">Barcode</th>
            <th>Judul</th>
            <th style="width:95px">Due Date</th>
            <th class="center" style="width:85px">Terlambat</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="6" class="center muted" style="padding:16px">TIDAK ADA YANG TERLAMBAT</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td class="center"><?= (int)$r['loan_id'] ?></td>
              <td>
                <b><?= htmlspecialchars($r['member_name'] ?? $r['member_id']) ?></b><br>
                <span class="muted">ID: <?= htmlspecialchars($r['member_id']) ?></span>
              </td>
              <td><?= htmlspecialchars($r['item_code']) ?></td>
              <td><?= htmlspecialchars($r['title'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['due_date']) ?></td>
              <td class="center"><b><?= (int)$r['late_days'] ?></b> hari</td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>

      <?= $this->signatureBlock([
        'city' => 'Semarang',
        'date' => date('d/m/Y'),
        'leftRole'  => 'Petugas',
        'midRole'   => 'Mengetahui',
        'rightRole' => 'Kepala Perpustakaan',
      ]); ?>
    <?php
    $contentHtml = ob_get_clean();

    ob_start();
    require __DIR__ . '/../views/reports/_pdf_layout.php';
    $html = ob_get_clean();

    Pdf::stream($html, "laporan-terlambat.pdf", false);
  }

  // 4) PDF FINES
  public function fines_pdf() {
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $rows = DB::select("
      SELECT f.fines_date, f.member_id, f.debet, f.credit, f.description,
             m.member_name
      FROM fines f
      LEFT JOIN member m ON m.member_id = f.member_id
      WHERE f.fines_date BETWEEN ? AND ?
      ORDER BY f.fines_id DESC
      LIMIT 2000
    ", "ss", [$from, $to]);

    $totalDebet = 0;
    $totalCredit = 0;
    foreach ($rows as $r) {
      $totalDebet  += (int)$r['debet'];
      $totalCredit += (int)$r['credit'];
    }

    $printedAt = ['date' => date('d/m/Y'), 'time' => date('H:i')];
    $title = "LAPORAN DENDA";
    $subtitle = "Periode: <b>".htmlspecialchars($from)."</b> s/d <b>".htmlspecialchars($to)."</b> — Total transaksi: <b>".count($rows)."</b>";

    ob_start(); ?>
      <table>
        <thead>
          <tr>
            <th style="width:95px">Tanggal</th>
            <th>Member</th>
            <th class="right" style="width:110px">Debet</th>
            <th class="right" style="width:110px">Credit</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="5" class="center muted" style="padding:16px">DATA TIDAK DITEMUKAN</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['fines_date']) ?></td>
              <td>
                <b><?= htmlspecialchars(($r['member_name'] ?? '') !== '' ? $r['member_name'] : $r['member_id']) ?></b><br>
                <span class="muted">ID: <?= htmlspecialchars($r['member_id']) ?></span>
              </td>
              <td class="right">Rp <?= number_format((int)$r['debet'], 0, ',', '.') ?></td>
              <td class="right">Rp <?= number_format((int)$r['credit'], 0, ',', '.') ?></td>
              <td><?= htmlspecialchars($r['description'] ?? '-') ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="2" class="right">TOTAL</th>
            <th class="right">Rp <?= number_format((int)$totalDebet, 0, ',', '.') ?></th>
            <th class="right">Rp <?= number_format((int)$totalCredit, 0, ',', '.') ?></th>
            <th></th>
          </tr>
        </tfoot>
      </table>

      <?= $this->signatureBlock([
        'city' => 'Semarang',
        'date' => date('d/m/Y'),
        'leftRole'  => 'Petugas',
        'midRole'   => 'Mengetahui',
        'rightRole' => 'Kepala Perpustakaan',
      ]); ?>
    <?php
    $contentHtml = ob_get_clean();

    ob_start();
    require __DIR__ . '/../views/reports/_pdf_layout.php';
    $html = ob_get_clean();

    Pdf::stream($html, "laporan-denda-$from-sd-$to.pdf", false);
  }
}