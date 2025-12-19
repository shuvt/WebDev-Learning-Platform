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

// Получаем все задания из базы данных, сгруппированные по темам
$allTasks = getAllTasks();
$tasksByTopic = [];
foreach ($allTasks as $task) {
    $tasksByTopic[$task['topic']][] = $task;
}

// Обработка отправки SQL запроса
$user_query = '';
$query_result = null;
$solution_result = null;
$error_message = '';
$success_message = '';
$is_correct = null;
$current_topic = $_GET['topic'] ?? 'database_info';
$current_task = $_GET['task'] ?? '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sql_query'])) {
    $user_query = trim($_POST['sql_query']);
    $current_topic = $_POST['topic'] ?? 'database_info';
    $current_task = $_POST['task_id'] ?? '1';
    
    if (!empty($user_query)) {
        try {
            // Выполняем запрос пользователя
            $query_result = executeFirebirdQuery($user_query);
            
            // Если это не просмотр базы данных, проверяем решение
            if ($current_topic != 'database_info') {
                $task = getTaskByTopicAndNumber($current_topic, $current_task);
                
                if ($task) {
                    // Выполняем эталонный запрос
                    try {
                        $solution_result = executeFirebirdQuery($task['solution_query']);
                        
                        // Сравниваем результаты
                        $is_correct = compareResults($query_result, $solution_result);
                        
                        if ($is_correct) {
                            $success_message = " Задание выполнено правильно!";
                            markTaskCompleted($user['id'], $task['id'], $user_query);
                        } else {
                            $success_message = "Запрос выполнен. Найдено записей: " . count($query_result);
                            $error_message = " Результат не совпадает с ожидаемым. Попробуйте еще раз!";
                            incrementAttempt($user['id'], $task['id'], $user_query);
                        }
                    } catch (Exception $e) {
                        $success_message = "Запрос выполнен. Найдено записей: " . count($query_result);
                    }
                } else {
                    $success_message = "Запрос выполнен успешно! Найдено записей: " . count($query_result);
                }
            } else {
                $success_message = "Запрос выполнен успешно! Найдено записей: " . count($query_result);
            }
            
        } catch (Exception $e) {
            $error_message = "Ошибка в SQL запросе: " . $e->getMessage();
            
            // Записываем неудачную попытку
            if ($current_topic != 'database_info') {
                $task = getTaskByTopicAndNumber($current_topic, $current_task);
                if ($task) {
                    incrementAttempt($user['id'], $task['id'], $user_query);
                }
            }
        }
    }
}

// Обработка кнопок просмотра таблиц (без FIRST 50 - выводим все записи)
if (isset($_POST['view_table'])) {
    $table_name = $_POST['view_table'];
    $user_query = "SELECT * FROM {$table_name}";
    try {
        $query_result = executeFirebirdQuery($user_query);
        $success_message = "Таблица {$table_name} загружена! Записей: " . count($query_result);
    } catch (Exception $e) {
        $error_message = "Ошибка загрузки таблицы: " . $e->getMessage();
    }
}

// Функция сравнения результатов
function compareResults($userResult, $solutionResult) {
    // Проверяем количество строк
    if (count($userResult) !== count($solutionResult)) {
        return false;
    }
    
    // Если оба пустые - равны
    if (count($userResult) === 0) {
        return true;
    }
    
    // Нормализуем ключи (приводим к верхнему регистру и убираем пробелы)
    $normalizeRow = function($row) {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtoupper(trim($key));
            $normalizedValue = is_string($value) ? trim($value) : $value;
            $normalized[$normalizedKey] = $normalizedValue;
        }
        return $normalized;
    };
    
    // Нормализуем оба результата
    $userNormalized = array_map($normalizeRow, $userResult);
    $solutionNormalized = array_map($normalizeRow, $solutionResult);
    
    // Получаем ключи из первой строки
    $userKeys = array_keys($userNormalized[0]);
    $solutionKeys = array_keys($solutionNormalized[0]);
    
    // Проверяем количество столбцов
    if (count($userKeys) !== count($solutionKeys)) {
        return false;
    }
    
    // Сортируем по первому столбцу для сравнения
    $sortByFirstColumn = function($a, $b) {
        $keys = array_keys($a);
        $firstKey = reset($keys);
        return strcmp((string)$a[$firstKey], (string)$b[$firstKey]);
    };
    
    usort($userNormalized, $sortByFirstColumn);
    usort($solutionNormalized, $sortByFirstColumn);
    
    // Сравниваем значения
    for ($i = 0; $i < count($userNormalized); $i++) {
        $userValues = array_values($userNormalized[$i]);
        $solutionValues = array_values($solutionNormalized[$i]);
        
        for ($j = 0; $j < count($userValues); $j++) {
            // Приводим к строке и сравниваем
            $userVal = (string)$userValues[$j];
            $solutionVal = (string)$solutionValues[$j];
            
            // Округляем числа с плавающей точкой для сравнения
            if (is_numeric($userVal) && is_numeric($solutionVal)) {
                if (round((float)$userVal, 2) !== round((float)$solutionVal, 2)) {
                    return false;
                }
            } else if ($userVal !== $solutionVal) {
                return false;
            }
        }
    }
    
    return true;
}

