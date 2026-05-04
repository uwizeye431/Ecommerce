<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/functions.php';

$message = '';
$error = '';
if (is_post()) {
    $name  = sanitize($_POST['name']    ?? '');
    $email = sanitize($_POST['email']   ?? '');
    $body  = sanitize($_POST['message'] ?? '');
    if (save_contact_message($name, $email, $body)) {
        $message = 'Thank you, your message has been sent.';
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<div class="card" style="max-width:640px;margin:auto;">
    <h2>Contact us</h2>
    <p class="muted">Send us a message about orders, products, or any support request.</p>
    <?php render_alerts($message, $error); ?>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>

