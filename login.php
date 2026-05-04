<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if (is_post()) {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login_user($email, $password)) {
        redirect(APP_URL . '/index.php');
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<div class="card" style="max-width:420px;margin:40px auto;">
    <h2>Login</h2>
    <?php render_alerts('', $error); ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn primary" type="submit" style="margin-top:12px;">Login</button>
    </form>
    <p class="muted" style="margin-top:10px;">No account? <a href="register.php">Register</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

