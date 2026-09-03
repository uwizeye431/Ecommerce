<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
$message = '';
if (is_post()) {
    $userId = (int)$_POST['user_id'];
    if (isset($_POST['delete'])) {
        // Backup then delete
        $u = $pdo->prepare('SELECT * FROM Users WHERE UserID=?');
        $u->execute([$userId]);
        if ($row = $u->fetch()) {
            $pdo->prepare('INSERT INTO ArchivedUsers (UserID, Username, Email, Role, Status, ArchivedAt) VALUES (?, ?, ?, ?, ?, NOW())')
                ->execute([$row['UserID'], $row['Username'], $row['Email'], $row['Role'], $row['Status'] ?? 'active']);
        }
        $pdo->prepare('DELETE FROM Customers WHERE CustomerID=?')->execute([$userId]);
        $pdo->prepare('DELETE FROM Admins WHERE AdminID=?')->execute([$userId]);
        $pdo->prepare('DELETE FROM Users WHERE UserID=?')->execute([$userId]);
        $message = 'User deleted and backed up.';
    } elseif (isset($_POST['reset_password'])) {
        $new = 'User123';
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE Users SET Password=? WHERE UserID=?')->execute([$hash, $userId]);
        $message = "Password reset to: $new";
    } elseif (isset($_POST['toggle_status'])) {
        $status = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
        $pdo->prepare('UPDATE Users SET Status=? WHERE UserID=?')->execute([$status, $userId]);
        $message = "User status updated to $status.";
    } elseif (isset($_POST['save'])) {
        $role = sanitize($_POST['role']);
        $pdo->prepare('UPDATE Users SET Role=? WHERE UserID=?')->execute([$role, $userId]);
        $message = 'User updated.';
    }
}
$users = $pdo->query('SELECT * FROM Users ORDER BY UserID DESC')->fetchAll();
?>
<div class="card">
    <h2>Manage users</h2>
    <?php if ($message): ?><div class="alert success"><?php echo $message; ?></div><?php endif; ?>
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['UserID']; ?></td>
                <td><?php echo sanitize($u['Username']); ?></td>
                <td><?php echo sanitize($u['Email']); ?></td>
                <td><?php echo sanitize($u['Role']); ?></td>
                <td><?php echo sanitize($u['Status'] ?? 'active'); ?></td>
                <td><?php echo $u['LastLogin'] ?? '-'; ?></td>
                <td>
                    <form method="post" class="flex" style="gap:6px;">
                        <input type="hidden" name="user_id" value="<?php echo $u['UserID']; ?>">
                        <input type="hidden" name="current_status" value="<?php echo $u['Status'] ?? 'active'; ?>">
                        <select name="role">
                            <option value="customer" <?php if ($u['Role']==='customer') echo 'selected'; ?>>Customer</option>
                            <option value="admin" <?php if ($u['Role']==='admin') echo 'selected'; ?>>Admin</option>
                        </select>
                        <button class="btn secondary" type="submit" name="save">Save</button>
                        <button class="btn secondary" type="submit" name="toggle_status">
                            <?php echo ($u['Status'] ?? 'active') === 'active' ? 'Deactivate' : 'Activate'; ?>
                        </button>
                        <button class="btn secondary" type="submit" name="reset_password">Reset password</button>
                        <?php if ($u['UserID'] != current_user_id()): ?>
                            <button class="btn danger" type="submit" name="delete" onclick="return confirm('Delete this user?')">Delete</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>

