<?php
require_once 'partials/header.php';

$db = getDBConnection();

// Pagination setup
$limit = 10; // Projects per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query
$sql = "SELECT p.*, u.username as client_name, c.name as category_name
        FROM projects p
        JOIN users u ON p.client_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'open'";
$count_sql = "SELECT COUNT(*) FROM projects WHERE status = 'open'";
$params = [];

// Filtering by category
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $count_sql .= " AND category_id = ?";
    $params[] = $category_id;
}

// Get total projects for pagination
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute($params);
$total_projects = $count_stmt->fetchColumn();
$total_pages = ceil($total_projects / $limit);

// Add ordering and pagination to main query
$sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params_for_exec = array_merge($params, [$limit, $offset]);


// Fetch projects
$stmt = $db->prepare($sql);
// PDO requires integer type for LIMIT/OFFSET placeholders, so we bind them specifically.
$param_index = 1;
foreach($params as $param) {
    $stmt->bindValue($param_index++, $param);
}
$stmt->bindValue($param_index++, $limit, PDO::PARAM_INT);
$stmt->bindValue($param_index++, $offset, PDO::PARAM_INT);

$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for filter dropdown
$categories_stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h4>Filter Projects</h4>
            <div class="list-group">
                <a href="project_list.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">All Categories</a>
                <?php foreach ($categories as $category) : ?>
                    <a href="project_list.php?category_id=<?php echo $category['id']; ?>"
                       class="list-group-item list-group-item-action <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-9">
            <h1 class="mb-4">Browse Projects</h1>
            <?php if ($projects): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="card project-card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><a href="project_detail.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></a></h5>
                            <p class="card-text text-muted">
                                Posted by <?php echo htmlspecialchars($project['client_name']); ?> |
                                Category: <span class="badge bg-info"><?php echo htmlspecialchars($project['category_name']); ?></span> |
                                Budget: <span class="fw-bold">$<?php echo htmlspecialchars($project['budget']); ?></span>
                            </p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($project['description'], 0, 200)); ?>...</p>
                            <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary">View & Bid</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="project_list.php?page=<?php echo $i; ?><?php if($category_id) echo '&category_id='.$category_id; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>

            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    No projects found.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
