<?php
// toggle-topic.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

function topicButton($course, $topicKey, $nextTopic = null) {
    if (!isLoggedIn()) return '';
    
    global $db;
    $userId = (int)$_SESSION['user_id'];
    
    $stmt = $db->prepare("SELECT is_done FROM course_topic_progress WHERE user_id=? AND course=? AND topic_key=?");
    $stmt->execute([$userId, $course, $topicKey]);
    $done = (bool)$stmt->fetchColumn();
    $anchor = $nextTopic ? $nextTopic : $topicKey;
    
    $courseFile = $course . '-course.php';
    if ($course === 'html') {
        $courseFile = 'html-css-course.php';
    }
    ?>
    <form method="post" action="/toggle-topic.php" class="topic-form">
        <input type="hidden" name="action" value="toggle">
        <input type="hidden" name="course" value="<?= $course ?>">
        <input type="hidden" name="topic_key" value="<?= $topicKey ?>">
        <input type="hidden" name="next_topic" value="<?= $nextTopic ?>">
        <input type="hidden" name="back" value="<?= "/{$courseFile}#{$anchor}" ?>">
        <button type="submit" class="topic-done-btn <?= $done ? 'done' : '' ?>">
            <?= $done ? '✓ Тема пройдена' : 'Отметить как пройденную' ?>
        </button>
    </form>
    <?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }

    $userId   = (int)$_SESSION['user_id'];
    $action   = $_POST['action'] ?? 'toggle';
    $course   = $_POST['course']    ?? '';
    $topicKey = $_POST['topic_key'] ?? '';
    $back     = $_POST['back']      ?? '/courses.php';


    // ===== ЗАГРУЗКА ИЗОБРАЖЕНИЙ =====
if ($action === 'upload_image') {
    header('Content-Type: application/json');

    if (!isTeacher()) {
        echo json_encode(['success' => false, 'message' => 'Недостаточно прав']);
        exit;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Файл не загружен']);
        exit;
    }

    $file = $_FILES['image'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Недопустимый тип файла']);
        exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid() . '.' . $ext;
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $path = $uploadDir . $newName;

    if (!move_uploaded_file($file['tmp_name'], $path)) {
        echo json_encode(['success' => false, 'message' => 'Ошибка сохранения файла']);
        exit;
    }

    echo json_encode(['success' => true, 'imageUrl' => '/uploads/' . $newName]);
    exit; // завершаем обработку
}


    // Сохранение содержимого существующей секции
    if ($action === 'save_section') {
        if (isTeacher() && $course && $topicKey && isset($_POST['content'])) {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            saveCourseSection($course, $topicKey, $_POST['content'], $userId, $title !== '' ? $title : null);
        }
        header('Location: ' . $back);
        exit;
    }

    // Добавление новой темы
    if ($action === 'add_section') {
        if (isTeacher() && $course && isset($_POST['content'], $_POST['title'])) {
            $title = trim($_POST['title']);
            if ($title !== '') {
                $key = 'custom_' . substr(md5($title . microtime()), 0, 8);
                saveCustomSection($course, $key, $title, $_POST['content'], $userId);
            }
        }
        header('Location: ' . $back);
        exit;
    }

    // Удаление пользовательской темы
    if ($action === 'delete_section') {
        if (isTeacher() && $course && $topicKey) {
            deleteCustomSection($course, $topicKey);
        }
        header('Location: ' . $back);
        exit;
    }

    // Переключение прогресса (по умолчанию)
    $nextTopic = $_POST['next_topic'] ?? null;

    if ($course && $topicKey) {
        $stmt = $db->prepare("SELECT is_done FROM course_topic_progress WHERE user_id=? AND course=? AND topic_key=?");
        $stmt->execute([$userId, $course, $topicKey]);
        $row = $stmt->fetch();

        $newState = $row ? ($row['is_done'] ? 0 : 1) : 1;

        $db->prepare("INSERT INTO course_topic_progress (user_id, course, topic_key, is_done)
                      VALUES (?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE is_done = ?")
           ->execute([$userId, $course, $topicKey, $newState, $newState]);
    }

    header('Location: ' . $back);
    exit;
}