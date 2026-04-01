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

$stats           = getUserProgressStats($userId);
$progressByTopic = getProgressByTopic($userId);
$sqlPct = $stats['total_tasks'] > 0 ? round($stats['completed_tasks'] / $stats['total_tasks'] * 100) : 0;

$db->exec("CREATE TABLE IF NOT EXISTS course_topic_progress (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    course    VARCHAR(50)  NOT NULL,
    topic_key VARCHAR(100) NOT NULL,
    is_done   TINYINT(1)   DEFAULT 0,
    UNIQUE KEY user_course_topic (user_id, course, topic_key),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

$totals       = ['sql' => 9, 'php' => 6, 'html' => 4, 'web' => 10];
$courseLabels = ['sql' => 'SQL', 'php' => 'PHP', 'html' => 'HTML/CSS', 'web' => 'Веб-приложение'];

$stmt = $db->prepare("SELECT course, SUM(is_done) as done FROM course_topic_progress WHERE user_id=? GROUP BY course");
$stmt->execute([$userId]);
$done = ['sql' => 0, 'php' => 0, 'html' => 0, 'web' => 0];
foreach ($stmt->fetchAll() as $r) {
    if (isset($done[$r['course']])) $done[$r['course']] = (int)$r['done'];
}

$topicNames = [
    'simple'      => 'Простые запросы',
    'aggregation' => 'Агрегация данных',
    'joins'       => 'Соединение таблиц',
    'subqueries'  => 'Подзапросы',
];

$initials  = mb_strtoupper(mb_substr($user['username'], 0, 2));
$sqlDash   = round($sqlPct * 2.89);

$coursePct = [];
foreach ($totals as $key => $total) {
    $coursePct[$key] = $total > 0 ? round($done[$key] / $total * 100) : 0;
}

$totalTopics = array_sum($totals);
$doneTopics  = array_sum($done);
$coursesPct  = $totalTopics > 0 ? round($doneTopics / $totalTopics * 100) : 0;
$coursesDash = round($coursesPct * 2.89);

require_once __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="/templates/course.css">

<div class="db-wrap">
    <div class="db-card">
        <h1 class="db-h1">Личный кабинет</h1>

        <div class="db-user-section">
            <div class="db-avatar"><?= htmlspecialchars($initials) ?></div>
            <div class="db-user-info">
                <div class="db-username"><?= htmlspecialchars($user['username']) ?></div>
                <div class="db-meta-row"><span class="db-meta-key">Роль</span><span class="db-meta-val"><?= getRoleName($user['role']) ?></span></div>
                <div class="db-meta-row"><span class="db-meta-key">Почта</span><span class="db-meta-val"><?= htmlspecialchars($user['email']) ?></span></div>
                <div class="db-meta-row"><span class="db-meta-key">Дата регистрации</span><span class="db-meta-val"><?= date('d.m.Y', strtotime($user['created_at'])) ?></span></div>
            </div>
            <div class="db-user-btns">
                <a href="/courses.php"      class="db-btn db-btn--teal">К курсам</a>
                <a href="/sql-practice.php" class="db-btn db-btn--teal">Практика SQL</a>
                <?php if (isTeacher()): ?>
                <a href="/admin/tasks.php"  class="db-btn db-btn--purple">Управление заданиями</a>
                <?php endif ?>
                <?php if (isAdmin()): ?>
                <a href="/admin/users.php"  class="db-btn db-btn--purple">Управление пользователями</a>
                <?php endif ?>
                <a href="/logout.php"       class="db-btn db-btn--logout">Выйти</a>
            </div>
        </div>

        <div class="db-divider"></div>

        <div class="db-progress-row">
            <div class="db-progress-section">
                <div class="db-section-title">SQL-практика</div>
                <div class="db-circle-center">
                    <div class="db-circle-wrap">
                        <svg width="120" height="120" viewBox="0 0 120 120">
                            <defs>
                                <linearGradient id="sqlGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%"   stop-color="rgb(90,150,144)"/>
                                    <stop offset="100%" stop-color="rgb(110,86,160)"/>
                                </linearGradient>
                            </defs>
                            <circle cx="60" cy="60" r="46" fill="none" stroke="rgba(90,150,144,0.15)" stroke-width="11"/>
                            <circle cx="60" cy="60" r="46" fill="none" stroke="url(#sqlGrad)" stroke-width="11"
                                    stroke-linecap="round"
                                    stroke-dasharray="<?= $sqlDash ?> 289"
                                    transform="rotate(-90 60 60)"/>
                        </svg>
                        <div class="db-circle-label"><?= $sqlPct ?>%</div>
                    </div>
                    <div class="db-circle-sub"><?= $stats['completed_tasks'] ?> из <?= $stats['total_tasks'] ?> заданий</div>
                </div>
                <?php foreach ($progressByTopic as $tp):
                    $tPct = $tp['total_tasks'] > 0 ? round($tp['completed_tasks'] / $tp['total_tasks'] * 100) : 0;
                    $name = htmlspecialchars($topicNames[$tp['topic']] ?? $tp['topic']);
                ?>
                <div class="db-sub-row">
                    <span class="db-sub-name"><?= $name ?></span>
                    <span class="db-sub-count"><?= $tp['completed_tasks'] ?>/<?= $tp['total_tasks'] ?></span>
                </div>
                <div class="db-tbar"><div class="db-tbar-fill" style="width:<?= $tPct ?>%"></div></div>
                <?php endforeach ?>
            </div>

            <div class="db-progress-sep"></div>

            <div class="db-progress-section">
                <div class="db-section-title">Курсы</div>
                <div class="db-circle-center">
                    <div class="db-circle-wrap">
                        <svg width="120" height="120" viewBox="0 0 120 120">
                            <defs>
                                <linearGradient id="coursesGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%"   stop-color="rgb(90,150,144)"/>
                                    <stop offset="100%" stop-color="rgb(110,86,160)"/>
                                </linearGradient>
                            </defs>
                            <circle cx="60" cy="60" r="46" fill="none" stroke="rgba(90,150,144,0.15)" stroke-width="11"/>
                            <circle cx="60" cy="60" r="46" fill="none" stroke="url(#coursesGrad)" stroke-width="11"
                                    stroke-linecap="round"
                                    stroke-dasharray="<?= $coursesDash ?> 289"
                                    transform="rotate(-90 60 60)"/>
                        </svg>
                        <div class="db-circle-label"><?= $coursesPct ?>%</div>
                    </div>
                    <div class="db-circle-sub"><?= $doneTopics ?> из <?= $totalTopics ?> тем</div>
                </div>
                <?php foreach ($totals as $key => $total):
                    $d   = $done[$key];
                    $pct = $coursePct[$key];
                ?>
                <div class="db-sub-row">
                    <span class="db-sub-name"><?= $courseLabels[$key] ?></span>
                    <span class="db-sub-count"><?= $d ?>/<?= $total ?></span>
                </div>
                <div class="db-tbar"><div class="db-tbar-fill" style="width:<?= $pct ?>%"></div></div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>