// Названия тем для отображения
$topicNames = [
    'simple' => 'Простые SQL-запросы',
    'aggregation' => 'Агрегация данных',
    'joins' => 'Соединение таблиц',
    'subqueries' => 'Подзапросы'
];

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
                <div class="topic-item <?php echo $current_topic == 'database_info' ? 'active' : ''; ?>" data-topic="database_info">
                    <div class="topic-header">
                        <h3>Описание базы</h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="tasks-list" style="<?php echo $current_topic == 'database_info' ? 'display: block;' : 'display: none;'; ?>">
                        <div class="task-item <?php echo ($current_topic == 'database_info' && $current_task == '1') ? 'active' : ''; ?>" 
                             data-topic="database_info" data-task="1">
                            <span class="task-number"></span>
                            <div class="task-info">
                                <h4>Структура базы</h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Динамически генерируем темы и задания из базы данных -->
                <?php foreach ($topicNames as $topicKey => $topicName): ?>
                    <div class="topic-item <?php echo $current_topic == $topicKey ? 'active' : ''; ?>" data-topic="<?php echo $topicKey; ?>">
                        <div class="topic-header">
                            <h3><?php echo $topicName; ?></h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="tasks-list" style="<?php echo $current_topic == $topicKey ? 'display: block;' : 'display: none;'; ?>">
                            <?php if (isset($tasksByTopic[$topicKey])): ?>
                                <?php foreach ($tasksByTopic[$topicKey] as $task): ?>
                                    <?php $completed = isTaskCompleted($user['id'], $topicKey, $task['task_number']); ?>
                                    <div class="task-item <?php echo ($current_topic == $topicKey && $current_task == $task['task_number']) ? 'active' : ''; ?> <?php echo $completed ? 'completed' : ''; ?>" 
                                         data-topic="<?php echo $topicKey; ?>" data-task="<?php echo $task['task_number']; ?>">
                                        <span class="task-number"><?php echo $completed ? '✓' : $task['task_number']; ?></span>
                                        <div class="task-info">
                                            <h4>Задание <?php echo $task['task_number']; ?></h4>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-tasks-msg">Заданий пока нет</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Блок с редактором и результатом -->
        <div class="workspace">
            <?php 
            // Получаем информацию о задании из базы данных
            $taskInfo = null;
            if ($current_topic != 'database_info') {
                $taskInfo = getTaskByTopicAndNumber($current_topic, $current_task);
            }
            ?>
            <div class="task-description">
                <h3 id="current-task-title" style="margin-bottom: 10px;">
                    <?php echo $taskInfo ? htmlspecialchars($taskInfo['title']) : ($current_topic == 'database_info' ? 'Структура базы данных Employee' : 'Задание'); ?>
                </h3>
                <p id="current-task-desc" style="margin-bottom: 30px;">
                    <?php echo $taskInfo ? htmlspecialchars($taskInfo['description']) : ($current_topic == 'database_info' ? 'Ознакомьтесь со структурой базы данных Employee. Нажмите на название таблицы, чтобы просмотреть её содержимое.' : ''); ?>
                </p>
                
                <?php if ($current_topic == 'database_info'): ?>
                    <div class="database-info">                        
                        <div class="table-buttons">
                            <?php 
                            $tables = ['COUNTRY', 'CUSTOMER', 'DEPARTMENT', 'EMPLOYEE', 'EMPLOYEE_PROJECT', 'JOB', 'PROJECT', 'PROJ_DEPT_BUDGET', 'SALARY_HISTORY', 'SALES'];
                            foreach ($tables as $table): 
                            ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="topic" value="<?php echo $current_topic; ?>">
                                <input type="hidden" name="task_id" value="<?php echo $current_task; ?>">
                                <button type="submit" name="view_table" value="<?php echo $table; ?>" class="btn table-btn">
                                    <?php echo $table; ?>
                                </button>
                            </form>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Форма для SQL запроса - только для заданий, не для просмотра базы -->
            <?php if ($current_topic != 'database_info'): ?>
            <form method="POST" class="sql-form">
                <input type="hidden" name="topic" value="<?php echo $current_topic; ?>">
                <input type="hidden" name="task_id" id="task_id" value="<?php echo $current_task; ?>">
                
                <div class="form-group">
                    <label for="sql_query">Ваш SQL запрос:</label>
                    <div class="sql-editor-container">
                        <div class="sql-editor-backdrop">
                            <div class="sql-highlights" id="sql-highlights"></div>
                        </div>
                        <textarea id="sql_query" name="sql_query" rows="8" 
                                  placeholder="Напишите здесь ваш SQL запрос..."
                                  spellcheck="false"><?php echo htmlspecialchars($user_query); ?></textarea>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn run-btn">▶ Выполнить запрос</button>
                    <?php if (isTaskCompleted($user['id'], $current_topic, $current_task)): ?>
                        <span class="completed-badge">✓ Задание выполнено</span>
                    <?php endif; ?>
                </div>
            </form>
            <?php endif; ?>
            
            <!-- Результаты -->
            <?php if ($success_message): ?>
                <div class="message success <?php echo $is_correct === true ? 'correct' : ''; ?>"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
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
                                                <th><?php echo htmlspecialchars($column); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($query_result as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $value): ?>
                                                    <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="result-info">
                            Показано записей: <?php echo count($query_result); ?>
                        </div>
                    <?php else: ?>
                        <p>Запрос выполнен, но не вернул данных</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.practice-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 10px;
}

