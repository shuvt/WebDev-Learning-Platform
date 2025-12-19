<?php
// admin/tasks.php - Управление заданиями для учителей
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireTeacher();

$user = getCurrentUser();
$message = '';
$error = '';

// Обработка добавления/редактирования задания
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $topic = trim($_POST['topic']);
        $task_number = (int)$_POST['task_number'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $solution_query = trim($_POST['solution_query']);
        $expected_rows = !empty($_POST['expected_rows']) ? (int)$_POST['expected_rows'] : null;
        
        if (empty($topic) || empty($title) || empty($description) || empty($solution_query)) {
            $error = 'Все поля обязательны для заполнения';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $db->prepare("
                        INSERT INTO sql_tasks (topic, task_number, title, description, solution_query, expected_rows, created_by)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$topic, $task_number, $title, $description, $solution_query, $expected_rows, $user['id']]);
                    $message = 'Задание успешно добавлено!';
                } else {
                    $task_id = (int)$_POST['task_id'];
                    $stmt = $db->prepare("
                        UPDATE sql_tasks 
                        SET topic = ?, task_number = ?, title = ?, description = ?, solution_query = ?, expected_rows = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$topic, $task_number, $title, $description, $solution_query, $expected_rows, $task_id]);
                    $message = 'Задание успешно обновлено!';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'Задание с таким номером в данной теме уже существует';
                } else {
                    $error = 'Ошибка базы данных: ' . $e->getMessage();
                }
            }
        }
    }
    
    if ($action === 'delete') {
        $task_id = (int)$_POST['task_id'];
        try {
            $stmt = $db->prepare("DELETE FROM sql_tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            $message = 'Задание успешно удалено!';
        } catch (Exception $e) {
            $error = 'Ошибка при удалении: ' . $e->getMessage();
        }
    }
}

// Получаем все задания
$tasks = getAllTasks();

// Группируем по темам
$tasksByTopic = [];
foreach ($tasks as $task) {
    $tasksByTopic[$task['topic']][] = $task;
}

$topicNames = [
    'simple' => 'Простые SQL-запросы',
    'aggregation' => 'Агрегация данных',
    'joins' => 'Соединение таблиц',
    'subqueries' => 'Подзапросы'
];

// Получаем задание для редактирования
$editTask = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM sql_tasks WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editTask = $stmt->fetch();
}

require_once __DIR__ . '/../templates/header.php';
?>

