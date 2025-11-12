<?php
// login_process.php 
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Все поля обязательны для заполнения';
    header('Location: /login.php');
    exit;
}

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Неверный логин или пароль';
        header('Location: /login.php');
        exit;
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['message'] = 'Добро пожаловать, ' . $user['username'] . '!';
    header('Location: /dashboard.php');
    exit;
    
} catch(Exception $e) {
    $_SESSION['error'] = 'Ошибка при входе: ' . $e->getMessage();
    header('Location: /login.php');
    exit;
}
?>