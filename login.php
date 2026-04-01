<?php
// login.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

require_once __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="/templates/course.css">

<div class="admin-container" style="max-width: 400px; margin: 40px auto;">
    <div class="admin-card" style="text-align: center;">
        <div class="admin-card-title" style="font-size: 1.5rem; text-align: center;">Вход в платформу</div>
        
        <form action="login_process.php" method="POST" class="admin-form" style="text-align: left;">
            <div class="form-group">
                <label>Логин или Email:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-buttons" style="justify-content: center;">
                <button type="submit" class="btn btn-primary">Войти</button>
                <a href="/register.php" class="btn btn-edit">Зарегистрироваться</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>