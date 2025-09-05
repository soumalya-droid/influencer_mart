<?php
require_once 'partials/header.php';
checkRole(['client', 'freelancer', 'admin']); // Must be logged in

$current_user_id = $_SESSION['user_id'];
$other_user_id = isset($_GET['with_user_id']) ? (int)$_GET['with_user_id'] : 0;

if ($other_user_id === 0 || $other_user_id === $current_user_id) {
    $_SESSION['error'] = 'Invalid user to chat with.';
    redirect('messages.php');
}

$db = getDBConnection();

// Fetch the other user's info
$stmt = $db->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
$stmt->execute([$other_user_id]);
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$other_user) {
    $_SESSION['error'] = 'User not found.';
    redirect('messages.php');
}

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
    } else {
        $message = sanitizeInput($_POST['message']);
        if (!empty($message)) {
            $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
            $insert_stmt = $db->prepare($sql);
            $insert_stmt->execute([$current_user_id, $other_user_id, $message]);
        }
    }
    // Redirect to the same page to prevent form resubmission
    redirect("chat.php?with_user_id=$other_user_id");
}

// Fetch message history
$sql = "SELECT * FROM messages
        WHERE (sender_id = :current_user AND receiver_id = :other_user)
           OR (sender_id = :other_user AND receiver_id = :current_user)
        ORDER BY created_at ASC";
$msg_stmt = $db->prepare($sql);
$msg_stmt->execute(['current_user' => $current_user_id, 'other_user' => $other_user_id]);
$messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark messages as read
$update_sql = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?";
$update_stmt = $db->prepare($update_sql);
$update_stmt->execute([$other_user_id, $current_user_id]);

?>
<div class="container">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <img src="<?php echo htmlspecialchars($other_user['profile_picture'] ?? 'https://via.placeholder.com/40'); ?>" class="profile-img me-2" alt="Profile">
            <h4 class="mb-0">Chat with <?php echo htmlspecialchars($other_user['username']); ?></h4>
        </div>
        <div class="card-body">
            <div class="chat-container mb-3">
                <?php if ($messages): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?php echo $msg['sender_id'] == $current_user_id ? 'sent' : 'received'; ?>">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            <div class="text-end" style="font-size: 0.75rem; margin-top: 5px;">
                                <small><?php echo date('h:i A', strtotime($msg['created_at'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">No messages yet. Start the conversation!</p>
                <?php endif; ?>
            </div>
            <form method="POST" action="chat.php?with_user_id=<?php echo $other_user_id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="input-group">
                    <textarea name="message" class="form-control" placeholder="Type your message..." required rows="2"></textarea>
                    <button class="btn btn-primary" type="submit">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
