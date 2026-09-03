<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth.php';

$message = '';
$error = '';
if (is_post()) {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $ok = register_user($username, $email, $password, 'customer');
        if ($ok) {
            $message = 'Account created. Please login.';
        } else {
            $error = 'Registration failed (email may exist).';
        }
    }
}
?>
<div class="card" style="max-width:420px;margin:40px auto;">
    <h2>Create account</h2>
    <?php if ($message): ?><div class="alert success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required minlength="6">
        <button class="btn primary" type="submit" style="margin-top:12px;">Register</button>
    </form>
    <p class="muted" style="margin-top:10px;">Have an account? <a href="login.php">Login</a></p>
</div>
</main>
</body>
</html>

