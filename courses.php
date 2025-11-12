<?php
// courses.php
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

<div class="courses-container">
    <h1>Курсы и обучение</h1>
    <p>Добро пожаловать в раздел обучения, <?= htmlspecialchars($user['username']) ?>!</p>
    
    <div class="courses-grid" style="margin-top: 30px;">
        <div class="course-card">
            <h3>SQL Basics</h3>
            <p>Основы работы с базами данных</p>
             <a href="/sql-course.php" class="btn" style="margin-top: 10px;">Начать обучение</a>
        </div>

        <div class="course-card">
            <h3>PHP Basics</h3>
            <p>Основы программирования на PHP</p>
            <a href="#" class="btn" style="margin-top: 10px;">Скоро доступно</a>
        </div>

        <div class="course-card">
            <h3>HTML & CSS Basics</h3>
            <p>Основы создания веб-страниц</p>
            <a href="#" class="btn" style="margin-top: 10px;">Скоро доступно</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>