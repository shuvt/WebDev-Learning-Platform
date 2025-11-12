<?php
// register_process.php 
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register.php');
    exit;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($username) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'Все поля обязательны для заполнения';
    header('Location: /register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Пароль должен быть не менее 6 символов';
    header('Location: /register.php');
    exit;
}

try {
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Пользователь с таким логином или email уже существует';
        header('Location: /register.php');
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password]);
    
    $_SESSION['message'] = 'Регистрация успешна! Теперь вы можете войти.';
    header('Location: /login.php');
    exit;
    
} catch(Exception $e) {
    $_SESSION['error'] = 'Ошибка при регистрации: ' . $e->getMessage();
    header('Location: /register.php');
    exit;
}
?>