.practice-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 25px;
    margin-top: 20px;
}

.topics-sidebar {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.topics-sidebar h2 {
    color: rgb(47, 87, 85);
    margin-bottom: 20px;
    font-size: 1.2rem;
    border-bottom: 2px solid rgb(90, 150, 144);
    padding-bottom: 10px;
}

.topic-item {
    margin-bottom: 10px;
}

.topic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: rgba(90, 150, 144, 0.1);
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.topic-header:hover {
    background: rgba(90, 150, 144, 0.2);
}

.topic-header h3 {
    margin: 0;
    font-size: 0.95rem;
    color: rgb(47, 87, 85);
}

.toggle-icon {
    font-size: 0.8rem;
    color: rgb(47, 87, 85);
    transition: transform 0.3s;
}

.topic-item.active .toggle-icon {
    transform: rotate(180deg);
}

.tasks-list {
    padding: 10px 0 10px 10px;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}

.task-item:hover {
    background: rgba(90, 150, 144, 0.1);
}

.task-item.active {
    background: rgba(90, 150, 144, 0.2);
    border-left: 3px solid rgb(47, 87, 85);
}

.task-item.completed .task-number {
    background: rgb(40, 167, 69);
    color: white;
}

.task-number {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgb(224, 217, 217);
    border-radius: 50%;
    font-size: 0.85rem;
    font-weight: bold;
    color: rgb(67, 35, 35);
}

.task-item.active .task-number {
    background: rgb(90, 150, 144);
    color: white;
}

.task-info h4 {
    color: rgb(67, 35, 35);
    margin: 0;
    font-size: 0.9rem;
}

.no-tasks-msg {
    color: #999;
    font-style: italic;
    padding: 10px;
    font-size: 0.85rem;
}

.workspace {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    overflow: hidden;
}

.database-info {
    background: rgba(90, 150, 144, 0.1);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
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

/* SQL Editor с подсветкой синтаксиса - увеличенный шрифт и яркие цвета */
.sql-editor-container {
    position: relative;
    width: 100%;
}

.sql-editor-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
    border: 2px solid transparent;
    border-radius: 8px;
    padding: 15px;
}

.sql-highlights {
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 16px;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
    color: transparent;
}

.sql-form textarea {
    position: relative;
    width: 100%;
    padding: 15px;
    border: 2px solid rgb(224, 217, 217);
    border-radius: 8px;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 16px;
    line-height: 1.6;
    resize: vertical;
    transition: border 0.3s;
    background: transparent;
    color: #333;
    z-index: 1;
}

.sql-form textarea:focus {
    border-color: rgb(90, 150, 144);
    outline: none;
    box-shadow: 0 0 0 3px rgba(90, 150, 144, 0.1);
}

/* Подсветка синтаксиса - яркие цвета */
.sql-keyword {
    font-weight: bold;
    color: #000080;
}

.sql-table {
    color: #008000;
    text-decoration: underline;
    font-weight: 600;
}

.sql-string {
    color: #0066CC;
}

.sql-comment {
    color: #0000FF;
    font-style: italic;
}

.sql-function {
    color: #800080;
    font-weight: 600;
}

.sql-number {
    color: #CC3300;
}

.form-buttons {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 15px;
}

.run-btn {
    background: rgb(47, 87, 85);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
}

.run-btn:hover {
    background: rgb(90, 150, 144);
}

.completed-badge {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Результаты - фиксированная ширина с прокруткой */
.result-section {
    margin-top: 25px;
}

.result-section h4 {
    color: rgb(47, 87, 85);
    margin-bottom: 15px;
}

.result-table-container {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 500px;
    border: 1px solid rgb(224, 217, 217);
    border-radius: 8px;
    margin: 15px 0;
}

.result-table {
    min-width: 100%;
}

.result-table table {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    background: white;
}

.result-table th {
    background: rgb(47, 87, 85);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
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

.success.correct {
    background: rgba(40, 167, 69, 0.1);
    color: #155724;
    border-left-color: #28a745;
}

.error { 
    background: rgba(220, 53, 69, 0.1); 
    color: #721c24; 
    border-left-color: #dc3545;
}
</style>

<script>
// Списки для подсветки синтаксиса
// КЛЮЧЕВЫЕ СЛОВА SQL - жирный синий
var SQL_KEYWORDS = [
    'SELECT', 'FROM', 'WHERE', 'AND', 'OR', 'NOT', 'IN', 'BETWEEN', 'LIKE',
    'ORDER', 'BY', 'ASC', 'DESC', 'GROUP', 'HAVING', 'LIMIT', 'OFFSET',
    'JOIN', 'INNER', 'LEFT', 'RIGHT', 'OUTER', 'FULL', 'CROSS', 'ON',
    'INSERT', 'INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE', 'TRUNCATE',
    'CREATE', 'ALTER', 'DROP', 'TABLE', 'INDEX', 'VIEW', 'DATABASE',
    'PRIMARY', 'KEY', 'FOREIGN', 'REFERENCES', 'UNIQUE', 'CHECK', 'DEFAULT',
    'NULL', 'IS', 'AS', 'DISTINCT', 'ALL', 'ANY', 'EXISTS',
    'UNION', 'INTERSECT', 'EXCEPT', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END',
    'FIRST', 'SKIP', 'ROWS', 'TO', 'FETCH', 'NEXT', 'ONLY',
    'CAST', 'COALESCE', 'NULLIF', 'EXTRACT', 'POSITION', 'SUBSTRING',
    'UPPER', 'LOWER', 'TRIM', 'LTRIM', 'RTRIM', 'REPLACE', 'CONCAT',
    'FOR', 'WITH', 'RECURSIVE'
];

// НАЗВАНИЯ ТАБЛИЦ - зеленый подчеркнутый (добавляйте сюда свои таблицы)
var SQL_TABLES = [
    'EMPLOYEE', 'DEPARTMENT', 'COUNTRY', 'CUSTOMER', 'JOB', 
    'PROJECT', 'PROJ_DEPT_BUDGET', 'EMPLOYEE_PROJECT', 
    'SALARY_HISTORY', 'SALES'
];

// АГРЕГАТНЫЕ И ВСТРОЕННЫЕ ФУНКЦИИ - фиолетовый
var SQL_FUNCTIONS = [
    'COUNT', 'SUM', 'AVG', 'MIN', 'MAX', 
    'ROUND', 'FLOOR', 'CEILING', 'ABS', 'MOD',
    'YEAR', 'MONTH', 'DAY', 'HOUR', 'MINUTE', 'SECOND',
    'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP',
    'CHAR_LENGTH', 'CHARACTER_LENGTH', 'OCTET_LENGTH', 'BIT_LENGTH'
];

function highlightSQL(text) {
    if (!text) return '';
    
    var result = text;
    
    // Экранируем HTML
    result = result.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    
    // Сохраняем строки в кавычках (голубой)
    var strings = [];
    result = result.replace(/'([^']*)'/g, function(match, p1) {
        strings.push(match);
        return '__STRING_' + (strings.length - 1) + '__';
    });
    
    // Сохраняем комментарии (синий)
    var comments = [];
    // Однострочные комментарии --
    result = result.replace(/--.*$/gm, function(match) {
        comments.push(match);
        return '__COMMENT_' + (comments.length - 1) + '__';
    });
    // Многострочные комментарии /* */
    result = result.replace(/\/\*[\s\S]*?\*\//g, function(match) {
        comments.push(match);
        return '__COMMENT_' + (comments.length - 1) + '__';
    });
    
    // Подсвечиваем функции (фиолетовый)
    for (var f = 0; f < SQL_FUNCTIONS.length; f++) {
        var func = SQL_FUNCTIONS[f];
        var regex = new RegExp('\\b(' + func + ')\\s*\\(', 'gi');
        result = result.replace(regex, '<span class="sql-function">$1</span>(');
    }
    
    // Подсвечиваем ключевые слова (жирный синий)
    for (var k = 0; k < SQL_KEYWORDS.length; k++) {
        var keyword = SQL_KEYWORDS[k];
        var regex = new RegExp('\\b(' + keyword + ')\\b', 'gi');
        result = result.replace(regex, '<span class="sql-keyword">$1</span>');
    }
    
    // Подсвечиваем таблицы (зеленый подчеркнутый)
    for (var t = 0; t < SQL_TABLES.length; t++) {
        var table = SQL_TABLES[t];
        var regex = new RegExp('\\b(' + table + ')\\b', 'gi');
        result = result.replace(regex, '<span class="sql-table">$1</span>');
    }
    
    // Подсвечиваем числа (красно-оранжевый)
    result = result.replace(/\b(\d+\.?\d*)\b/g, '<span class="sql-number">$1</span>');
    
    // Восстанавливаем комментарии
    for (var i = 0; i < comments.length; i++) {
        result = result.replace('__COMMENT_' + i + '__', '<span class="sql-comment">' + comments[i] + '</span>');
    }
    
    // Восстанавливаем строки
    for (var i = 0; i < strings.length; i++) {
        result = result.replace('__STRING_' + i + '__', '<span class="sql-string">' + strings[i] + '</span>');
    }
    
    return result;
}

