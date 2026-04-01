<?php
// admin/tasks.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireTeacher();

$user = getCurrentUser();
$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $topic          = trim($_POST['topic']);
        $task_number    = (int)$_POST['task_number'];
        $title          = trim($_POST['title']);
        $description    = trim($_POST['description']);
        $solution_query = trim($_POST['solution_query']);
        $expected_rows  = !empty($_POST['expected_rows']) ? (int)$_POST['expected_rows'] : null;

        if (empty($topic) || empty($title) || empty($description) || empty($solution_query)) {
            $error = 'Все поля обязательны для заполнения';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $db->prepare("INSERT INTO sql_tasks (topic, task_number, title, description, solution_query, expected_rows, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$topic, $task_number, $title, $description, $solution_query, $expected_rows, $user['id']]);
                    $message = 'Задание успешно добавлено!';
                } else {
                    $task_id = (int)$_POST['task_id'];
                    $stmt = $db->prepare("UPDATE sql_tasks SET topic=?, task_number=?, title=?, description=?, solution_query=?, expected_rows=? WHERE id=?");
                    $stmt->execute([$topic, $task_number, $title, $description, $solution_query, $expected_rows, $task_id]);
                    $message = 'Задание успешно обновлено!';
                }
            } catch (PDOException $e) {
                $error = $e->getCode() == 23000
                    ? 'Задание с таким номером в данной теме уже существует'
                    : 'Ошибка базы данных: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $task_id = (int)$_POST['task_id'];
        try {
            $db->prepare("DELETE FROM sql_tasks WHERE id=?")->execute([$task_id]);
            $message = 'Задание успешно удалено!';
        } catch (Exception $e) {
            $error = 'Ошибка при удалении: ' . $e->getMessage();
        }
    }
}

$tasks = getAllTasks();
$tasksByTopic = [];
foreach ($tasks as $task) $tasksByTopic[$task['topic']][] = $task;

$topicNames = [
    'simple'      => 'Простые SQL-запросы',
    'aggregation' => 'Агрегация данных',
    'joins'       => 'Соединение таблиц',
    'subqueries'  => 'Подзапросы',
];

$editTask = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM sql_tasks WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editTask = $stmt->fetch();
}

require_once __DIR__ . '/../templates/header.php';
?>

<link rel="stylesheet" href="/templates/course.css">

<div class="admin-container">

    <a href="/dashboard.php" class="btn">← Вернуться в личный кабинет</a>

    <?php if ($message): ?>
    <div class="alert alert-success" style="margin-top:16px"><?= htmlspecialchars($message) ?></div>
    <?php endif ?>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-top:16px"><?= htmlspecialchars($error) ?></div>
    <?php endif ?>

    <div class="admin-layout">

        <!-- Форма -->
        <div class="admin-card">
            <div class="admin-card-title"><?= $editTask ? 'Редактировать задание' : 'Добавить новое задание' ?></div>

            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="<?= $editTask ? 'edit' : 'add' ?>">
                <?php if ($editTask): ?>
                <input type="hidden" name="task_id" value="<?= $editTask['id'] ?>">
                <?php endif ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Тема:</label>
                        <select name="topic" required>
                            <option value="">Выберите тему</option>
                            <?php foreach ($topicNames as $key => $name): ?>
                            <option value="<?= $key ?>" <?= ($editTask && $editTask['topic'] == $key) ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Номер задания:</label>
                        <input type="number" name="task_number" min="1" required value="<?= $editTask ? $editTask['task_number'] : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Заголовок:</label>
                    <input type="text" name="title" required value="<?= $editTask ? htmlspecialchars($editTask['title']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Описание задания:</label>
                    <textarea name="description" rows="3" required><?= $editTask ? htmlspecialchars($editTask['description']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label>SQL-запрос решения (эталонный):</label>
                    <textarea name="solution_query" rows="6" required placeholder="SELECT * FROM EMPLOYEE WHERE ..."><?= $editTask ? htmlspecialchars($editTask['solution_query']) : '' ?></textarea>
                    <span class="hint">Этот запрос будет использоваться для проверки ответов студентов</span>
                </div>

                <div class="form-group">
                    <label>Ожидаемое количество строк (опционально):</label>
                    <input type="number" name="expected_rows" min="0" value="<?= $editTask ? $editTask['expected_rows'] : '' ?>">
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary"><?= $editTask ? 'Сохранить изменения' : 'Добавить задание' ?></button>
                    <?php if ($editTask): ?>
                    <a href="/admin/tasks.php" class="btn">Отмена</a>
                    <?php endif ?>
                </div>
            </form>
        </div>

        <!-- Список заданий -->
        <div class="admin-card">
            <div class="admin-card-title">Существующие задания</div>

            <?php foreach ($topicNames as $topicKey => $topicName): ?>
            <div class="topic-group">
                <h3><?= $topicName ?></h3>
                <?php if (isset($tasksByTopic[$topicKey])): ?>
                <table class="admin-table">
                    <thead>
                        <tr><th>№</th><th>Заголовок</th><th>Строк</th><th>Действия</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasksByTopic[$topicKey] as $task): ?>
                        <tr>
                            <td><?= $task['task_number'] ?></td>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= $task['expected_rows'] ?? '—' ?></td>
                            <td class="actions">
                                <a href="?edit=<?= $task['id'] ?>" class="btn btn-small btn-edit">✎ Редактировать</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить задание?');">
                                    <input type="hidden" name="action"  value="delete">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <button type="submit" class="btn btn-small btn-delete">✕ Удалить</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="no-tasks">Заданий пока нет</p>
                <?php endif ?>
            </div>
            <?php endforeach ?>
        </div>

    </div>
</div>

<style>
.admin-layout {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 24px;
    margin-top: 18px;
}
@media (max-width: 1024px) { .admin-layout { grid-template-columns: 1fr; } }
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>