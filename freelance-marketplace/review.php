<?php
require_once 'partials/header.php';
checkRole(['client', 'freelancer']); // Must be logged in to leave a review

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
if ($project_id === 0) {
    $_SESSION['error'] = 'Invalid project specified for review.';
    redirect('dashboard.php');
}

$db = getDBConnection();
$reviewer_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch project to ensure it's completed and user is involved
$sql = "SELECT * FROM projects WHERE id = ? AND status = 'completed'";
$stmt = $db->prepare($sql);
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    $_SESSION['error'] = 'This project cannot be reviewed at this time.';
    redirect('dashboard.php');
}

// Determine who is being reviewed
$reviewee_id = ($role === 'client') ? $project['assigned_freelancer_id'] : $project['client_id'];

// Check if user is the client or the assigned freelancer
if ($reviewer_id != $project['client_id'] && $reviewer_id != $project['assigned_freelancer_id']) {
    $_SESSION['error'] = 'You are not authorized to review this project.';
    redirect('dashboard.php');
}

// Check if a review has already been submitted by this user for this project
$check_stmt = $db->prepare("SELECT id FROM reviews WHERE project_id = ? AND reviewer_id = ?");
$check_stmt->execute([$project_id, $reviewer_id]);
if ($check_stmt->fetch()) {
    $_SESSION['error'] = 'You have already submitted a review for this project.';
    redirect('project_detail.php?id=' . $project_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        redirect('review.php?project_id=' . $project_id);
    }

    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = sanitizeInput($_POST['comment']);

    $errors = [];
    if ($rating < 1 || $rating > 5) $errors[] = 'Please select a rating between 1 and 5 stars.';
    if (empty($comment)) $errors[] = 'A comment is required for the review.';

    if (empty($errors)) {
        $sql = "INSERT INTO reviews (project_id, reviewer_id, reviewee_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $db->prepare($sql);
        if ($insert_stmt->execute([$project_id, $reviewer_id, $reviewee_id, $rating, $comment])) {
            $_SESSION['success'] = 'Your review has been submitted successfully. Thank you!';
            redirect('project_detail.php?id=' . $project_id);
        } else {
            $_SESSION['error'] = 'Failed to submit your review. Please try again.';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}
?>
<style>
.rating { display: inline-block; }
.rating input { display: none; }
.rating label { float: right; cursor: pointer; color: #ccc; transition: color 0.2s; font-size: 2rem; }
.rating label:before { content: '\2605'; }
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label { color: #ffc107; }
</style>

<div class="form-section">
    <h2 class="mb-2">Leave a Review</h2>
    <p class="lead">For project: <strong><?php echo htmlspecialchars($project['title']); ?></strong></p>
    <hr>
    <form method="POST" action="review.php?project_id=<?php echo $project_id; ?>" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div class="mb-4 text-center">
            <label class="form-label d-block">Your Rating</label>
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars"></label>
                <input type="radio" id="star4" name="rating" value="4" required/><label for="star4" title="4 stars"></label>
                <input type="radio" id="star3" name="rating" value="3" required/><label for="star3" title="3 stars"></label>
                <input type="radio" id="star2" name="rating" value="2" required/><label for="star2" title="2 stars"></label>
                <input type="radio" id="star1" name="rating" value="1" required/><label for="star1" title="1 star"></label>
            </div>
            <div class="invalid-feedback d-block">Please provide a star rating.</div>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Your Comments</label>
            <textarea class="form-control" id="comment" name="comment" rows="6" placeholder="Share your experience working on this project..." required></textarea>
            <div class="invalid-feedback">Please leave a comment.</div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Submit Review</button>
    </form>
</div>

<?php require_once 'partials/footer.php'; ?>
