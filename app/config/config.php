<?php
/**
 * Konfigurasi Aplikasi
 */

// Base URL (auto-detect scheme, host, and port)
$__scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$__host = $_SERVER['HTTP_HOST'] ?? 'localhost'; // includes port if present (e.g., localhost:8080)
define('BASE_URL', $__scheme . '://' . $__host . '/WISUDA/');

// App Settings
define('APP_NAME', 'Sistem Manajemen Wisuda');
define('APP_VERSION', '1.0.0');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour

// Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXCEL_TYPES', ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);

// Pagination
define('RECORDS_PER_PAGE', 25);

// QR Code Settings
define('QR_SIZE', 300);
define('QR_MARGIN', 10);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
