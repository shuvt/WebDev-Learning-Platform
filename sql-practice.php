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

$allTasks     = getAllTasks();
$tasksByTopic = [];
foreach ($allTasks as $task) {
    $tasksByTopic[$task['topic']][] = $task;
}

$user_query      = '';
$query_result    = null;
$error_message   = '';
$success_message = '';
$is_correct      = null;
$current_topic   = $_GET['topic'] ?? 'database_info';
$current_task    = $_GET['task']  ?? '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sql_query'])) {
    $user_query    = trim($_POST['sql_query']);
    $current_topic = $_POST['topic']   ?? 'database_info';
    $current_task  = $_POST['task_id'] ?? '1';

    if (!empty($user_query)) {
        try {
            $query_result = executeFirebirdQuery($user_query);

            if ($current_topic != 'database_info') {
                $task = getTaskByTopicAndNumber($current_topic, $current_task);
                if ($task) {
                    try {
                        $solution_result = executeFirebirdQuery($task['solution_query']);
                        $is_correct      = compareResults($query_result, $solution_result);
                        if ($is_correct) {
                            $success_message = 'Задание выполнено правильно!';
                            markTaskCompleted($user['id'], $task['id'], $user_query);
                        } else {
                            $success_message = 'Запрос выполнен. Найдено записей: ' . count($query_result);
                            $error_message   = 'Результат не совпадает с ожидаемым. Попробуйте ещё раз!';
                            incrementAttempt($user['id'], $task['id'], $user_query);
                        }
                    } catch (Exception $e) {
                        $success_message = 'Запрос выполнен. Найдено записей: ' . count($query_result);
                    }
                } else {
                    $success_message = 'Запрос выполнен успешно! Найдено записей: ' . count($query_result);
                }
            } else {
                $success_message = 'Запрос выполнен успешно! Найдено записей: ' . count($query_result);
            }
        } catch (Exception $e) {
            $error_message = 'Ошибка в SQL-запросе: ' . $e->getMessage();
            if ($current_topic != 'database_info') {
                $task = getTaskByTopicAndNumber($current_topic, $current_task);
                if ($task) incrementAttempt($user['id'], $task['id'], $user_query);
            }
        }
    }
}

if (isset($_POST['view_table'])) {
    $table_name   = $_POST['view_table'];
    $user_query   = "SELECT * FROM {$table_name}";
    try {
        $query_result    = executeFirebirdQuery($user_query);
        $success_message = 'Таблица ' . $table_name . ' загружена. Записей: ' . count($query_result);
    } catch (Exception $e) {
        $error_message = 'Ошибка загрузки таблицы: ' . $e->getMessage();
    }
}

function compareResults($userResult, $solutionResult) {
    if (count($userResult) !== count($solutionResult)) return false;
    if (count($userResult) === 0) return true;
    $norm = function($row) {
        $n = [];
        foreach ($row as $k => $v) $n[strtoupper(trim($k))] = is_string($v) ? trim($v) : $v;
        return $n;
    };
    $uN = array_map($norm, $userResult);
    $sN = array_map($norm, $solutionResult);
    if (count(array_keys($uN[0])) !== count(array_keys($sN[0]))) return false;
    $sf = function($a, $b) { $k = array_key_first($a); return strcmp((string)$a[$k], (string)$b[$k]); };
    usort($uN, $sf); usort($sN, $sf);
    for ($i = 0; $i < count($uN); $i++) {
        $uV = array_values($uN[$i]); $sV = array_values($sN[$i]);
        for ($j = 0; $j < count($uV); $j++) {
            $u = (string)$uV[$j]; $s = (string)$sV[$j];
            if (is_numeric($u) && is_numeric($s)) { if (round((float)$u, 2) !== round((float)$s, 2)) return false; }
            elseif ($u !== $s) return false;
        }
    }
    return true;
}

$topicNames = [
    'simple'      => 'Простые SQL-запросы',
    'aggregation' => 'Агрегация данных',
    'joins'       => 'Соединение таблиц',
    'subqueries'  => 'Подзапросы',
];

