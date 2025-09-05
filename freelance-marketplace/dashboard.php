<?php
require_once 'partials/header.php';
checkRole(['client', 'freelancer', 'admin']); // Must be logged in

$db = getDBConnection();
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch role-specific data
$stats = [];
if ($role === 'client') {
    // Stats for clients
    $stmt = $db->prepare("SELECT COUNT(*) FROM projects WHERE client_id = ? AND status = 'open'");
    $stmt->execute([$user_id]);
    $stats['open_projects'] = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM projects WHERE client_id = ? AND status = 'completed'");
    $stmt->execute([$user_id]);
    $stats['completed_projects'] = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(b.id) FROM bids b JOIN projects p ON b.project_id = p.id WHERE p.client_id = ?");
    $stmt->execute([$user_id]);
    $stats['total_bids_received'] = $stmt->fetchColumn();

} elseif ($role === 'freelancer') {
    // Stats for freelancers
    $stmt = $db->prepare("SELECT COUNT(*) FROM bids WHERE freelancer_id = ?");
    $stmt->execute([$user_id]);
    $stats['total_bids'] = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM projects WHERE assigned_freelancer_id = ? AND status = 'in_progress'");
    $stmt->execute([$user_id]);
    $stats['active_projects'] = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE freelancer_id = ? AND status = 'completed'");
    $stmt->execute([$user_id]);
    $stats['total_earnings'] = $stmt->fetchColumn() ?? 0;
} elseif ($role === 'admin') {
    // Stats for admin
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();
    $stmt = $db->query("SELECT COUNT(*) FROM projects");
    $stats['total_projects'] = $stmt->fetchColumn();
    $stmt = $db->query("SELECT COUNT(*) FROM projects WHERE status='open'");
    $stats['open_projects'] = $stmt->fetchColumn();
}

?>
<div class="container">
    <h1 class="mb-4">Dashboard</h1>
    <div class="alert alert-info">
        Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! You are logged in as a <?php echo ucfirst($role); ?>.
    </div>

    <div class="row">
        <?php if ($role === 'client'): ?>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-primary mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['open_projects']; ?></div>
                        <div class="stats-text">Open Projects</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-success mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['completed_projects']; ?></div>
                        <div class="stats-text">Completed Projects</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-info mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['total_bids_received']; ?></div>
                        <div class="stats-text">Bids Received</div>
                    </div>
                </div>
            </div>
        <?php elseif ($role === 'freelancer'): ?>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-primary mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['total_bids']; ?></div>
                        <div class="stats-text">Bids Placed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-warning mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['active_projects']; ?></div>
                        <div class="stats-text">Active Projects</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-success mb-3">
                    <div class="card-body">
                        <div class="stats-number">$<?php echo number_format($stats['total_earnings'], 2); ?></div>
                        <div class="stats-text">Total Earnings</div>
                    </div>
                </div>
            </div>
        <?php elseif ($role === 'admin'): ?>
             <div class="col-md-4">
                <div class="card stats-card text-white bg-danger mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['total_users']; ?></div>
                        <div class="stats-text">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['total_projects']; ?></div>
                        <div class="stats-text">Total Projects</div>
                    </div>
                </div>
            </div>
             <div class="col-md-4">
                <div class="card stats-card text-white bg-info mb-3">
                    <div class="card-body">
                        <div class="stats-number"><?php echo $stats['open_projects']; ?></div>
                        <div class="stats-text">Open Projects</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <h2>Quick Links</h2>
        <hr>
        <div class="list-group">
            <?php if ($role === 'client'): ?>
                <a href="post_project.php" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle me-2"></i>Post a New Project</a>
                <a href="my_projects.php" class="list-group-item list-group-item-action"><i class="fas fa-tasks me-2"></i>Manage My Projects</a>
                <a href="project_list.php" class="list-group-item list-group-item-action"><i class="fas fa-search me-2"></i>Browse Freelancers</a>
            <?php elseif ($role === 'freelancer'): ?>
                <a href="project_list.php" class="list-group-item list-group-item-action"><i class="fas fa-search me-2"></i>Browse Projects</a>
                <a href="my_bids.php" class="list-group-item list-group-item-action"><i class="fas fa-gavel me-2"></i>View My Bids</a>
                <a href="earnings.php" class="list-group-item list-group-item-action"><i class="fas fa-dollar-sign me-2"></i>My Earnings</a>
            <?php elseif ($role === 'admin'): ?>
                <a href="manage_users.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
                <a href="manage_projects.php" class="list-group-item list-group-item-action"><i class="fas fa-project-diagram me-2"></i>Manage Projects</a>
                <a href="manage_categories.php" class="list-group-item list-group-item-action"><i class="fas fa-tags me-2"></i>Manage Categories</a>
            <?php endif; ?>
             <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-user-edit me-2"></i>Edit My Profile</a>
             <a href="messages.php" class="list-group-item list-group-item-action"><i class="fas fa-envelope me-2"></i>My Messages</a>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
