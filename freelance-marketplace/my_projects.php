<?php
require_once 'partials/header.php';
checkRole(['client']); // Only clients can view this page

$db = getDBConnection();
$client_id = $_SESSION['user_id'];

// Fetch projects for the current client
$sql = "SELECT p.*, c.name as category_name,
        (SELECT COUNT(id) FROM bids WHERE project_id = p.id) as bid_count
        FROM projects p
        JOIN categories c ON p.category_id = c.id
        WHERE p.client_id = ?
        ORDER BY p.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$client_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Projects</h1>
        <a href="post_project.php" class="btn btn-primary"><i class="fas fa-plus"></i> Post New Project</a>
    </div>

    <?php if ($projects): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Budget</th>
                                <th>Bids</th>
                                <th>Status</th>
                                <th>Posted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td>
                                        <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="fw-bold">
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($project['category_name']); ?></td>
                                    <td>$<?php echo htmlspecialchars($project['budget']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $project['bid_count']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge bg-<?php
                                            switch($project['status']) {
                                                case 'open': echo 'primary'; break;
                                                case 'in_progress': echo 'warning'; break;
                                                case 'completed': echo 'success'; break;
                                                case 'cancelled': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?> text-capitalize">
                                            <?php echo htmlspecialchars($project['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                                    <td>
                                        <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <!-- Add other actions like Edit/Cancel if status is 'open' -->
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
            <h3>You haven't posted any projects yet.</h3>
            <p>Ready to get started? Post a project and find the perfect freelancer for the job.</p>
            <a href="post_project.php" class="btn btn-primary mt-3">Post Your First Project</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'partials/footer.php'; ?>
