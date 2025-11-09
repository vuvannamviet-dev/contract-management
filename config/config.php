<?php
// Application configuration
define('APP_NAME', 'Contract Management System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/contract-management');

// Upload directory configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/contracts/');
define('MAX_FILE_SIZE', 10485760); // 10MB

// Date format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
