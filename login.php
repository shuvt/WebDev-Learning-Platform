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

<div class="auth-container">
    <h2>Вход в платформу</h2>
    
    <form action="login_process.php" method="POST" class="auth-form">
        <div class="form-group">
            <label for="username">Логин или Email:</label>
            <input type="text" id="username" name="username" required 
                   placeholder="Введите ваш логин или email">
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required 
                   placeholder="Введите ваш пароль">
        </div>
        
        <button type="submit" class="btn">Войти</button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <p>Тестовый пользователь: <strong>admin</strong> / <strong>123456</strong></p>
        <p>Нет аккаунта? <a href="/register.php">Зарегистрируйтесь здесь</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>