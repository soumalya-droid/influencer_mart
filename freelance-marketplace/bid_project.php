<?php
require_once 'partials/header.php';
checkRole(['freelancer']); // Only freelancers can bid

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
if ($project_id === 0) {
    $_SESSION['error'] = 'Invalid project specified.';
    redirect('project_list.php');
}

$db = getDBConnection();
$freelancer_id = $_SESSION['user_id'];

// Fetch project details to display
$stmt = $db->prepare("SELECT title, client_id FROM projects WHERE id = ? AND status = 'open'");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    $_SESSION['error'] = 'This project is not available for bidding.';
    redirect('project_list.php');
}

// Check if freelancer has already placed a bid
$stmt = $db->prepare("SELECT id FROM bids WHERE project_id = ? AND freelancer_id = ?");
$stmt->execute([$project_id, $freelancer_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'You have already placed a bid on this project.';
    redirect('project_detail.php?id=' . $project_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        redirect('bid_project.php?project_id=' . $project_id);
    }

    $amount = filter_var(sanitizeInput($_POST['amount']), FILTER_VALIDATE_FLOAT);
    $proposal = sanitizeInput($_POST['proposal']);

    $errors = [];
    if ($amount === false || $amount <= 0) $errors[] = 'Please enter a valid bid amount.';
    if (empty($proposal)) $errors[] = 'Your proposal message cannot be empty.';

    if (empty($errors)) {
        $sql = "INSERT INTO bids (project_id, freelancer_id, amount, proposal) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt->execute([$project_id, $freelancer_id, $amount, $proposal])) {
            // Notify project owner
            $notification_msg = "You have a new bid on your project '" . htmlspecialchars($project['title']) . "'.";
            $notification_link = "project_detail.php?id=" . $project_id;
            $notify_stmt = $db->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
            $notify_stmt->execute([$project['client_id'], $notification_msg, $notification_link]);

            $_SESSION['success'] = 'Your bid has been placed successfully!';
            redirect('project_detail.php?id=' . $project_id);
        } else {
            $_SESSION['error'] = 'Failed to place your bid. Please try again.';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}
?>

<div class="form-section">
    <h2 class="mb-2">Place Your Bid</h2>
    <p class="lead">For project: <strong><?php echo htmlspecialchars($project['title']); ?></strong></p>
    <hr>
    <form method="POST" action="bid_project.php?project_id=<?php echo $project_id; ?>" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div class="mb-3">
            <label for="amount" class="form-label">Your Bid Amount ($)</label>
            <input type="number" class="form-control" id="amount" name="amount" placeholder="e.g., 450" required step="0.01">
            <div class="invalid-feedback">Please enter a valid bid amount.</div>
        </div>

        <div class="mb-3">
            <label for="proposal" class="form-label">Your Proposal</label>
            <textarea class="form-control" id="proposal" name="proposal" rows="8" placeholder="Explain why you are the best fit for this project. Detail your approach and timeline." required></textarea>
            <div class="invalid-feedback">A proposal message is required.</div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Submit Bid</button>
    </form>
</div>

<?php require_once 'partials/footer.php'; ?>
