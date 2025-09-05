<?php
require_once 'partials/header.php';
checkRole(['admin']);

$db = getDBConnection();

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_status'])) {
    if (validate_csrf($_POST['csrf_token'])) {
        $payment_id = (int)$_POST['payment_id'];
        $status = sanitizeInput($_POST['status']);
        if (in_array($status, ['pending', 'completed', 'failed'])) {
            $stmt = $db->prepare("UPDATE payments SET status = ? WHERE id = ?");
            $stmt->execute([$status, $payment_id]);
            $_SESSION['success'] = 'Payment status updated.';
        } else {
            $_SESSION['error'] = 'Invalid status.';
        }
    } else {
        $_SESSION['error'] = 'Invalid CSRF token.';
    }
    redirect('manage_payments.php');
}

// Pagination
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_payments = $db->query("SELECT COUNT(*) FROM payments")->fetchColumn();
$total_pages = ceil($total_payments / $limit);

// Fetch all payments with related info
$sql = "SELECT pay.*, p.title as project_title, c.username as client_name, f.username as freelancer_name
        FROM payments pay
        JOIN projects p ON pay.project_id = p.id
        JOIN users c ON pay.client_id = c.id
        JOIN users f ON pay.freelancer_id = f.id
        ORDER BY pay.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container">
    <h1 class="mb-4">Manage Payments</h1>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Freelancer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo $payment['id']; ?></td>
                                <td><a href="project_detail.php?id=<?php echo $payment['project_id']; ?>"><?php echo htmlspecialchars($payment['project_title']); ?></a></td>
                                <td><?php echo htmlspecialchars($payment['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($payment['freelancer_name']); ?></td>
                                <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $payment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="completed" <?php echo $payment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="failed" <?php echo $payment['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                        </select>
                                        <noscript><button type="submit" name="update_payment_status" class="btn btn-sm">Update</button></noscript>
                                    </form>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($payment['created_at'])); ?></td>
                                <td>
                                    <!-- Can add link to a detailed transaction view -->
                                    <button class="btn btn-sm btn-outline-secondary">Details</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

             <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
