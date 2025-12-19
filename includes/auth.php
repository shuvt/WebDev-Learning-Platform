<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function loginUser($user_id, $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
}

function logoutUser() {
    session_destroy();
    session_start();
}

// Проверка роли пользователя
// 1 - студент, 2 - учитель, 3 - админ
function isStudent() {
    $user = getCurrentUser();
    return $user && $user['role'] == 1;
}

function isTeacher() {
    $user = getCurrentUser();
    return $user && $user['role'] >= 2; // учитель или админ
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] == 3;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireTeacher() {
    requireLogin();
    if (!isTeacher()) {
        $_SESSION['error'] = 'Недостаточно прав для доступа к этой странице';
        header('Location: /dashboard.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = 'Недостаточно прав для доступа к этой странице';
        header('Location: /dashboard.php');
        exit;
    }
}

function getRoleName($role) {
    $roles = [
        1 => 'Студент',
        2 => 'Учитель',
        3 => 'Администратор'
    ];
    return $roles[$role] ?? 'Неизвестная роль';
}
?>
