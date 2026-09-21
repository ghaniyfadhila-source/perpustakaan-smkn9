<?php
// app/helpers/Phone.php
class Phone
{
  public static function normalizeID(?string $raw): string
  {
    $raw = trim((string)$raw);
    if ($raw === '') return '';

    // buang semua selain angka
    $p = preg_replace('/[^0-9]/', '', $raw);

    // 0xxxx -> 62xxxx
    if (strpos($p, '0') === 0) $p = '62' . substr($p, 1);

    // 8xxxx -> 628xxxx (kalau user input tanpa 0)
    if (strpos($p, '8') === 0) $p = '62' . $p;

    return $p;
  }
}