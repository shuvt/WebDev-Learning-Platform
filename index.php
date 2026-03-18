<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/templates/header.php';
?>

<div class="hero">
    <h1>WebDev Самоучитель</h1>
    <p>Интерактивная платформа для обучения веб-разработке</p>
    
    <div style="margin-top: 20px;">
        <a href="/courses.php" class="btn">Начать обучение</a>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>