require_once __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="/templates/course.css">

<div class="pr-wrap">

    <h1 class="pr-h1">Практические задания: SQL</h1>

    <div class="pr-layout">

        <!-- ── Боковая панель ─── -->
        <div class="pr-sidebar">

            <div class="pr-topic <?= $current_topic == 'database_info' ? 'pr-topic--open' : '' ?>">
                <div class="pr-topic-head" onclick="toggleTopic(this)">
                    <span>Структура базы данных</span>
                    <span class="pr-arrow">▾</span>
                </div>
                <div class="pr-topic-body">
                    <a class="pr-task-link <?= $current_topic == 'database_info' ? 'pr-task-link--active' : '' ?>"
                       href="?topic=database_info&task=1">
                        <span class="pr-dot pr-dot--plain"></span>
                        Таблицы Employee
                    </a>
                </div>
            </div>

            <?php foreach ($topicNames as $topicKey => $topicName):
                $isOpenTopic = $current_topic == $topicKey;
                $topicTotal  = isset($tasksByTopic[$topicKey]) ? count($tasksByTopic[$topicKey]) : 0;
                $topicDone   = 0;
                if (isset($tasksByTopic[$topicKey])) {
                    foreach ($tasksByTopic[$topicKey] as $t) {
                        if (isTaskCompleted($user['id'], $topicKey, $t['task_number'])) $topicDone++;
                    }
                }
            ?>
            <div class="pr-topic <?= $isOpenTopic ? 'pr-topic--open' : '' ?>">
                <div class="pr-topic-head" onclick="toggleTopic(this)">
                    <span><?= htmlspecialchars($topicName) ?></span>
                    <span class="pr-topic-count"><?= $topicDone ?>/<?= $topicTotal ?></span>
                    <span class="pr-arrow">▾</span>
                </div>
                <div class="pr-topic-body">
                    <?php if (isset($tasksByTopic[$topicKey])): ?>
                        <?php foreach ($tasksByTopic[$topicKey] as $task):
                            $done     = isTaskCompleted($user['id'], $topicKey, $task['task_number']);
                            $isActive = $current_topic == $topicKey && $current_task == $task['task_number'];
                        ?>
                        <a class="pr-task-link <?= $isActive ? 'pr-task-link--active' : '' ?>"
                           href="?topic=<?= $topicKey ?>&task=<?= $task['task_number'] ?>">
                            <span class="pr-dot <?= $done ? 'pr-dot--done' : 'pr-dot--plain' ?>">
                                <?= $done ? '✓' : $task['task_number'] ?>
                            </span>
                            Задание <?= $task['task_number'] ?>
                        </a>
                        <?php endforeach ?>
                    <?php else: ?>
                        <span class="pr-no-tasks">Заданий пока нет</span>
                    <?php endif ?>
                </div>
            </div>
            <?php endforeach ?>
        </div>

        <!-- ── Рабочая область ─── -->
        <div class="pr-workspace">

            <?php
            $taskInfo = null;
            if ($current_topic != 'database_info') {
                $taskInfo = getTaskByTopicAndNumber($current_topic, $current_task);
            }
            ?>

            <div class="pr-task-desc">
                <h2 class="pr-task-title">
                    <?= $taskInfo
                        ? htmlspecialchars($taskInfo['title'])
                        : ($current_topic == 'database_info' ? 'База данных Employee' : 'Задание') ?>
                </h2>
                <p class="pr-task-text">
                    <?= $taskInfo
                        ? htmlspecialchars($taskInfo['description'])
                        : ($current_topic == 'database_info'
                            ? 'Ознакомьтесь со структурой базы данных. Нажмите на название таблицы, чтобы просмотреть её содержимое.'
                            : '') ?>
                </p>

                <?php if ($current_topic == 'database_info'): ?>
                <div class="pr-tables">
                    <?php foreach (['COUNTRY','CUSTOMER','DEPARTMENT','EMPLOYEE','EMPLOYEE_PROJECT','JOB','PROJECT','PROJ_DEPT_BUDGET','SALARY_HISTORY','SALES'] as $tbl): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="topic"   value="<?= $current_topic ?>">
                        <input type="hidden" name="task_id" value="<?= $current_task ?>">
                        <button type="submit" name="view_table" value="<?= $tbl ?>" class="pr-table-btn"><?= $tbl ?></button>
                    </form>
                    <?php endforeach ?>
                </div>
                <?php endif ?>

                <?php if ($taskInfo && isTaskCompleted($user['id'], $current_topic, $current_task)): ?>
                <div class="pr-done-banner">✓ Задание уже выполнено</div>
                <?php endif ?>
            </div>

            <?php if ($current_topic != 'database_info'): ?>
            <form method="POST" class="pr-form">
                <input type="hidden" name="topic"   value="<?= $current_topic ?>">
                <input type="hidden" name="task_id" value="<?= $current_task ?>">

                <label class="pr-label" for="sql_query">Ваш SQL-запрос:</label>

                <div class="pr-editor-wrap">
                    <div class="pr-backdrop">
                        <div class="pr-highlights" id="sql-highlights"></div>
                    </div>
                    <textarea id="sql_query" name="sql_query" rows="7"
                              placeholder="Напишите здесь ваш SQL-запрос..."
                              spellcheck="false"><?= htmlspecialchars($user_query) ?></textarea>
                </div>

                <button type="submit" class="pr-run-btn">▶ Выполнить запрос</button>
            </form>
            <?php endif ?>

            <?php if ($success_message): ?>
            <div class="pr-msg <?= $is_correct === true ? 'pr-msg--ok' : 'pr-msg--info' ?>">
                <?= $is_correct === true ? '✓ ' : '' ?><?= htmlspecialchars($success_message) ?>
            </div>
            <?php endif ?>

            <?php if ($error_message): ?>
            <div class="pr-msg pr-msg--err"><?= htmlspecialchars($error_message) ?></div>
            <?php endif ?>

            <?php if ($query_result !== null): ?>
            <div class="pr-result">
                <div class="pr-result-head">
                    Результат — <?= count($query_result) ?> <?php
                        $n = count($query_result);
                        echo $n === 1 ? 'запись' : ($n < 5 ? 'записи' : 'записей');
                    ?>
                </div>
                <?php if (count($query_result) > 0): ?>
                <div class="pr-table-wrap">
                    <table class="pr-table">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($query_result[0]) as $col): ?>
                                <th><?= htmlspecialchars($col) ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($query_result as $row): ?>
                            <tr>
                                <?php foreach ($row as $val): ?>
                                <td><?= htmlspecialchars($val ?? 'NULL') ?></td>
                                <?php endforeach ?>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="pr-empty">Запрос выполнен, но не вернул данных.</p>
                <?php endif ?>
            </div>
            <?php endif ?>

        </div>
    </div>
