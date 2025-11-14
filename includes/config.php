<?php

define('DB_HOST', 'MySQL-8.2'); 
define('DB_NAME', 'learning_platform');
define('DB_USER', 'root');
define('DB_PASS', '');

define('FIREBIRD_DB_PATH', __DIR__ . '/../employee.fdb');
define('FIREBIRD_USER', 'SYSDBA');
define('FIREBIRD_PASS', 'masterkey');

define('SITE_NAME', 'WebDev Learning Platform');
define('SITE_URL', 'http://learning-platform.local');

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
?>