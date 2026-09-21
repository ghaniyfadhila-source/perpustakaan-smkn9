<?php
// app/helpers/WhatsApp.php
class WhatsApp
{
  public static function send(string $phone, string $message): array
  {
    $cfg = require __DIR__ . '/../../config/whatsapp.php';

    $url      = $cfg['base_url']  ?? '';
    $deviceId = $cfg['device_id'] ?? '';
    $timeout  = (int)($cfg['timeout'] ?? 15);

    if ($url === '' || $deviceId === '') {
      return [
        'ok' => false,
        'http_code' => 0,
        'response' => '',
        'error' => 'Config whatsapp.php belum lengkap (base_url/device_id kosong)',
      ];
    }

    // ✅ format sesuai Whacenter: device_id, number, message
    $data = [
      'device_id' => $deviceId,
      'number'    => $phone,
      'message'   => $message,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST           => true,
      CURLOPT_HTTPHEADER     => [
        'Content-Type: application/x-www-form-urlencoded',
      ],
      CURLOPT_POSTFIELDS     => http_build_query($data),
      CURLOPT_TIMEOUT        => $timeout,
    ]);

    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
      'ok'        => ($err === '' && $code >= 200 && $code < 300),
      'http_code' => $code,
      'response'  => $resp,
      'error'     => $err,
    ];
  }
}