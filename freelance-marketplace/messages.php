<?php
require_once 'partials/header.php';
checkRole(['client', 'freelancer', 'admin']); // Must be logged in

$db = getDBConnection();
$user_id = $_SESSION['user_id'];

// This is a complex query to get the list of conversations.
// It finds the last message for each unique conversation partner.
$sql = "
    SELECT u.id as partner_id, u.username as partner_name, u.profile_picture, m1.message, m1.created_at, m1.is_read, m1.sender_id
    FROM messages m1
    INNER JOIN (
        SELECT
            LEAST(sender_id, receiver_id) as user1,
            GREATEST(sender_id, receiver_id) as user2,
            MAX(id) as last_message_id
        FROM messages
        WHERE sender_id = :user_id OR receiver_id = :user_id
        GROUP BY user1, user2
    ) m2 ON m1.id = m2.last_message_id
    INNER JOIN users u ON (CASE WHEN m2.user1 = :user_id THEN m2.user2 ELSE m2.user1 END) = u.id
    WHERE m1.sender_id = :user_id OR m1.receiver_id = :user_id
    ORDER BY m1.created_at DESC
";
$stmt = $db->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container">
    <h1 class="mb-4">My Messages</h1>
    <div class="card">
        <div class="list-group list-group-flush">
            <?php if ($conversations): ?>
                <?php foreach ($conversations as $convo): ?>
                    <a href="chat.php?with_user_id=<?php echo $convo['partner_id']; ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($convo['profile_picture'] ?? 'https://via.placeholder.com/50'); ?>" alt="User" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($convo['partner_name']); ?></h5>
                                    <p class="mb-1 <?php echo ($convo['is_read'] == 0 && $convo['sender_id'] != $user_id) ? 'fw-bold' : ''; ?>">
                                        <?php echo htmlspecialchars(substr($convo['message'], 0, 50)); ?>...
                                    </p>
                                </div>
                            </div>
                            <small class="text-muted"><?php echo date('M d', strtotime($convo['created_at'])); ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    <p class="text-center p-4">You have no messages. Start a conversation from a project page.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
