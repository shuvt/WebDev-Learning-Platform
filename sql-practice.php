<?php
// sql-practice.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/firebird_db.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$user = getCurrentUser();

// Обработка отправки SQL запроса
$user_query = '';
$query_result = null;
$error_message = '';
$success_message = '';
$current_topic = $_GET['topic'] ?? 'database_info';
$current_task = $_GET['task'] ?? '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sql_query'])) {
    $user_query = trim($_POST['sql_query']);
    $current_topic = $_POST['topic'] ?? 'database_info';
    $current_task = $_POST['task_id'] ?? '1';
    
    if (!empty($user_query)) {
        try {
            $query_result = executeFirebirdQuery($user_query);
            $success_message = "Запрос выполнен успешно! Найдено записей: " . count($query_result);
            
        } catch (Exception $e) {
            $error_message = "Ошибка в SQL запросе: " . $e->getMessage();
        }
    }
}

// Обработка кнопок просмотра таблиц
if (isset($_POST['view_table'])) {
    $table_name = $_POST['view_table'];
    $user_query = "SELECT FIRST 50 * FROM {$table_name}";
    try {
        $query_result = executeFirebirdQuery($user_query);
        $success_message = "Таблица {$table_name} загружена! Найдено записей: " . count($query_result);
    } catch (Exception $e) {
        $error_message = "Ошибка загрузки таблицы: " . $e->getMessage();
    }
}

require_once __DIR__ . '/templates/header.php';
?>

