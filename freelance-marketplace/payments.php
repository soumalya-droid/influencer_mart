<?php
require_once 'partials/header.php';
checkRole(['client']); // Only clients can view this page

$db = getDBConnection();
$client_id = $_SESSION['user_id'];

// Fetch payment history for the current client
$sql = "SELECT pay.amount, pay.status as payment_status, pay.created_at as payment_date,
               p.title as project_title, p.id as project_id,
               u.username as freelancer_name
        FROM payments pay
        JOIN projects p ON pay.project_id = p.id
        JOIN users u ON pay.freelancer_id = u.id
        WHERE pay.client_id = ?
        ORDER BY pay.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$client_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total spent
$total_spent_stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE client_id = ? AND status = 'completed'");
$total_spent_stmt->execute([$client_id]);
$total_spent = $total_spent_stmt->fetchColumn() ?? 0;
?>

<div class="container">
    <h1 class="mb-4">Payment History</h1>

    <div class="card stats-card bg-light mb-4">
        <div class="card-body">
            <div class="stats-text">Total Spent</div>
            <div class="stats-number text-success">$<?php echo number_format($total_spent, 2); ?></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>All Transactions</h4>
        </div>
        <div class="card-body">
            <?php if ($payments): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Paid To</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><a href="project_detail.php?id=<?php echo $payment['project_id']; ?>"><?php echo htmlspecialchars($payment['project_title']); ?></a></td>
                                    <td><?php echo htmlspecialchars($payment['freelancer_name']); ?></td>
                                    <td class="fw-bold">$<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $payment['payment_status'] == 'completed' ? 'success' : 'warning'; ?> text-capitalize">
                                            <?php echo htmlspecialchars($payment['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center p-4">You have not made any payments yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
