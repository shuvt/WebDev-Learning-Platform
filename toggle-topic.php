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
    
    $userId    = (int)$_SESSION['user_id'];
    $course    = $_POST['course']    ?? '';
    $topicKey  = $_POST['topic_key'] ?? '';
    $nextTopic = $_POST['next_topic'] ?? null;
    $back      = $_POST['back']      ?? '/courses.php';

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
?>