function updateHighlight() {
    var textarea = document.getElementById('sql_query');
    var highlights = document.getElementById('sql-highlights');
    
    if (textarea && highlights) {
        highlights.innerHTML = highlightSQL(textarea.value) + '\n';
        
        // Синхронизируем прокрутку
        highlights.scrollTop = textarea.scrollTop;
        highlights.scrollLeft = textarea.scrollLeft;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('sql_query');
    
    if (textarea) {
        // Обновляем подсветку при вводе
        textarea.addEventListener('input', updateHighlight);
        textarea.addEventListener('scroll', function() {
            var highlights = document.getElementById('sql-highlights');
            if (highlights) {
                highlights.scrollTop = this.scrollTop;
                highlights.scrollLeft = this.scrollLeft;
            }
        });
        
        // Начальная подсветка
        updateHighlight();
    }
    
    // Переключение тем
    var topicHeaders = document.querySelectorAll('.topic-header');
    
    for (var i = 0; i < topicHeaders.length; i++) {
        topicHeaders[i].addEventListener('click', function() {
            var topicItem = this.parentElement;
            var tasksList = topicItem.querySelector('.tasks-list');
            var isActive = topicItem.classList.contains('active');
            
            // Закрываем все темы
            var allTopics = document.querySelectorAll('.topic-item');
            for (var j = 0; j < allTopics.length; j++) {
                allTopics[j].classList.remove('active');
                allTopics[j].querySelector('.tasks-list').style.display = 'none';
            }
            
            // Открываем текущую тему если она была закрыта
            if (!isActive) {
                topicItem.classList.add('active');
                tasksList.style.display = 'block';
            }
        });
    }
    
    // Выбор задания
    var taskItems = document.querySelectorAll('.task-item');
    
    for (var i = 0; i < taskItems.length; i++) {
        taskItems[i].addEventListener('click', function() {
            var topic = this.getAttribute('data-topic');
            var task = this.getAttribute('data-task');
            
            var url = new URL(window.location);
            url.searchParams.set('topic', topic);
            url.searchParams.set('task', task);
            window.location.href = url.toString();
        });
    }
    
    // Автоматически открываем активную тему при загрузке
    var activeTopic = document.querySelector('.topic-item.active');
    if (activeTopic) {
        activeTopic.querySelector('.tasks-list').style.display = 'block';
    }
});
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
