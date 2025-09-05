<?php
require_once 'partials/header.php';
checkRole(['freelancer']); // Only freelancers can view this page

$db = getDBConnection();
$freelancer_id = $_SESSION['user_id'];

// Fetch bids made by the current freelancer, along with project details
$sql = "SELECT b.amount as bid_amount, b.proposal, b.created_at as bid_date,
               p.id as project_id, p.title as project_title, p.status as project_status,
               c.name as category_name
        FROM bids b
        JOIN projects p ON b.project_id = p.id
        JOIN categories c ON p.category_id = c.id
        WHERE b.freelancer_id = ?
        ORDER BY b.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$freelancer_id]);
$bids = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Bids</h1>
        <a href="project_list.php" class="btn btn-primary"><i class="fas fa-search"></i> Browse More Projects</a>
    </div>

    <?php if ($bids): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Project Title</th>
                                <th>Category</th>
                                <th>My Bid</th>
                                <th>Project Status</th>
                                <th>Date Placed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bids as $bid): ?>
                                <tr>
                                    <td>
                                        <a href="project_detail.php?id=<?php echo $bid['project_id']; ?>" class="fw-bold">
                                            <?php echo htmlspecialchars($bid['project_title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($bid['category_name']); ?></td>
                                    <td class="fw-bold text-success">$<?php echo htmlspecialchars($bid['bid_amount']); ?></td>
                                    <td>
                                        <span class="badge status-badge bg-<?php
                                            switch($bid['project_status']) {
                                                case 'open': echo 'primary'; break;
                                                case 'in_progress': echo 'warning'; break;
                                                case 'completed': echo 'success'; break;
                                                case 'cancelled': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?> text-capitalize">
                                            <?php echo htmlspecialchars($bid['project_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($bid['bid_date'])); ?></td>
                                    <td>
                                        <a href="project_detail.php?id=<?php echo $bid['project_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Project
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center p-5 border rounded">
            <h3>You haven't placed any bids yet.</h3>
            <p>Find a project that matches your skills and place a bid to get started.</p>
            <a href="project_list.php" class="btn btn-primary mt-3">Browse Projects</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'partials/footer.php'; ?>
