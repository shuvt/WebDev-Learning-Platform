<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/templates/header.php';
?>

<div class="hero">
    <h1>WebDev Самоучитель</h1>
    <p>Интерактивная платформа для обучения веб-разработке</p>
    
    <div style="margin-top: 20px;">
        <a href="/login.php" class="btn">Начать обучение</a>
    </div>
</div>

<div style="margin-top: 40px;">
    <h2>Возможности платформы:</h2>
    <ul style="margin-top: 15px; margin-left: 20px;">
        <li>Уроки по PHP, SQL, HTML+CSS</li>
        <li>Практические задания с автоматической проверкой</li>
        <li>Создание реальных проектов</li>
        <li>Отслеживание прогресса обучения</li>
    </ul>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>