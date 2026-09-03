<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getPDO();
$message = '';
$error = '';

if (is_post()) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $body = sanitize($_POST['message'] ?? '');
    if (!$name || !$email || !$body) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO ContactMessages (Name, Email, Message, CreatedAt) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$name, $email, $body]);
        $message = 'Thank you, your message has been sent.';
    }
}
?>
<div class="card" style="max-width:640px;margin:auto;">
    <h2>Contact us</h2>
    <p class="muted">Send us a message about orders, products, or any support request.</p>
    <?php if ($message): ?><div class="alert success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Message</label>
        <textarea name="message" rows="4" required></textarea>
        <button class="btn primary" type="submit" style="margin-top:12px;">Send</button>
    </form>
</div>
</main>
</body>
</html>

