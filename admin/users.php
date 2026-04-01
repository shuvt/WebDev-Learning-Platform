<?php
// admin/users.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$user    = getCurrentUser();
$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_role') {
        $user_id  = (int)$_POST['user_id'];
        $new_role = (int)$_POST['new_role'];
        if ($user_id === $user['id']) {
            $error = 'Вы не можете изменить свою собственную роль';
        } else {
            try {
                $db->prepare("UPDATE users SET role=? WHERE id=?")->execute([$new_role, $user_id]);
                $message = 'Роль пользователя успешно изменена!';
            } catch (Exception $e) {
                $error = 'Ошибка: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        if ($user_id === $user['id']) {
            $error = 'Вы не можете удалить свой собственный аккаунт';
        } else {
            try {
                $db->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);
                $message = 'Пользователь успешно удалён!';
            } catch (Exception $e) {
                $error = 'Ошибка: ' . $e->getMessage();
            }
        }
    }
}

$users = $db->query("
    SELECT u.*,
           COUNT(DISTINCT CASE WHEN p.is_completed = 1 THEN p.task_id END) as completed_tasks,
           (SELECT COUNT(*) FROM sql_tasks) as total_tasks
    FROM users u
    LEFT JOIN user_progress p ON u.id = p.user_id
    GROUP BY u.id
")->fetchAll();

// Сортировка: текущий пользователь первым, затем по роли (3→2→1), затем по дате
usort($users, function($a, $b) use ($user) {
    if ($a['id'] === $user['id']) return -1;
    if ($b['id'] === $user['id']) return  1;
    if ($b['role'] !== $a['role']) return $b['role'] - $a['role'];
    return strtotime($a['created_at']) - strtotime($b['created_at']);
});

$studentsCount = $teachersCount = $adminsCount = 0;
foreach ($users as $u) {
    if ($u['role'] == 1) $studentsCount++;
    if ($u['role'] == 2) $teachersCount++;
    if ($u['role'] == 3) $adminsCount++;
}

$roles = [1 => 'Студент', 2 => 'Учитель', 3 => 'Администратор'];

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

    <div class="stats-summary">
        <div class="stat-card"><h3><?= count($users) ?></h3><p>Всего пользователей</p></div>
        <div class="stat-card"><h3><?= $studentsCount ?></h3><p>Студентов</p></div>
        <div class="stat-card"><h3><?= $teachersCount ?></h3><p>Учителей</p></div>
        <div class="stat-card"><h3><?= $adminsCount ?></h3><p>Администраторов</p></div>
    </div>

    <div class="users-section">
        <h2>Список пользователей</h2>
        <div class="table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Имя пользователя</th><th>Email</th>
                        <th>Роль</th><th>Прогресс SQL</th><th>Дата регистрации</th><th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u):
                        $pct = $u['total_tasks'] > 0 ? round($u['completed_tasks'] / $u['total_tasks'] * 100) : 0;
                        $isMe = $u['id'] === $user['id'];
                    ?>
                    <tr class="<?= $isMe ? 'current-user' : '' ?>">
                        <td><?= $u['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($u['username']) ?></strong>
                            <?php if ($isMe): ?><span class="badge badge-you">Это вы</span><?php endif ?>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="role-badge role-<?= $u['role'] ?>"><?= $roles[$u['role']] ?></span></td>
                        <td>
                            <div class="progress-mini"><div class="progress-bar-mini" style="width:<?= $pct ?>%"></div></div>
                            <small><?= $u['completed_tasks'] ?>/<?= $u['total_tasks'] ?></small>
                        </td>
                        <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                        <td class="actions">
                            <?php if (!$isMe): ?>
                            <form method="POST" class="role-form">
                                <input type="hidden" name="action"   value="change_role">
                                <input type="hidden" name="user_id"  value="<?= $u['id'] ?>">
                                <select name="new_role" onchange="this.form.submit()">
                                    <?php foreach ($roles as $rid => $rname): ?>
                                    <option value="<?= $rid ?>" <?= $u['role'] == $rid ? 'selected' : '' ?>><?= $rname ?></option>
                                    <?php endforeach ?>
                                </select>
                            </form>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить пользователя?');">
                                <input type="hidden" name="action"  value="delete_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-small btn-delete">Удалить</button>
                            </form>
                            <?php else: ?>
                            <span class="no-action">—</span>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>