<?php
// Добавьте ЭТИ 3 строки в самое начало файла
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'learning-platform.local';
define('SITE_URL', $protocol . '://' . $host);

define('DB_HOST', 'localhost');
define('DB_NAME', 'learning_platform');
define('DB_USER', 'lpuser');
define('DB_PASS', 'learnplat852');

define('FIREBIRD_DB_PATH', __DIR__ . '/../employee.fdb');
define('FIREBIRD_USER', 'SYSDBA');
define('FIREBIRD_PASS', 'masterkey');

define('SITE_NAME', 'WebDev Learning Platform');
#define('SITE_URL', 'http://learning-platform.local');

error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();
?>