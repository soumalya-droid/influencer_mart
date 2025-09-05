<?php
require_once 'partials/header.php';
checkRole(['admin']);

$db = getDBConnection();

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if (validate_csrf($_POST['csrf_token'])) {
        $user_id_to_delete = (int)$_POST['user_id'];
        // Add a check to prevent deleting the main admin or oneself
        if ($user_id_to_delete > 1 && $user_id_to_delete != $_SESSION['user_id']) {
            $delete_stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $delete_stmt->execute([$user_id_to_delete]);
            $_SESSION['success'] = 'User deleted successfully.';
        } else {
            $_SESSION['error'] = 'This user cannot be deleted.';
        }
    } else {
        $_SESSION['error'] = 'Invalid CSRF token.';
    }
    redirect('manage_users.php');
}

// Pagination
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Fetch users
$stmt = $db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container">
    <h1 class="mb-4">Manage Users</h1>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge bg-info"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="manage_users.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger">Delete</button>
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
