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

<link rel="stylesheet" href="/templates/course.css">

<div class="admin-container" style="max-width: 400px; margin: 40px auto;">
    <div class="admin-card" style="text-align: center;">
        <div class="admin-card-title" style="font-size: 1.5rem; text-align: center;">Регистрация</div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error" style="text-align: left;"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form action="register_process.php" method="POST" class="admin-form" style="text-align: left;">
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-buttons" style="justify-content: center;">
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                <a href="/login.php" class="btn btn-edit">Войти</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>