<div class="practice-container">
    <h1>Практические задания: SQL Basics</h1>
    
    <div class="practice-layout">
        <!-- Блок с темами и заданиями -->
        <div class="topics-sidebar">
            <h2>Темы заданий</h2>
            
            <div class="topics-list">
                <!-- Описание базы данных -->
                <div class="topic-item <?= $current_topic == 'database_info' ? 'active' : '' ?>" data-topic="database_info">
                    <div class="topic-header">
                        <h3>Описание базы</h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="tasks-list" style="<?= $current_topic == 'database_info' ? 'display: block;' : 'display: none;' ?>">
                        <div class="task-item <?= ($current_topic == 'database_info' && $current_task == '1') ? 'active' : '' ?>" 
                             data-topic="database_info" data-task="1">
                            <span class="task-number"></span>
                            <div class="task-info">
                                <h4>Структура базы</h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Простые SQL-запросы -->
                <div class="topic-item <?= $current_topic == 'simple' ? 'active' : '' ?>" data-topic="simple">
                    <div class="topic-header">
                        <h3>Простые SQL-запросы</h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="tasks-list" style="<?= $current_topic == 'simple' ? 'display: block;' : 'display: none;' ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="task-item <?= ($current_topic == 'simple' && $current_task == $i) ? 'active' : '' ?>" 
                                 data-topic="simple" data-task="<?= $i ?>">
                                <span class="task-number"><?= $i ?></span>
                                <div class="task-info">
                                    <h4>Задание <?= $i ?></h4>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Запросы с агрегацией данных -->
                <div class="topic-item <?= $current_topic == 'aggregation' ? 'active' : '' ?>" data-topic="aggregation">
                    <div class="topic-header">
                        <h3>Агрегация данных</h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="tasks-list" style="<?= $current_topic == 'aggregation' ? 'display: block;' : 'display: none;' ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="task-item <?= ($current_topic == 'aggregation' && $current_task == $i) ? 'active' : '' ?>" 
                                 data-topic="aggregation" data-task="<?= $i ?>">
                                <span class="task-number"><?= $i ?></span>
                                <div class="task-info">
                                    <h4>Задание <?= $i ?></h4>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Соединение таблиц -->
                <div class="topic-item <?= $current_topic == 'joins' ? 'active' : '' ?>" data-topic="joins">
                    <div class="topic-header">
                        <h3>Соединение таблиц</h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="tasks-list" style="<?= $current_topic == 'joins' ? 'display: block;' : 'display: none;' ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="task-item <?= ($current_topic == 'joins' && $current_task == $i) ? 'active' : '' ?>" 
                                 data-topic="joins" data-task="<?= $i ?>">
                                <span class="task-number"><?= $i ?></span>
                                <div class="task-info">
                                    <h4>Задание <?= $i ?></h4>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Блок с редактором и результатом -->
        <div class="workspace">
            <div class="task-description">
                <h3 id="current-task-title" style="margin-bottom: 10px;"><?= getTaskTitle($current_topic, $current_task) ?></h3>
                <p id="current-task-desc" style="margin-bottom: 30px;"><?= getTaskDescription($current_topic, $current_task) ?></p>
                
                <?php if ($current_topic == 'database_info'): ?>
                    <div class="database-info">                        
                        <div class="table-buttons">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="COUNTRY" class="btn table-btn">
                                    COUNTRY
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="CUSTOMER" class="btn table-btn">
                                    CUSTOMER
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="DEPARTMENT" class="btn table-btn">
                                    DEPARTMENT
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="EMPLOYEE" class="btn table-btn">
                                    EMPLOYEE
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="EMPLOYEE_PROJECT" class="btn table-btn">
                                    EMPLOYEE_PROJECT
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="JOB" class="btn table-btn">
                                    JOB
                                </button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="PROJECT" class="btn table-btn">
                                    PROJECT
                                </button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="PROJ_DEPT_BUDGET" class="btn table-btn">
                                    PROJ_DEPT_BUDGET
                                </button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="SALARY_HISTORY" class="btn table-btn">
                                    SALARY_HISTORY
                                </button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                                <input type="hidden" name="task_id" value="<?= $current_task ?>">
                                <button type="submit" name="view_table" value="SALES" class="btn table-btn">
                                    SALES
                                </button>
                            </form>
                        </div>
                    
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Форма для SQL запроса (скрываем для просмотра таблиц) -->
            <?php if ($current_topic != 'database_info' || !isset($_POST['view_table'])): ?>
            <form method="POST" class="sql-form">
                <input type="hidden" name="topic" value="<?= $current_topic ?>">
                <input type="hidden" name="task_id" id="task_id" value="<?= $current_task ?>">
                
                <div class="form-group">
                    <label for="sql_query">Ваш SQL запрос:</label>
                    <textarea id="sql_query" name="sql_query" rows="6" placeholder="Напишите здесь ваш SQL запрос..."><?= htmlspecialchars($user_query) ?></textarea>
                </div>
                
                <button type="submit" class="btn run-btn">▶ Выполнить запрос</button>
            </form>
            <?php endif; ?>
            
            <!-- Результаты -->
            <?php if ($success_message): ?>
                <div class="message success"><?= $success_message ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message error"><?= $error_message ?></div>
            <?php endif; ?>
            
            <?php if ($query_result !== null): ?>
                <div class="result-section">
                    <h4>Результат выполнения:</h4>
                    <?php if (count($query_result) > 0): ?>
                        <div class="result-table-container">
                            <div class="result-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($query_result[0]) as $column): ?>
                                                <th><?= htmlspecialchars($column) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($query_result as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $value): ?>
                                                    <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="result-info">
                            Показано записей: <?= count($query_result) ?>
                        </div>
                    <?php else: ?>
                        <p>Запрос выполнен, но не вернул данных</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Функции для получения описаний заданий
function getTaskTitle($topic, $task) {
    $tasks = [
        'database_info' => [
            '1' => 'Структура базы данных Employee'
        ],
        'simple' => [
            '1' => 'Простые SQL-Запросы. Задание 1.',
            '2' => 'Простые SQL-Запросы. Задание 2.',
            '3' => 'Простые SQL-Запросы. Задание 3.',
            '4' => 'Простые SQL-Запросы. Задание 4.',
            '5' => 'Простые SQL-Запросы. Задание 5.'
        ],
        'aggregation' => [
            '1' => 'Агрегация данных. Задание 1',
            '2' => 'Агрегация данных. Задание 2',
            '3' => 'Агрегация данных. Задание 3',
            '4' => 'Агрегация данных. Задание 4',
            '5' => 'Агрегация данных. Задание 5'
        ],
        'joins' => [
            '1' => 'Соединение таблиц. Задание 1',
            '2' => 'Соединение таблиц. Задание 2',
            '3' => 'Соединение таблиц. Задание 3',
            '4' => 'Соединение таблиц. Задание 4',
            '5' => 'Соединение таблиц. Задание 5'
        ]
    ];
    
    return $tasks[$topic][$task] ?? 'Задание';
}

