<?php
// config/whatsapp.php
return [
  'base_url'  => getenv('WA_BASE_URL') ?: 'https://app.whacenter.com/api/send',
  'device_id' => getenv('WA_DEVICE_ID') ?: 'ba068e4514b32c28232b5a7ce0d16d3e',
  'timeout'   => (int)(getenv('WA_TIMEOUT') ?: 15),
];