</div>

<script>
function toggleTopic(header) {
    var topic  = header.parentElement;
    var isOpen = topic.classList.contains('pr-topic--open');
    document.querySelectorAll('.pr-topic').forEach(function(t) {
        t.classList.remove('pr-topic--open');
    });
    if (!isOpen) topic.classList.add('pr-topic--open');
}

var SQL_KEYWORDS  = ['SELECT','FROM','WHERE','AND','OR','NOT','IN','BETWEEN','LIKE','ORDER','BY','ASC','DESC','GROUP','HAVING','LIMIT','OFFSET','JOIN','INNER','LEFT','RIGHT','OUTER','FULL','CROSS','ON','INSERT','INTO','VALUES','UPDATE','SET','DELETE','TRUNCATE','CREATE','ALTER','DROP','TABLE','INDEX','VIEW','DATABASE','PRIMARY','KEY','FOREIGN','REFERENCES','UNIQUE','CHECK','DEFAULT','NULL','IS','AS','DISTINCT','ALL','ANY','EXISTS','UNION','INTERSECT','EXCEPT','CASE','WHEN','THEN','ELSE','END','FIRST','SKIP','ROWS','TO','FETCH','NEXT','ONLY','CAST','COALESCE','NULLIF','EXTRACT','FOR','WITH','RECURSIVE','SINGULAR'];
var SQL_TABLES    = ['EMPLOYEE','DEPARTMENT','COUNTRY','CUSTOMER','JOB','PROJECT','PROJ_DEPT_BUDGET','EMPLOYEE_PROJECT','SALARY_HISTORY','SALES'];
var SQL_FUNCTIONS = ['COUNT','SUM','AVG','MIN','MAX','ROUND','FLOOR','CEILING','ABS','MOD','YEAR','MONTH','DAY','HOUR','MINUTE','SECOND','CURRENT_DATE','CURRENT_TIME','CURRENT_TIMESTAMP','CHAR_LENGTH','CHARACTER_LENGTH'];