function getTaskDescription($topic, $task) {
    $descriptions = [
        'database_info' => [
            '1' => 'Ознакомьтесь со структурой базы данных Employee. Используйте кнопки ниже для просмотра содержимого таблиц.'
        ],
        'simple' => [
            '1' => 'Напишите SQL запрос для вывода всех сотрудников из таблицы EMPLOYEE. Ожидается 104 записи в результате.',
            '2' => 'Напишите SQL запрос для вывода всех отделов и их местоположений из таблицы DEPARTMENT. Ожидается 22 запись в результате.',
            '3' => 'Напишите SQL запрос для вывода сотрудников с зарплатой больше 100000. Ожидается 10 записей.',
            '4' => 'Напишите SQL запрос для вывода фамилий и дат приема на работу сотрудников, нанятых после 1 января 1993 года. Ожидается 76 записей.',
            '5' => 'Напишите SQL запрос для вывода списка сотрудников в USA с зарплатой больше 10000 или сотрудников с номером отдела 120. Ожидается 36 записей.'
        ],
        'aggregation' => [
            '1' => 'Используя агрегатные функции найдите среднюю зарплату всех сотрудников из отдела 623. Ожидается 1 запись.',
            '2' => 'Найдите максимальное значение зарплаты среди всех сотрудников. Ожидается 1 запись.',
            '3' => 'Сгруппируйте данные по отделам и найдите количество сотрудников для каждого из них. Ожидается 19 записей.',
            '4' => 'Рассчитайте суммарный бюджет для каждого отдела. Ожидается 21 запись.',
            '5' => 'Найдите минимальное и максимальное значение зарплаты в отделе 120. Ожидается 1 запись.'
        ],
        'joins' => [
            '1' => 'Выведите полные имена сотрудников и названия их отделов. Ожидается 42 записи.',
            '2' => 'Используйте соединение таблиц SALES и EMPLOYEE. Для каждого заказа вывести страну сотрудника, принимавшего заказ. Ожидается 33 записи.',
            '3' => 'Выведите имена сотрудников из отдела "Customer Support". Ожидается 5 записей.',
            '4' => 'Объедините таблицы PROJECT и EMPLOYEE. Выведите названия всех проектов и полное имя лидера проекта, если оно есть. Ожидается 6 записей.',
            '5' => 'Выведите имена сотрудников с бюджетом отдела больше 500000, которые были наняты после 01.01.1993. Ожидается 6 записей.'
        ]
    ];
    
    return $descriptions[$topic][$task] ?? 'Описание задания будет добавлено позже.';
}
?>

<style>
.practice-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 10px;
}

.practice-layout {
    display: grid;
    grid-template-columns: 290px minmax(0, 1fr);
    gap: 40px;
    margin-top: 20px;
}

.topics-sidebar {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
    max-height: 80vh;
    overflow-y: auto;
}

.topics-sidebar h2 {
    color: rgb(47, 87, 85);
    margin-bottom: 20px;
    font-size: 1.3rem;
}

.topics-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.topic-item {
    border: 2px solid rgb(224, 217, 217);
    border-radius: 8px;
    overflow: hidden;
}

.topic-item.active {
    border-color: rgb(90, 150, 144);
}

.topic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: rgba(90, 150, 144, 0.1);
    cursor: pointer;
    transition: background 0.3s;
}

.topic-header:hover {
    background: rgba(90, 150, 144, 0.2);
}

.topic-item.active .topic-header {
    background: rgba(90, 150, 144, 0.3);
}

.topic-header h3 {
    color: rgb(47, 87, 85);
    margin: 0;
    font-size: 1.1rem;
}

.toggle-icon {
    color: rgb(90, 150, 144);
    font-size: 0.8rem;
    transition: transform 0.3s;
}

.topic-item.active .toggle-icon {
    transform: rotate(180deg);
}

.tasks-list {
    background: white;
    display: none;
}

