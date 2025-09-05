<?php
require_once 'partials/header.php';
// Note: database.php is already included in header.php

$db = getDBConnection();

// Get featured projects
$stmt = $db->query("SELECT p.*, u.username as client_name, c.name as category_name
                   FROM projects p
                   JOIN users u ON p.client_id = u.id
                   JOIN categories c ON p.category_id = c.id
                   WHERE p.status = 'open'
                   ORDER BY p.created_at DESC
                   LIMIT 6");
$featuredProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $db->query("SELECT * FROM categories LIMIT 8");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Find Perfect Freelance Services</h1>
        <p class="lead">Connect with skilled freelancers for your projects or find work as a freelancer</p>
        <a href="project_list.php" class="btn btn-primary btn-lg me-2">Browse Projects</a>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'client'): ?>
            <a href="post_project.php" class="btn btn-secondary btn-lg">Post a Project</a>
        <?php endif; ?>
    </div>
</div>

<section class="container">
    <h2 class="text-center mb-4">Featured Projects</h2>
    <div class="row">
        <?php if ($featuredProjects): ?>
            <?php foreach ($featuredProjects as $project): ?>
                <div class="col-md-4">
                    <div class="card project-card">
                        <div class="card-body">
                            <h5 class="card-title"><a href="project_detail.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></a></h5>
                            <p class="card-text text-muted">Posted by <?php echo htmlspecialchars($project['client_name']); ?> in <span class="badge bg-info"><?php echo htmlspecialchars($project['category_name']); ?></span></p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">$<?php echo htmlspecialchars($project['budget']); ?></span>
                                <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="btn btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No featured projects available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<section class="container mt-5">
    <h2 class="text-center mb-4">Browse Categories</h2>
    <div class="row">
        <?php if ($categories): ?>
            <?php foreach ($categories as $category): ?>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-code fa-2x text-primary mb-2"></i> <!-- Example icon -->
                            <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <a href="project_list.php?category_id=<?php echo $category['id']; ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No categories found.</p>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'partials/footer.php';
?>