function highlightSQL(text) {
    if (!text) return '';
    var r = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    var strings = [], comments = [];
    r = r.replace(/'([^']*)'/g, function(m) { strings.push(m); return '__S'+(strings.length-1)+'__'; });
    r = r.replace(/--.*$/gm,   function(m) { comments.push(m); return '__C'+(comments.length-1)+'__'; });
    r = r.replace(/\/\*[\s\S]*?\*\//g, function(m) { comments.push(m); return '__C'+(comments.length-1)+'__'; });
    SQL_FUNCTIONS.forEach(function(f) { r = r.replace(new RegExp('\\b('+f+')\\s*\\(','gi'),'<span class="sql-function">$1</span>('); });
    SQL_KEYWORDS.forEach(function(k)  { r = r.replace(new RegExp('\\b('+k+')\\b','gi'),'<span class="sql-keyword">$1</span>'); });
    SQL_TABLES.forEach(function(t)    { r = r.replace(new RegExp('\\b('+t+')\\b','gi'),'<span class="sql-table">$1</span>'); });
    r = r.replace(/\b(\d+\.?\d*)\b/g,'<span class="sql-number">$1</span>');
    comments.forEach(function(c,i) { r = r.replace('__C'+i+'__','<span class="sql-comment">'+c+'</span>'); });
    strings.forEach(function(s,i)  { r = r.replace('__S'+i+'__','<span class="sql-string">'+s+'</span>'); });
    return r;
}

// Форматирование чисел в ячейках таблицы результатов:
// если значение — число с дробью (например 1.000000000), оставляем максимум 4 знака,
// убирая лишние нули в конце (1.0 -> 1, 1.25000 -> 1.25)
function formatCellValue(text) {
    if (text === '' || text === 'NULL') return text;
    // Проверяем, является ли значение числом с десятичной точкой
    if (/^-?\d+\.\d+$/.test(text.trim())) {
        var num = parseFloat(text);
        if (!isNaN(num)) {
            // toFixed(4) → убираем trailing zeros через parseFloat
            return String(parseFloat(num.toFixed(4)));
        }
    }
    return text;
}

document.addEventListener('DOMContentLoaded', function() {
    // Форматируем числа в таблице результатов
    document.querySelectorAll('.pr-table td').forEach(function(td) {
        td.textContent = formatCellValue(td.textContent);
    });

    var ta = document.getElementById('sql_query');
    var hl = document.getElementById('sql-highlights');
    function update() {
        if (!ta || !hl) return;
        hl.innerHTML  = highlightSQL(ta.value) + '\n';
        hl.scrollTop  = ta.scrollTop;
        hl.scrollLeft = ta.scrollLeft;
    }
    if (ta) {
        ta.addEventListener('input',  update);
        ta.addEventListener('scroll', function() { if(hl){ hl.scrollTop=this.scrollTop; hl.scrollLeft=this.scrollLeft; } });
        update();
    }
});
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>