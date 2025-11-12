<?php
// logout.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

logoutUser();
$_SESSION['message'] = 'Вы успешно вышли из системы';
header('Location: /login.php');
exit;
?>