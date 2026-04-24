<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

$user_id = (int)$_SESSION['user_id'];

$messages = $conn->query("
    SELECT m.*,
           CASE WHEN m.sender_id = $user_id THEN r.full_name ELSE s.full_name END as other_name,
           CASE WHEN m.sender_id = $user_id THEN r.id ELSE s.id END as other_id
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    JOIN users r ON m.receiver_id = r.id
    WHERE m.sender_id = $user_id OR m.receiver_id = $user_id
    ORDER BY m.created_at DESC
");

$pageTitle = 'Messages';
require_once '../includes/header.php';
?>

<div class="messages-container">
    <h1>Messages</h1>

    <?php if ($messages->num_rows > 0): ?>
        <div class="message-list">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div class="message-item <?= (!$msg['is_read'] && $msg['receiver_id'] == $user_id) ? 'unread' : '' ?>">
                    <div class="message-avatar"><?= strtoupper(substr($msg['other_name'], 0, 1)) ?></div>
                    <div class="message-content">
                        <h4><?= sanitize($msg['other_name']) ?></h4>
                        <p><?= sanitize(substr($msg['message'], 0, 100)) ?><?= strlen($msg['message']) > 100 ? '...' : '' ?></p>
                    </div>
                    <span class="message-time"><?= date('M d', strtotime($msg['created_at'])) ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-regular fa-message"></i>
            <h3>No messages yet</h3>
            <p>When you contact a host or receive a booking inquiry, you'll see your messages here.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
