<?php
require_once 'partials/header.php';
checkRole(['admin']); // Only admins can access this page

$db = getDBConnection();

// Fetch key statistics for the admin dashboard
$stats = [
    'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_clients' => $db->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn(),
    'total_freelancers' => $db->query("SELECT COUNT(*) FROM users WHERE role = 'freelancer'")->fetchColumn(),
    'total_projects' => $db->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'open_projects' => $db->query("SELECT COUNT(*) FROM projects WHERE status = 'open'")->fetchColumn(),
    'completed_projects' => $db->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'")->fetchColumn(),
    'total_payments' => $db->query("SELECT COUNT(*) FROM payments")->fetchColumn(),
    'total_volume' => $db->query("SELECT SUM(amount) FROM payments WHERE status = 'completed'")->fetchColumn() ?? 0,
];

?>
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card stats-card text-white bg-primary">
                <div class="card-body">
                    <div class="stats-number"><?php echo $stats['total_users']; ?></div>
                    <div class="stats-text">Total Users</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stats-card text-white bg-info">
                <div class="card-body">
                    <div class="stats-number"><?php echo $stats['total_projects']; ?></div>
                    <div class="stats-text">Total Projects</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stats-card text-white bg-success">
                <div class="card-body">
                    <div class="stats-number">$<?php echo number_format($stats['total_volume']); ?></div>
                    <div class="stats-text">Total Volume</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stats-card text-white bg-warning">
                <div class="card-body">
                    <div class="stats-number"><?php echo $stats['open_projects']; ?></div>
                    <div class="stats-text">Open Projects</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Quick Management Links</h4>
        </div>
        <div class="card-body">
            <div class="list-group">
                <a href="manage_users.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-users-cog me-3"></i>Manage Users</div>
                    <span class="badge bg-primary rounded-pill"><?php echo $stats['total_users']; ?></span>
                </a>
                <a href="manage_projects.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-project-diagram me-3"></i>Manage Projects</div>
                    <span class="badge bg-primary rounded-pill"><?php echo $stats['total_projects']; ?></span>
                </a>
                <a href="manage_categories.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-tags me-3"></i>Manage Categories</div>
                </a>
                <a href="manage_payments.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-credit-card me-3"></i>Manage Payments</div>
                    <span class="badge bg-primary rounded-pill"><?php echo $stats['total_payments']; ?></span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