.task-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    cursor: pointer;
    transition: background 0.3s;
    border-top: 1px solid rgb(224, 217, 217);
}

.task-item:hover {
    background: rgba(90, 150, 144, 0.1);
}

.task-item.active {
    background: rgba(90, 150, 144, 0.2);
}

.task-number {
    background: rgb(47, 87, 85);
    color: white;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 10px;
    flex-shrink: 0;
    font-size: 0.8rem;
}

.task-item.active .task-number {
    background: rgb(90, 150, 144);
}

.task-info h4 {
    color: rgb(67, 35, 35);
    margin: 0;
    font-size: 0.9rem;
}

.workspace {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
}

.database-info {
    background: rgba(90, 150, 144, 0.1);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.database-info h4 {
    color: rgb(47, 87, 85);
    margin-bottom: 10px;
}

.table-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.table-btn {
    background: rgb(47, 87, 85);
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.3s;
}

.table-btn:hover {
    background: rgb(90, 150, 144);
}

/* Стили для таблицы с прокруткой */
.result-table-container {
    max-width: 100%;
    overflow-x: auto;
    border: 1px solid rgb(224, 217, 217);
    border-radius: 8px;
    margin: 15px 0;
}

.result-table {
    min-width: 100%;
}

.result-table table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.result-table th {
    background: rgb(47, 87, 85);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    position: sticky;
    left: 0;
    white-space: nowrap;
}

.result-table td {
    padding: 10px 12px;
    border-bottom: 1px solid rgb(224, 217, 217);
    white-space: nowrap;
}

.result-table tr:hover {
    background: rgba(90, 150, 144, 0.1);
}

.result-info {
    text-align: center;
    color: rgb(100, 100, 100);
    font-size: 0.9rem;
    margin-top: 10px;
}

.sql-form textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid rgb(224, 217, 217);
    border-radius: 8px;
    font-family: monospace;
    font-size: 14px;
    resize: vertical;
    transition: border 0.3s;
}

.sql-form textarea:focus {
    border-color: rgb(90, 150, 144);
    outline: none;
    box-shadow: 0 0 0 3px rgba(90, 150, 144, 0.1);
}

.run-btn {
    background: rgb(47, 87, 85);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 15px;
}

.run-btn:hover {
    background: rgb(90, 150, 144);
}

.result-section {
    margin-top: 25px;
}

.result-section h4 {
    color: rgb(47, 87, 85);
    margin-bottom: 15px;
}

.message {
    padding: 15px;
    margin: 15px 0;
    border-radius: 8px;
    border-left: 5px solid;
}

.success { 
    background: rgba(90, 150, 144, 0.1); 
    color: rgb(47, 87, 85); 
    border-left-color: rgb(90, 150, 144);
}

.error { 
    background: rgba(67, 35, 35, 0.1); 
    color: rgb(67, 35, 35); 
    border-left-color: rgb(67, 35, 35);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение тем
    const topicHeaders = document.querySelectorAll('.topic-header');
    
    topicHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const topicItem = this.parentElement;
            const tasksList = topicItem.querySelector('.tasks-list');
            const isActive = topicItem.classList.contains('active');
            
            // Закрываем все темы
            document.querySelectorAll('.topic-item').forEach(item => {
                item.classList.remove('active');
                item.querySelector('.tasks-list').style.display = 'none';
            });
            
            // Открываем текущую тему если она была закрыта
            if (!isActive) {
                topicItem.classList.add('active');
                tasksList.style.display = 'block';
            }
        });
    });
    
    // Выбор задания
    const taskItems = document.querySelectorAll('.task-item');
    
    taskItems.forEach(item => {
        item.addEventListener('click', function() {
            const topic = this.getAttribute('data-topic');
            const task = this.getAttribute('data-task');
            
            // Обновляем URL с перезагрузкой страницы для правильной работы
            const url = new URL(window.location);
            url.searchParams.set('topic', topic);
            url.searchParams.set('task', task);
            window.location.href = url.toString();
        });
    });
    
    // Автоматически открываем активную тему при загрузке
    const activeTopic = document.querySelector('.topic-item.active');
    if (activeTopic) {
        activeTopic.querySelector('.tasks-list').style.display = 'block';
    }
});
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>