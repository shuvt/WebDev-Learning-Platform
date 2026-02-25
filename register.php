<?php
// register.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

require_once __DIR__ . '/templates/header.php';
?>

<div class="auth-container">
    <h2>Регистрация в платформе</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form action="register_process.php" method="POST" class="auth-form">
        <div class="form-group">
            <label for="username">Логин:</label>
            <input type="text" id="username" name="username" required 
                   placeholder="Придумайте логин">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required 
                   placeholder="Ваш email">
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required 
                   placeholder="Не менее 6 символов">
        </div>
        
        <button type="submit" class="btn">Зарегистрироваться</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center;">
        Уже есть аккаунт? <a href="/login.php">Войдите здесь</a>
    </p>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
