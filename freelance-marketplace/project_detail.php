<?php
require_once 'partials/header.php';

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($project_id === 0) {
    $_SESSION['error'] = 'Invalid project ID.';
    redirect('project_list.php');
}

$db = getDBConnection();

// Fetch project details
$sql = "SELECT p.*, u.username as client_name, u.id as client_id, c.name as category_name
        FROM projects p
        JOIN users u ON p.client_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    $_SESSION['error'] = 'Project not found.';
    redirect('project_list.php');
}

// Fetch bids for this project
$bids_sql = "SELECT b.*, u.username as freelancer_name, u.profile_picture
             FROM bids b
             JOIN users u ON b.freelancer_id = u.id
             WHERE b.project_id = ?
             ORDER BY b.created_at DESC";
$bids_stmt = $db->prepare($bids_sql);
$bids_stmt->execute([$project_id]);
$bids = $bids_stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if current user has already bid
$has_bid = false;
if (isLoggedIn() && $_SESSION['role'] === 'freelancer') {
    foreach ($bids as $bid) {
        if ($bid['freelancer_id'] == $_SESSION['user_id']) {
            $has_bid = true;
            break;
        }
    }
}
?>

<div class="container">
    <div class="row">
        <!-- Main project details -->
        <div class="col-lg-8">
            <h1 class="mb-3"><?php echo htmlspecialchars($project['title']); ?></h1>
            <div class="d-flex align-items-center mb-3 text-muted">
                <span>Posted by: <?php echo htmlspecialchars($project['client_name']); ?></span>
                <span class="mx-2">|</span>
                <span>Category: <span class="badge bg-secondary"><?php echo htmlspecialchars($project['category_name']); ?></span></span>
                <span class="mx-2">|</span>
                <span>Status: <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($project['status']); ?></span></span>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Project Description</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Required Skills</h5>
                    <div>
                        <?php
                        $skills = explode(',', $project['required_skills']);
                        foreach($skills as $skill): ?>
                            <span class="badge bg-light text-dark skill-badge"><?php echo htmlspecialchars(trim($skill)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Bids Section -->
            <div class="mt-4">
                <h3>Bids (<?php echo count($bids); ?>)</h3>
                <hr>
                <?php if ($bids): ?>
                    <?php foreach ($bids as $bid): ?>
                        <div class="card bid-card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?php echo htmlspecialchars($bid['freelancer_name']); ?></strong>
                                        <p class="text-muted mb-2">Bid on: <?php echo date('M d, Y', strtotime($bid['created_at'])); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="text-success">$<?php echo htmlspecialchars($bid['amount']); ?></h4>
                                        <?php if(isLoggedIn() && $project['client_id'] == $_SESSION['user_id'] && $project['status'] == 'open'): ?>
                                            <form action="accept_bid.php" method="POST" class="d-inline">
                                                <input type="hidden" name="bid_id" value="<?php echo $bid['id']; ?>">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Accept Bid</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($bid['proposal'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No bids have been placed on this project yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Project Budget</h5>
                    <p class="display-5 fw-bold text-primary">$<?php echo htmlspecialchars($project['budget']); ?></p>
                    <?php if (isLoggedIn() && $_SESSION['role'] === 'freelancer' && !$has_bid && $project['status'] == 'open'): ?>
                        <a href="bid_project.php?project_id=<?php echo $project['id']; ?>" class="btn btn-success btn-lg w-100">Place Your Bid</a>
                    <?php elseif (isLoggedIn() && $_SESSION['role'] === 'freelancer' && $has_bid): ?>
                        <button class="btn btn-secondary btn-lg w-100" disabled>You have already bid</button>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="login.php" class="btn btn-info btn-lg w-100">Login to Bid</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
