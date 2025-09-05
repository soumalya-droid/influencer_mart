<?php
require_once 'partials/header.php';

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($user_id === 0) {
    $_SESSION['error'] = 'No user specified to view reviews.';
    redirect('dashboard.php');
}

$db = getDBConnection();

// Fetch user whose reviews are being viewed
$user_stmt = $db->prepare("SELECT username, role, profile_picture FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    redirect('dashboard.php');
}

// Fetch reviews for this user
$sql = "SELECT r.*, p.title as project_title, u.username as reviewer_name
        FROM reviews r
        JOIN projects p ON r.project_id = p.id
        JOIN users u ON r.reviewer_id = u.id
        WHERE r.reviewee_id = ?
        ORDER BY r.created_at DESC";
$reviews_stmt = $db->prepare($sql);
$reviews_stmt->execute([$user_id]);
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$avg_rating = 0;
$total_reviews = count($reviews);
if ($total_reviews > 0) {
    $total_rating = 0;
    foreach ($reviews as $review) {
        $total_rating += $review['rating'];
    }
    $avg_rating = round($total_rating / $total_reviews, 1);
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'https://via.placeholder.com/150'); ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                    <hr>
                    <h5>Overall Rating</h5>
                    <h3 class="star-rating"><?php echo $avg_rating; ?> / 5</h3>
                    <p>(Based on <?php echo $total_reviews; ?> review<?php echo $total_reviews !== 1 ? 's' : ''; ?>)</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h2>User Reviews</h2>
            <hr>
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($review['reviewer_name']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted">For project: <a href="project_detail.php?id=<?php echo $review['project_id']; ?>"><?php echo htmlspecialchars($review['project_title']); ?></a></h6>
                                </div>
                                <div class="star-rating">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i < $review['rating'] ? '' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="card-text mt-2">"<?php echo nl2br(htmlspecialchars($review['comment'])); ?>"</p>
                            <small class="text-muted">Reviewed on <?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center p-4">This user has not received any reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