<div class="admin-container">
    <h1>Управление заданиями SQL</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="admin-layout">
        <!-- Форма добавления/редактирования -->
        <div class="task-form-section">
            <h2><?= $editTask ? 'Редактировать задание' : 'Добавить новое задание' ?></h2>
            
            <form method="POST" class="task-form">
                <input type="hidden" name="action" value="<?= $editTask ? 'edit' : 'add' ?>">
                <?php if ($editTask): ?>
                    <input type="hidden" name="task_id" value="<?= $editTask['id'] ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="topic">Тема:</label>
                        <select name="topic" id="topic" required>
                            <option value="">Выберите тему</option>
                            <?php foreach ($topicNames as $key => $name): ?>
                                <option value="<?= $key ?>" <?= ($editTask && $editTask['topic'] == $key) ? 'selected' : '' ?>>
                                    <?= $name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="task_number">Номер задания:</label>
                        <input type="number" name="task_number" id="task_number" min="1" required
                               value="<?= $editTask ? $editTask['task_number'] : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="title">Заголовок:</label>
                    <input type="text" name="title" id="title" required
                           value="<?= $editTask ? htmlspecialchars($editTask['title']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Описание задания:</label>
                    <textarea name="description" id="description" rows="3" required><?= $editTask ? htmlspecialchars($editTask['description']) : '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="solution_query">SQL-запрос решения (эталонный):</label>
                    <textarea name="solution_query" id="solution_query" rows="6" required 
                              placeholder="SELECT * FROM EMPLOYEE WHERE ..."><?= $editTask ? htmlspecialchars($editTask['solution_query']) : '' ?></textarea>
                    <small class="hint">Этот запрос будет использоваться для проверки ответов студентов</small>
                </div>
                
                <div class="form-group">
                    <label for="expected_rows">Ожидаемое количество строк (опционально):</label>
                    <input type="number" name="expected_rows" id="expected_rows" min="0"
                           value="<?= $editTask ? $editTask['expected_rows'] : '' ?>">
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        <?= $editTask ? 'Сохранить изменения' : 'Добавить задание' ?>
                    </button>
                    <?php if ($editTask): ?>
                        <a href="/admin/tasks.php" class="btn">Отмена</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Список заданий -->
        <div class="tasks-list-section">
            <h2>Существующие задания</h2>
            
            <?php foreach ($topicNames as $topicKey => $topicName): ?>
                <div class="topic-group">
                    <h3><?= $topicName ?></h3>
                    
                    <?php if (isset($tasksByTopic[$topicKey])): ?>
                        <table class="tasks-table">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>Заголовок</th>
                                    <th>Ожидаемые строки</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasksByTopic[$topicKey] as $task): ?>
                                    <tr>
                                        <td><?= $task['task_number'] ?></td>
                                        <td><?= htmlspecialchars($task['title']) ?></td>
                                        <td><?= $task['expected_rows'] ?? '-' ?></td>
                                        <td class="actions">
                                            <a href="?edit=<?= $task['id'] ?>" class="btn btn-small btn-edit">✎ Редактировать</a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Вы уверены, что хотите удалить это задание?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                <button type="submit" class="btn btn-small btn-delete">✕ Удалить</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-tasks">Заданий в этой теме пока нет</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="back-link">
        <a href="/dashboard.php" class="btn">← Вернуться в личный кабинет</a>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.admin-layout {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 30px;
    margin-top: 20px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    border-left: 5px solid;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: #155724;
    border-left-color: #28a745;
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: #721c24;
    border-left-color: #dc3545;
}

.task-form-section, .tasks-list-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
}

.task-form-section h2, .tasks-list-section h2 {
    color: rgb(47, 87, 85);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgb(90, 150, 144);
}

.task-form .form-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
}

.task-form .form-group {
    margin-bottom: 15px;
}

.task-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: rgb(67, 35, 35);
}

.task-form input,
.task-form select,
.task-form textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid rgb(224, 217, 217);
    border-radius: 6px;
    font-size: 14px;
}

.task-form textarea {
    font-family: 'Consolas', 'Monaco', monospace;
    resize: vertical;
}

.task-form input:focus,
.task-form select:focus,
.task-form textarea:focus {
    border-color: rgb(90, 150, 144);
    outline: none;
}

.hint {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.85rem;
}

.form-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    background: rgb(224, 217, 217);
    color: rgb(67, 35, 35);
}

.btn-primary {
    background: rgb(47, 87, 85);
    color: white;
}

.btn-primary:hover {
    background: rgb(90, 150, 144);
}

.topic-group {
    margin-bottom: 25px;
}

.topic-group h3 {
    color: rgb(47, 87, 85);
    margin-bottom: 10px;
    font-size: 1rem;
}

.tasks-table {
    width: 100%;
    border-collapse: collapse;
}

.tasks-table th,
.tasks-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid rgb(224, 217, 217);
}

.tasks-table th {
    background: rgba(90, 150, 144, 0.1);
    color: rgb(47, 87, 85);
    font-weight: 600;
}

.tasks-table .actions {
    white-space: nowrap;
}

.btn-small {
    padding: 5px 10px;
    font-size: 12px;
    margin-right: 5px;
}

.btn-edit {
    background: #5c6bc0;
    color: white;
}

.btn-edit:hover {
    background: #3f51b5;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.no-tasks {
    color: #666;
    font-style: italic;
    padding: 10px 0;
}

.back-link {
    margin-top: 30px;
}

@media (max-width: 1024px) {
    .admin-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
