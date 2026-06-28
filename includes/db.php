<?php
//require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASS);
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    if ($e->getCode() == 1049) {
        try {
            $temp_dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
            $temp_db = new PDO($temp_dsn, DB_USER, DB_PASS);
            
            $temp_db->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $db = new PDO($dsn, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e2) {
            die("Ошибка создания базы данных: " . $e2->getMessage());
        }
    } else {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

// Таблица пользователей
try {
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role INT DEFAULT 1 COMMENT '1-студент, 2-учитель, 3-админ',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO users (username, email, password, role) VALUES 
                  ('admin', 'admin@example.com', '$hashed_password', 3)");
        $db->exec("INSERT INTO users (username, email, password, role) VALUES 
                  ('teacher', 'teacher@example.com', '$hashed_password', 2)");
        $db->exec("INSERT INTO users (username, email, password, role) VALUES 
                  ('student', 'student@example.com', '$hashed_password', 1)");
    }
    
} catch(Exception $e) {
    echo "Ошибка создания таблицы users: " . $e->getMessage() . "<br>";
}

// Таблица заданий SQL
try {
    $db->exec("CREATE TABLE IF NOT EXISTS sql_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        topic VARCHAR(50) NOT NULL COMMENT 'simple, aggregation, joins, subqueries',
        task_number INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        solution_query TEXT NOT NULL COMMENT 'Эталонный SQL-запрос решения',
        expected_rows INT DEFAULT NULL COMMENT 'Ожидаемое количество строк',
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY topic_task (topic, task_number),
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    )");
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM sql_tasks");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $tasks = [
            ['simple', 1, 'Простые SQL-Запросы. Задание 1.', 
             'Напишите SQL запрос для вывода всех сотрудников из таблицы EMPLOYEE. Ожидается 104 записи в результате.',
             'SELECT * FROM EMPLOYEE', 104],
            ['simple', 2, 'Простые SQL-Запросы. Задание 2.', 
             'Напишите SQL запрос для вывода всех отделов и их местоположений из таблицы DEPARTMENT. Ожидается 22 записи в результате.',
             'SELECT DEPARTMENT, LOCATION FROM DEPARTMENT', 22],
            ['simple', 3, 'Простые SQL-Запросы. Задание 3.', 
             'Напишите SQL запрос для вывода сотрудников с зарплатой больше 100000. Ожидается 10 записей.',
             'SELECT * FROM EMPLOYEE WHERE SALARY > 100000', 10],
            ['simple', 4, 'Простые SQL-Запросы. Задание 4.', 
             'Напишите SQL запрос для вывода фамилий и дат приема на работу сотрудников, нанятых после 1 января 1993 года. Ожидается 76 записей.',
             "SELECT * FROM EMPLOYEE WHERE HIRE_DATE > '01.01.1993'", 76],
            ['simple', 5, 'Простые SQL-Запросы. Задание 5.', 
             'Напишите SQL запрос для вывода списка сотрудников в USA с зарплатой больше 10000 или сотрудников с номером отдела 120. Ожидается 36 записей.',
             "SELECT FIRST_NAME, LAST_NAME FROM EMPLOYEE WHERE (SALARY > 10000 AND JOB_COUNTRY = 'USA') OR (DEPT_NO = '120')", 36],
            ['aggregation', 1, 'Агрегация данных. Задание 1', 
             'Используя агрегатные функции найдите среднюю зарплату сотрудников из отдела 623. Ожидается 1 запись.',
             "SELECT AVG(SALARY) FROM EMPLOYEE WHERE DEPT_NO = '623'", 1],
            ['aggregation', 2, 'Агрегация данных. Задание 2', 
             'Найдите максимальное значение зарплаты среди всех сотрудников. Ожидается 1 запись.',
             'SELECT MAX(SALARY) FROM EMPLOYEE', 1],
            ['aggregation', 3, 'Агрегация данных. Задание 3', 
             'Сгруппируйте данные по отделам и найдите количество сотрудников для каждого из них. Ожидается 20 записей.',
             'SELECT DEPT_NO, COUNT(EMP_NO) FROM EMPLOYEE GROUP BY DEPT_NO', 20],
            ['aggregation', 4, 'Агрегация данных. Задание 4', 
             'Рассчитайте суммарный бюджет для каждого отдела. Ожидается 22 записи.',
             'SELECT DEPT_NO, SUM(BUDGET) FROM DEPARTMENT GROUP BY DEPT_NO', 22],
            ['aggregation', 5, 'Агрегация данных. Задание 5', 
             'Найдите минимальное и максимальное значение зарплаты в отделе 120. Ожидается два столбца: MIN_SALARY, MAX_SALARY и 1 запись в результате.',
             "SELECT MIN(SALARY) as min_salary, MAX(SALARY) as max_salary FROM EMPLOYEE WHERE DEPT_NO = '120'", 1],
            ['joins', 1, 'Соединение таблиц. Задание 1', 
             'Выведите полные имена сотрудников и названия их отделов. Ожидается два столбца: FULL_NAME, DEPARTMENT_NAME и 42 записи в результате.',
             "SELECT e.FIRST_NAME || ' ' || e.LAST_NAME as full_name, d.DEPARTMENT as department_name FROM EMPLOYEE e INNER JOIN DEPARTMENT d ON e.DEPT_NO = d.DEPT_NO", 42],
            ['joins', 2, 'Соединение таблиц. Задание 2', 
             'Для каждого заказа вывести страну сотрудника, принимавшего заказ. Используйте соединение таблиц SALES и EMPLOYEE. Ожидается два столбца: PO_NUMBER, EMPLOYEE_COUNTRY и 33 записи в результате.',
             'SELECT s.PO_NUMBER, e.JOB_COUNTRY as employee_country FROM SALES s INNER JOIN EMPLOYEE e ON s.SALES_REP = e.EMP_NO', 33],
            ['joins', 3, 'Соединение таблиц. Задание 3', 
             'Выведите полные имена сотрудников из отдела "Customer Support". Ожидается 1 столбец FULL_NAME и 5 записей в результате.',
             "SELECT e.FIRST_NAME || ' ' || e.LAST_NAME as full_name FROM EMPLOYEE e INNER JOIN DEPARTMENT d ON e.DEPT_NO = d.DEPT_NO WHERE d.DEPARTMENT = 'Customer Support'", 5],
            ['joins', 4, 'Соединение таблиц. Задание 4', 
             'Выведите названия всех проектов и полное имя лидера проекта, если оно есть. Для этого объедините таблицы PROJECT и EMPLOYEE. Ожидается два столбца: PROJECT_NAME, TEAM_LEADER_NAME и 16 записей в результате.',
             "SELECT p.PROJ_NAME as project_name, e.FIRST_NAME || ' ' || e.LAST_NAME as team_leader_name FROM PROJECT p LEFT JOIN EMPLOYEE e ON p.TEAM_LEADER = e.EMP_NO", 16],
            ['joins', 5, 'Соединение таблиц. Задание 5', 
             'Выведите имена сотрудников с бюджетом отдела больше 500000, которые были наняты после 01.01.1993. Ожидается 3 столбца: FULL_NAME, BUDGET, HIRE_DATE и 6 записей в результате.',
             "SELECT e.FIRST_NAME || ' ' || e.LAST_NAME as full_name, d.BUDGET, e.hire_date FROM EMPLOYEE e INNER JOIN DEPARTMENT d ON e.DEPT_NO = d.DEPT_NO WHERE d.BUDGET > 500000 AND e.HIRE_DATE > '1993-01-01'", 6],
            ['subqueries', 1, 'Подзапросы. Задание 1', 
             'Выведите полную информацию (*) о сотрудниках, у которых зарплата выше средней. Ожидается 3 записи.',
             'SELECT * FROM EMPLOYEE WHERE SALARY > (SELECT AVG(SALARY) FROM EMPLOYEE)', 3],
            ['subqueries', 2, 'Подзапросы. Задание 2', 
             'Выведите названия и расположения отделов, у которых бюджет меньше среднего. Ожидается 15 записей.',
             'SELECT DEPARTMENT, LOCATION FROM DEPARTMENT WHERE BUDGET < (SELECT AVG(BUDGET) FROM DEPARTMENT)', 15],
            ['subqueries', 3, 'Подзапросы. Задание 3', 
             'Для каждого проекта выведите названия отделов, принимавших участие в его выполнении в 1994 году. Для проектов, которые не выполнялись в 1994 году вместо названия отдела указать null. Используйте таблицы PROJ_DEPT_BUDGET, PROJECT, DEPARTMENT. Ожидается два столбца PROJECT_NAME, DEPARTMENT_NAME и 27 записей в результате.',
             'SELECT p.PROJ_NAME as project_name, d.DEPARTMENT as department_name FROM PROJECT p LEFT JOIN PROJ_DEPT_BUDGET pdb ON p.PROJ_ID = pdb.PROJ_ID AND pdb.FISCAL_YEAR = 1994 LEFT JOIN DEPARTMENT d ON pdb.DEPT_NO = d.DEPT_NO ORDER BY p.PROJ_NAME, d.DEPARTMENT', 27],
            ['subqueries', 4, 'Подзапросы. Задание 4', 
             'Выведите имена сотрудников, работающих в том же отделе, что и сотрудник с фамилией "Parker" (включительно). Ожидается столбец FULL_NAME 5 записей в результате.',
             "SELECT e1.FIRST_NAME || ' ' || e1.LAST_NAME as full_name FROM EMPLOYEE e1 WHERE e1.DEPT_NO = (SELECT e2.DEPT_NO FROM EMPLOYEE e2 WHERE e2.LAST_NAME = 'Parker')", 5],
            ['subqueries', 5, 'Подзапросы. Задание 5', 
             'Выведите названия проектов, проектируемый бюджет которых больше бюджета отдела "Marketing". Ожидается 2 записи в результате.',
             "SELECT p.PROJ_NAME FROM PROJECT p INNER JOIN PROJ_DEPT_BUDGET pdb ON p.PROJ_ID = pdb.PROJ_ID WHERE pdb.PROJECTED_BUDGET > (SELECT BUDGET FROM DEPARTMENT WHERE DEPARTMENT = 'Marketing')", 2],
        ];
        
        $stmt = $db->prepare("INSERT INTO sql_tasks (topic, task_number, title, description, solution_query, expected_rows) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($tasks as $task) {
            $stmt->execute($task);
        }
    }
    
} catch(Exception $e) {
    echo "Ошибка создания таблицы sql_tasks: " . $e->getMessage() . "<br>";
}

// Таблица прогресса пользователей
try {
    $db->exec("CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        task_id INT NOT NULL,
        is_completed TINYINT(1) DEFAULT 0,
        attempts INT DEFAULT 0,
        last_query TEXT,
        completed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY user_task (user_id, task_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (task_id) REFERENCES sql_tasks(id) ON DELETE CASCADE
    )");
    
} catch(Exception $e) {
    echo "Ошибка создания таблицы user_progress: " . $e->getMessage() . "<br>";
}

// Таблица прогресса по темам курса
try {
    $db->exec("CREATE TABLE IF NOT EXISTS course_topic_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course VARCHAR(50) NOT NULL,
        topic_key VARCHAR(100) NOT NULL,
        is_done TINYINT(1) DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY user_course_topic (user_id, course, topic_key),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
} catch(Exception $e) {
    echo "Ошибка создания таблицы course_topic_progress: " . $e->getMessage() . "<br>";
}

// Функции для работы с прогрессом
function getUserProgress($user_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT t.topic, t.task_number, p.is_completed, p.attempts, p.completed_at
        FROM sql_tasks t
        LEFT JOIN user_progress p ON t.id = p.task_id AND p.user_id = ?
        ORDER BY t.topic, t.task_number
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getUserProgressStats($user_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT 
            COUNT(DISTINCT t.id) as total_tasks,
            COUNT(DISTINCT CASE WHEN p.is_completed = 1 THEN t.id END) as completed_tasks,
            COALESCE(SUM(p.attempts), 0) as total_attempts
        FROM sql_tasks t
        LEFT JOIN user_progress p ON t.id = p.task_id AND p.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getProgressByTopic($user_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT 
            t.topic,
            COUNT(t.id) as total_tasks,
            COUNT(CASE WHEN p.is_completed = 1 THEN 1 END) as completed_tasks
        FROM sql_tasks t
        LEFT JOIN user_progress p ON t.id = p.task_id AND p.user_id = ?
        GROUP BY t.topic
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function markTaskCompleted($user_id, $task_id, $query) {
    global $db;
    $stmt = $db->prepare("
        INSERT INTO user_progress (user_id, task_id, is_completed, attempts, last_query, completed_at)
        VALUES (?, ?, 1, 1, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            is_completed = 1, 
            attempts = attempts + 1, 
            last_query = VALUES(last_query),
            completed_at = COALESCE(completed_at, NOW())
    ");
    $stmt->execute([$user_id, $task_id, $query]);
}

function incrementAttempt($user_id, $task_id, $query) {
    global $db;
    $stmt = $db->prepare("
        INSERT INTO user_progress (user_id, task_id, is_completed, attempts, last_query)
        VALUES (?, ?, 0, 1, ?)
        ON DUPLICATE KEY UPDATE 
            attempts = attempts + 1, 
            last_query = VALUES(last_query)
    ");
    $stmt->execute([$user_id, $task_id, $query]);
}

function getTaskByTopicAndNumber($topic, $task_number) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM sql_tasks WHERE topic = ? AND task_number = ?");
    $stmt->execute([$topic, $task_number]);
    return $stmt->fetch();
}

function getAllTasks() {
    global $db;
    return $db->query("SELECT * FROM sql_tasks ORDER BY topic, task_number")->fetchAll();
}

function isTaskCompleted($user_id, $topic, $task_number) {
    global $db;
    $stmt = $db->prepare("
        SELECT p.is_completed 
        FROM user_progress p
        JOIN sql_tasks t ON p.task_id = t.id
        WHERE p.user_id = ? AND t.topic = ? AND t.task_number = ?
    ");
    $stmt->execute([$user_id, $topic, $task_number]);
    $result = $stmt->fetch();
    return $result && $result['is_completed'] == 1;
}

// Функции для работы с содержимым курсов
function getCourseSection(string $course, string $key): ?string {
    global $db;
    $stmt = $db->prepare("SELECT content FROM course_sections WHERE course=? AND section_key=?");
    $stmt->execute([$course, $key]);
    $row = $stmt->fetch();
    return $row ? $row['content'] : null;
}

function getCourseSectionTitle(string $course, string $key): ?string {
    global $db;
    $stmt = $db->prepare("SELECT title FROM course_sections WHERE course=? AND section_key=?");
    $stmt->execute([$course, $key]);
    $row = $stmt->fetch();
    return ($row && $row['title'] !== null && $row['title'] !== '') ? $row['title'] : null;
}

function saveCourseSection(string $course, string $key, string $content, int $userId, ?string $title = null): void {
    global $db;
    if ($title !== null) {
        $db->prepare("INSERT INTO course_sections (course, section_key, title, content, updated_by)
                      VALUES (?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content), updated_by=VALUES(updated_by)")
           ->execute([$course, $key, $title, $content, $userId]);
    } else {
        $db->prepare("INSERT INTO course_sections (course, section_key, content, updated_by)
                      VALUES (?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE content=VALUES(content), updated_by=VALUES(updated_by)")
           ->execute([$course, $key, $content, $userId]);
    }
}

function getCustomSections(string $course): array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM course_sections WHERE course=? AND is_custom=1 ORDER BY id ASC");
    $stmt->execute([$course]);
    return $stmt->fetchAll();
}

function saveCustomSection(string $course, string $key, string $title, string $content, int $userId): void {
    global $db;
    $db->prepare("INSERT INTO course_sections (course, section_key, title, content, is_custom, updated_by)
                  VALUES (?, ?, ?, ?, 1, ?)
                  ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content), updated_by=VALUES(updated_by)")
       ->execute([$course, $key, $title, $content, $userId]);
}

function deleteCustomSection(string $course, string $key): void {
    global $db;
    $db->prepare("DELETE FROM course_sections WHERE course=? AND section_key=? AND is_custom=1")
       ->execute([$course, $key]);
}