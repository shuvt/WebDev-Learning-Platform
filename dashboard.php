<?php
// dashboard.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$user = getCurrentUser();

require_once __DIR__ . '/templates/header.php';
?>

<div class="dashboard-container">
    <h1>Личный кабинет</h1>
    
    <div class="user-info">
        <h2>Добро пожаловать, <?= htmlspecialchars($user['username']) ?>!</h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Роль:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Дата регистрации:</strong> <?= $user['created_at'] ?></p>
    </div>
    
    <div class="dashboard-actions" style="margin-top: 30px;">
        <h3>Доступные действия:</h3>
        <div style="margin-top: 15px;">
            <a href="/courses.php" class="btn">Перейти к обучению</a>
            <a href="/logout.php" class="btn" style="background: rgb(67, 35, 35);">Выйти</a>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    max-width: 600px;
    margin: 0 auto;
}

.user-info {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    margin-top: 20px;
    border-left: 5px solid rgb(47, 87, 85);
}

.user-info h2 {
    color: rgb(47, 87, 85);
    margin-bottom: 15px;
}
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>