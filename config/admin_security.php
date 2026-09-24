<?php
// config/admin_security.php
// Password: fadhil123 (hashed with password_hash)
return [
    'request_verification_password' => getenv('ADMIN_VERIFY_PASS_HASH') ?: '$2y$10$QuYkMvw6W28P..b.H19FpO1g/OkgUpMqUp12n4ebLWaP9FibHLHmq', // fadhil123
    'request_verification_timeout' => (int)(getenv('ADMIN_VERIFY_TIMEOUT') ?: 0), // 0 = selama session
];