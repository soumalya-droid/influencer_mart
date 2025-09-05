<?php
require_once 'partials/header.php';
checkRole(['client', 'freelancer', 'admin']); // Must be logged in

$db = getDBConnection();
$user_id = $_SESSION['user_id'];

// Fetch all notifications for the current user
$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark all unread notifications as read
$update_stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$update_stmt->execute([$user_id]);

?>
<div class="container">
    <h1 class="mb-4">Notifications</h1>

    <div class="card">
        <div class="list-group list-group-flush">
            <?php if ($notifications): ?>
                <?php foreach ($notifications as $notification): ?>
                    <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="list-group-item list-group-item-action <?php echo $notification['is_read'] ? '' : 'list-group-item-secondary'; ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <small><?php echo date('M d, Y, h:i A', strtotime($notification['created_at'])); ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    <p class="text-center p-4">You have no notifications.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
