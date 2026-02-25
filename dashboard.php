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

// Получаем статистику прогресса
$stats = getUserProgressStats($user['id']);
$progressByTopic = getProgressByTopic($user['id']);

// Названия топиков для отображения
$topicNames = array(
    'simple' => 'Простые SQL-запросы',
    'aggregation' => 'Агрегация данных',
    'joins' => 'Соединение таблиц',
    'subqueries' => 'Подзапросы'
);

// Вычисляем процент
$percentage = 0;
if ($stats['total_tasks'] > 0) {
    $percentage = round(($stats['completed_tasks'] / $stats['total_tasks']) * 100);
}

require_once __DIR__ . '/templates/header.php';
?>

<div class="dashboard-container">
    <h1>Личный кабинет</h1>
    
    <div class="user-info">
        <h2>Добро пожаловать, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Роль:</strong> <?php echo getRoleName($user['role']); ?></p>
        <p><strong>Дата регистрации:</strong> <?php echo $user['created_at']; ?></p>
    </div>
    
    <!-- Блок прогресса -->
    <div class="progress-section">
        <h3>Мой прогресс по SQL</h3>
        
        <div class="overall-progress">
            <div class="progress-circle">
                <svg viewBox="0 0 36 36" class="circular-chart">
                    <path class="circle-bg"
                        d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <path class="circle"
                        stroke-dasharray="<?php echo $percentage; ?>, 100"
                        d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <text x="18" y="20.35" class="percentage"><?php echo $percentage; ?>%</text>
                </svg>
            </div>
            <div class="progress-stats">
                <p><strong>Выполнено заданий:</strong> <?php echo $stats['completed_tasks']; ?> из <?php echo $stats['total_tasks']; ?></p>
                <p><strong>Всего попыток:</strong> <?php echo $stats['total_attempts']; ?></p>
            </div>
        </div>
        
        <div class="topic-progress">
            <h4>Прогресс по темам:</h4>
            <?php foreach ($progressByTopic as $topic): ?>
                <?php 
                $topicPercentage = 0;
                if ($topic['total_tasks'] > 0) {
                    $topicPercentage = round(($topic['completed_tasks'] / $topic['total_tasks']) * 100);
                }
                $topicDisplayName = isset($topicNames[$topic['topic']]) ? $topicNames[$topic['topic']] : $topic['topic'];
                ?>
                <div class="topic-bar">
                    <div class="topic-name"><?php echo $topicDisplayName; ?></div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo $topicPercentage; ?>%"></div>
                    </div>
                    <div class="topic-count"><?php echo $topic['completed_tasks']; ?>/<?php echo $topic['total_tasks']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="dashboard-actions">
        <h3>Доступные действия:</h3>
        <div class="action-buttons">
            <a href="/courses.php" class="btn">Перейти к обучению</a>
            <a href="/sql-practice.php" class="btn btn-primary">Практические задания</a>
            <?php if (isTeacher()): ?>
                <a href="/admin/tasks.php" class="btn btn-teacher">Управление заданиями</a>
            <?php endif; ?>
            <?php if (isAdmin()): ?>
                <a href="/admin/users.php" class="btn btn-admin">Управление пользователями</a>
            <?php endif; ?>
            <a href="/logout.php" class="btn btn-logout">Выйти</a>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    max-width: 900px;
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

/* Стили для блока прогресса */
.progress-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    margin-top: 20px;
}

.progress-section h3 {
    color: rgb(47, 87, 85);
    margin-bottom: 25px;
    border-bottom: 2px solid rgb(90, 150, 144);
    padding-bottom: 10px;
}

.overall-progress {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-bottom: 30px;
}

.progress-circle {
    width: 120px;
    height: 120px;
}

.circular-chart {
    display: block;
    max-width: 100%;
}

.circle-bg {
    fill: none;
    stroke: #eee;
    stroke-width: 3.8;
}

.circle {
    fill: none;
    stroke: rgb(90, 150, 144);
    stroke-width: 3.8;
    stroke-linecap: round;
    animation: progress 1s ease-out forwards;
}

@keyframes progress {
    0% {
        stroke-dasharray: 0 100;
    }
}

.percentage {
    fill: rgb(47, 87, 85);
    font-size: 0.5em;
    text-anchor: middle;
    font-weight: bold;
}

.progress-stats p {
    margin: 8px 0;
    color: rgb(67, 35, 35);
}

.topic-progress h4 {
    color: rgb(47, 87, 85);
    margin-bottom: 15px;
}

.topic-bar {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    gap: 15px;
}

.topic-name {
    width: 180px;
    font-size: 0.9rem;
    color: rgb(67, 35, 35);
}

.progress-bar-container {
    flex: 1;
    height: 20px;
    background: #eee;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, rgb(47, 87, 85), rgb(90, 150, 144));
    border-radius: 10px;
    transition: width 0.5s ease;
}

.topic-count {
    width: 50px;
    text-align: right;
    font-size: 0.9rem;
    color: rgb(100, 100, 100);
}

/* Стили для кнопок действий */
.dashboard-actions {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    margin-top: 20px;
}

.dashboard-actions h3 {
    color: rgb(47, 87, 85);
    margin-bottom: 20px;
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    background: rgb(47, 87, 85);
    color: white;
}

.btn:hover {
    background: rgb(90, 150, 144);
}

.btn-primary {
    background: rgb(90, 150, 144);
}

.btn-primary:hover {
    background: rgb(47, 87, 85);
}

.btn-teacher {
    background: #5c6bc0;
}

.btn-teacher:hover {
    background: #3f51b5;
}

.btn-admin {
    background: #7e57c2;
}

.btn-admin:hover {
    background: #673ab7;
}

.btn-logout {
    background: rgb(67, 35, 35);
}

.btn-logout:hover {
    background: #8b4545;
}
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
