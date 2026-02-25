<?php
// admin/users.php - Управление пользователями (только для админов)
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$user = getCurrentUser();
$message = '';
$error = '';

// Обработка изменения роли
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'change_role') {
        $user_id = (int)$_POST['user_id'];
        $new_role = (int)$_POST['new_role'];
        
        if ($user_id === $user['id']) {
            $error = 'Вы не можете изменить свою собственную роль';
        } else {
            try {
                $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->execute(array($new_role, $user_id));
                $message = 'Роль пользователя успешно изменена!';
            } catch (Exception $e) {
                $error = 'Ошибка при изменении роли: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        
        if ($user_id === $user['id']) {
            $error = 'Вы не можете удалить свой собственный аккаунт';
        } else {
            try {
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute(array($user_id));
                $message = 'Пользователь успешно удален!';
            } catch (Exception $e) {
                $error = 'Ошибка при удалении пользователя: ' . $e->getMessage();
            }
        }
    }
}

// Получаем всех пользователей с их статистикой
$users = $db->query("
    SELECT 
        u.*,
        COUNT(DISTINCT CASE WHEN p.is_completed = 1 THEN p.task_id END) as completed_tasks,
        (SELECT COUNT(*) FROM sql_tasks) as total_tasks
    FROM users u
    LEFT JOIN user_progress p ON u.id = p.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();

// Считаем статистику по ролям
$studentsCount = 0;
$teachersCount = 0;
$adminsCount = 0;
foreach ($users as $u) {
    if ($u['role'] == 1) $studentsCount++;
    if ($u['role'] == 2) $teachersCount++;
    if ($u['role'] == 3) $adminsCount++;
}

$roles = array(
    1 => 'Студент',
    2 => 'Учитель',
    3 => 'Администратор'
);

require_once __DIR__ . '/../templates/header.php';
?>

<div class="admin-container">
    <h1>Управление пользователями</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="stats-summary">
        <div class="stat-card">
            <h3><?php echo count($users); ?></h3>
            <p>Всего пользователей</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $studentsCount; ?></h3>
            <p>Студентов</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $teachersCount; ?></h3>
            <p>Учителей</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $adminsCount; ?></h3>
            <p>Администраторов</p>
        </div>
    </div>
    
    <div class="users-section">
        <h2>Список пользователей</h2>
        
        <div class="table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя пользователя</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Прогресс</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <?php 
                        $progressPercent = 0;
                        if ($u['total_tasks'] > 0) {
                            $progressPercent = round($u['completed_tasks'] / $u['total_tasks'] * 100);
                        }
                        ?>
                        <tr class="<?php echo $u['id'] === $user['id'] ? 'current-user' : ''; ?>">
                            <td><?php echo $u['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                                <?php if ($u['id'] === $user['id']): ?>
                                    <span class="badge badge-you">Это вы</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $u['role']; ?>"><?php echo $roles[$u['role']]; ?></span>
                            </td>
                            <td>
                                <div class="progress-mini">
                                    <div class="progress-bar-mini" style="width: <?php echo $progressPercent; ?>%"></div>
                                </div>
                                <small><?php echo $u['completed_tasks']; ?>/<?php echo $u['total_tasks']; ?></small>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($u['created_at'])); ?></td>
                            <td class="actions">
                                <?php if ($u['id'] !== $user['id']): ?>
                                    <form method="POST" class="role-form">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <select name="new_role" onchange="this.form.submit()">
                                            <?php foreach ($roles as $roleId => $roleName): ?>
                                                <option value="<?php echo $roleId; ?>" <?php echo $u['role'] == $roleId ? 'selected' : ''; ?>>
                                                    <?php echo $roleName; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                    
                                    <form method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Удалить пользователя?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-delete">Удалить</button>
                                    </form>
                                <?php else: ?>
                                    <span class="no-action">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

.stats-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin: 30px 0;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
    text-align: center;
}

.stat-card h3 {
    font-size: 2.5rem;
    color: rgb(47, 87, 85);
    margin-bottom: 5px;
}

.stat-card p {
    color: #666;
    margin: 0;
}

.users-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
}

.users-section h2 {
    color: rgb(47, 87, 85);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgb(90, 150, 144);
}

.table-container {
    width: 100%;
    overflow-x: auto;
}

.users-table {
    width: 100%;
    min-width: 800px;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid rgb(224, 217, 217);
}

.users-table th {
    background: rgba(90, 150, 144, 0.1);
    color: rgb(47, 87, 85);
    font-weight: 600;
}

.users-table tr:hover {
    background: rgba(90, 150, 144, 0.05);
}

.users-table tr.current-user {
    background: rgba(90, 150, 144, 0.1);
}

.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
    margin-left: 5px;
}

.badge-you {
    background: rgb(90, 150, 144);
    color: white;
}

.role-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
}

.role-1 {
    background: rgba(90, 150, 144, 0.2);
    color: rgb(47, 87, 85);
}

.role-2 {
    background: rgba(92, 107, 192, 0.2);
    color: #3f51b5;
}

.role-3 {
    background: rgba(126, 87, 194, 0.2);
    color: #673ab7;
}

.progress-mini {
    width: 60px;
    height: 8px;
    background: #eee;
    border-radius: 4px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
    margin-right: 5px;
}

.progress-bar-mini {
    height: 100%;
    background: rgb(90, 150, 144);
    border-radius: 4px;
}

.actions {
    white-space: nowrap;
}

.role-form {
    display: inline-block;
    margin-right: 10px;
}

.role-form select {
    padding: 5px 10px;
    border: 1px solid rgb(224, 217, 217);
    border-radius: 4px;
    font-size: 0.85rem;
}

.btn {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    background: rgb(224, 217, 217);
    color: rgb(67, 35, 35);
}

.btn-small {
    padding: 5px 10px;
    font-size: 12px;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.no-action {
    color: #999;
}

.back-link {
    margin-top: 30px;
}

@media (max-width: 768px) {
    .stats-summary {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
