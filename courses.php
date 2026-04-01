<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$user   = getCurrentUser();
$userId = (int)$_SESSION['user_id'];

// Создаём таблицу при первом обращении
$db->exec("CREATE TABLE IF NOT EXISTS course_topic_progress (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    course    VARCHAR(50)  NOT NULL,
    topic_key VARCHAR(100) NOT NULL,
    is_done   TINYINT(1)   DEFAULT 0,
    UNIQUE KEY user_course_topic (user_id, course, topic_key),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Количество тем в каждом курсе
$totals = ['sql' => 9, 'php' => 6, 'html' => 4, 'web' => 10];

// Сколько тем отмечено
$stmt = $db->prepare("SELECT course, SUM(is_done) as done FROM course_topic_progress WHERE user_id=? GROUP BY course");
$stmt->execute([$userId]);
$done = ['sql' => 0, 'php' => 0, 'html' => 0, 'web' => 0];
foreach ($stmt->fetchAll() as $r) {
    if (isset($done[$r['course']])) $done[$r['course']] = (int)$r['done'];
}

$courses = [
    ['key'=>'sql',  'num'=>1, 'title'=>'SQL и базы данных',        'sub'=>'Firebird SQL',    'desc'=>'Запросы SELECT, агрегация, JOIN, подзапросы, процедуры, триггеры и транзакции.', 'url'=>'/sql-course.php',      'final'=>false],
    ['key'=>'php',  'num'=>2, 'title'=>'Основы PHP',               'sub'=>'PHP 8',           'desc'=>'Синтаксис, переменные, функции, ООП, работа с файлами и базами данных через PDO.', 'url'=>'/php-course.php',      'final'=>false],
    ['key'=>'html', 'num'=>3, 'title'=>'HTML и CSS',               'sub'=>'HTML5 / CSS3',    'desc'=>'Структура страницы, теги, атрибуты, стили, работа с текстом и изображениями.', 'url'=>'/html-css-course.php', 'final'=>false],
    ['key'=>'web',  'num'=>4, 'title'=>'Создание веб-приложения',  'sub'=>'Финальный проект','desc'=>'Пошаговая разработка приложения «Библиотека» — БД, PHP-файлы, интерфейс.', 'url'=>'/web-course.php',      'final'=>true],
];

require_once __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="/templates/course.css">

<div class="cs-wrap">
    <div class="cs-head">
         <h1 class="cs-h1">Курсы и обучение</h1>
    </div>

    <div class="cs-grid">
        <?php foreach ($courses as $c):
            $d       = $done[$c['key']];
            $total   = $totals[$c['key']];
            $pct     = $total > 0 ? round($d / $total * 100) : 0;
            $finished = $d >= $total && $total > 0;
        ?>
        <div class="cs-card <?= $c['final'] ? 'cs-card--final' : '' ?> <?= $finished ? 'cs-card--done' : '' ?>">
            <div class="cs-card-top">
                <div class="cs-num <?= $c['final'] ? 'cs-num--final' : '' ?>"><?= $c['num'] ?></div>
                <div>
                    <div class="cs-card-title"><?= htmlspecialchars($c['title']) ?></div>
                </div>
                <?php if ($finished): ?>
                <div class="cs-done-badge">Завершён</div>
                <?php endif ?>
            </div>
            <p class="cs-card-desc"><?= htmlspecialchars($c['desc']) ?></p>
            <div class="cs-bar-wrap">
                <div class="cs-bar"><div class="cs-bar-fill <?= $c['final'] ? 'cs-bar-fill' : '' ?>" style="width:<?= $pct ?>%"></div></div>
                <div class="cs-bar-meta"><span><?= $d ?> / <?= $total ?> тем</span></div>
            </div>
            <a href="<?= $c['url'] ?>" class="cs-btn <?= $finished ? 'cs-btn--repeat' : ($d > 0 ? 'cs-btn--continue' : 'cs-btn--start') ?>">
                <?php if ($finished): ?>Повторить<?php elseif ($d > 0): ?>Продолжить<?php else: ?>Начать<?php endif ?>
            </a>
        </div>
        <?php endforeach ?>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>