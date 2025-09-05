<?php
require_once 'partials/header.php';
checkRole(['admin']);

$db = getDBConnection();

// Handle project deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
    if (validate_csrf($_POST['csrf_token'])) {
        $project_id_to_delete = (int)$_POST['project_id'];
        $delete_stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $delete_stmt->execute([$project_id_to_delete]);
        // Also consider deleting related bids, etc., or handle with DB constraints
        $_SESSION['success'] = 'Project deleted successfully.';
    } else {
        $_SESSION['error'] = 'Invalid CSRF token.';
    }
    redirect('manage_projects.php');
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_projects = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_pages = ceil($total_projects / $limit);

// Fetch projects with client and freelancer names
$sql = "SELECT p.*, c.username as client_name, f.username as freelancer_name
        FROM projects p
        JOIN users c ON p.client_id = c.id
        LEFT JOIN users f ON p.assigned_freelancer_id = f.id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container">
    <h1 class="mb-4">Manage Projects</h1>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Freelancer</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><a href="project_detail.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></a></td>
                                <td><?php echo htmlspecialchars($project['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($project['freelancer_name'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($project['budget'], 2); ?></td>
                                <td><span class="badge bg-secondary text-capitalize"><?php echo htmlspecialchars($project['status']); ?></span></td>
                                <td>
                                    <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <form method="POST" action="manage_projects.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                        <button type="submit" name="delete_project" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
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
