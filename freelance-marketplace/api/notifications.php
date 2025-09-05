<?php
require_once '../config/database.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Check if user is logged in. If not, stop.
if (!isLoggedIn()) {
    // Sending a final message and closing the connection can be done,
    // but for simplicity, we just exit. The client-side will handle the broken connection.
    exit();
}

$user_id = $_SESSION['user_id'];
$db = getDBConnection();

// Set a reasonable timeout
set_time_limit(60);

// Keep track of the last count to avoid sending redundant data
$last_count = -1;

while (true) {
    // Get the count of unread notifications
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    $unread_count = $stmt->fetchColumn();

    if ($unread_count !== $last_count) {
        $data = ['count' => $unread_count];
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
        $last_count = $unread_count;
    }

    // If the connection is aborted by the client, stop the script.
    if (connection_aborted()) {
        break;
    }

    // Sleep for a few seconds before checking again to reduce server load.
    sleep(5);
}
?>
