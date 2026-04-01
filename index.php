<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/templates/header.php';

// Если пользователь уже авторизован, кнопка будет вести на courses.php
$startLink = isLoggedIn() ? '/courses.php' : '/login.php';
?>

<link rel="stylesheet" href="/templates/course.css">

<div class="lp-hero">
    <h1 class="lp-title">Обучающая среда<br>по созданию веб-приложений</h1>
    <p class="lp-sub">Теория, примеры кода и практические задания.</p>
    <div class="lp-btns">
        <a href="<?= $startLink ?>" class="lp-btn-main">Начать обучение</a>
    </div>
</div>

<div class="lp-preview">
    <div class="lp-preview-head">
        <span class="lp-preview-title">Курсы</span>
    </div>
    <div class="lp-preview-grid">
        <div class="lp-pc"><div class="lp-pc-num">1</div><div class="lp-pc-title">SQL и базы данных</div><a href="/sql-course.php" class="lp-pc-link">Перейти →</a></div>
        <div class="lp-pc"><div class="lp-pc-num">2</div><div class="lp-pc-title">Основы PHP</div><a href="/php-course.php" class="lp-pc-link">Перейти →</a></div>
        <div class="lp-pc"><div class="lp-pc-num">3</div><div class="lp-pc-title">HTML и CSS</div><a href="/html-css-course.php" class="lp-pc-link">Перейти →</a></div>
        <div class="lp-pc lp-pc--final"><div class="lp-pc-num lp-pc-num--final">4</div><div class="lp-pc-title">Веб-приложение</div><a href="/web-course.php" class="lp-pc-link">Перейти →</a></div>
    </div>
</div>

<style>
.lp-hero {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 14px;
    padding: 50px 40px 36px;
    text-align: center;
    margin-bottom: 20px;
}
.lp-badge {
    display: inline-block;
    background: rgba(90,150,144,.12);
    color: rgb(47,87,85);
    font-size: 12px;
    font-weight: 500;
    padding: 5px 14px;
    border-radius: 20px;
    margin-bottom: 16px;
}
.lp-title { font-size: 2rem; font-weight: 500; color: rgb(47,87,85); line-height: 1.3; margin-bottom: 12px; }
.lp-sub   { color: #555; font-size: 1rem; line-height: 1.7; max-width: 460px; margin: 0 auto 24px; }
.lp-btns  { display: flex; gap: 12px; justify-content: center; margin-bottom: 32px; }
.lp-btn-main  { background: rgb(47,87,85); color: #fff; padding: 11px 26px; border-radius: 9px; text-decoration: none; font-size: .95rem; font-weight: 500; }
.lp-btn-main:hover  { background: rgb(90,150,144); }

.lp-preview { background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; padding: 22px 26px; }
.lp-preview-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.lp-preview-title { font-size: 1rem; font-weight: 600; color: rgb(47,87,85); }
.lp-preview-sub   { font-size: .82rem; color: #888; }
.lp-preview-grid  { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }

.lp-pc { border: 1px solid #e8e8e8; border-radius: 10px; padding: 16px; }
.lp-pc:hover { border-color: rgb(90,150,144); }
.lp-pc--final { border-color: rgba(90,150,144,.45); }
.lp-pc-num { width: 32px; height: 32px; border-radius: 8px; background: rgba(90,150,144,.12); color: rgb(47,87,85); font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
.lp-pc-num--final { background: rgba(47,87,85,.12); }
.lp-pc-title { font-size: .88rem; font-weight: 600; color: rgb(47,87,85); margin-bottom: 5px; }
.lp-pc-desc  { font-size: .77rem; color: #666; line-height: 1.5; margin-bottom: 10px; }
.lp-pc-link  { font-size: .77rem; color: rgb(90,150,144); text-decoration: none; font-weight: 500; }
.lp-pc-link:hover { color: rgb(47,87,85); }
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>