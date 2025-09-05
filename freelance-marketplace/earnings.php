<?php
require_once 'partials/header.php';
checkRole(['freelancer']); // Only freelancers can view this page

$db = getDBConnection();
$freelancer_id = $_SESSION['user_id'];

// Fetch earnings history for the current freelancer
$sql = "SELECT pay.amount, pay.status as payment_status, pay.created_at as payment_date,
               p.title as project_title, p.id as project_id,
               u.username as client_name
        FROM payments pay
        JOIN projects p ON pay.project_id = p.id
        JOIN users u ON pay.client_id = u.id
        WHERE pay.freelancer_id = ?
        ORDER BY pay.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$freelancer_id]);
$earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate earning stats
$total_earnings_stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE freelancer_id = ? AND status = 'completed'");
$total_earnings_stmt->execute([$freelancer_id]);
$total_earnings = $total_earnings_stmt->fetchColumn() ?? 0;

$pending_clearance_stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE freelancer_id = ? AND status = 'pending'");
$pending_clearance_stmt->execute([$freelancer_id]);
$pending_clearance = $pending_clearance_stmt->fetchColumn() ?? 0;
?>

<div class="container">
    <h1 class="mb-4">My Earnings</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card stats-card bg-light">
                <div class="card-body">
                    <div class="stats-text">Total Cleared Earnings</div>
                    <div class="stats-number text-success">$<?php echo number_format($total_earnings, 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stats-card bg-light">
                <div class="card-body">
                    <div class="stats-text">Pending Clearance</div>
                    <div class="stats-number text-warning">$<?php echo number_format($pending_clearance, 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Earnings History</h4>
        </div>
        <div class="card-body">
            <?php if ($earnings): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($earnings as $earning): ?>
                                <tr>
                                    <td><a href="project_detail.php?id=<?php echo $earning['project_id']; ?>"><?php echo htmlspecialchars($earning['project_title']); ?></a></td>
                                    <td><?php echo htmlspecialchars($earning['client_name']); ?></td>
                                    <td class="fw-bold">$<?php echo number_format($earning['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $earning['payment_status'] == 'completed' ? 'success' : 'warning'; ?> text-capitalize">
                                            <?php echo htmlspecialchars($earning['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($earning['payment_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center p-4">You have no earnings history yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
