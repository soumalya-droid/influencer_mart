<?php
require_once 'partials/header.php';
checkRole(['client']); // Only clients can post projects

$db = getDBConnection();

// Fetch categories for the dropdown
$stmt = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        redirect('post_project.php');
    }

    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $category_id = (int)sanitizeInput($_POST['category_id']);
    $budget = filter_var(sanitizeInput($_POST['budget']), FILTER_VALIDATE_FLOAT);
    $required_skills = sanitizeInput($_POST['required_skills']); // Comma-separated list of skills

    $errors = [];
    if (empty($title)) $errors[] = 'Project title is required.';
    if (empty($description)) $errors[] = 'Project description is required.';
    if (empty($category_id)) $errors[] = 'Please select a category.';
    if ($budget === false || $budget <= 0) $errors[] = 'Please enter a valid budget.';

    if (empty($errors)) {
        $client_id = $_SESSION['user_id'];
        $sql = "INSERT INTO projects (client_id, title, description, category_id, budget, required_skills, status) VALUES (?, ?, ?, ?, ?, ?, 'open')";
        $stmt = $db->prepare($sql);

        if ($stmt->execute([$client_id, $title, $description, $category_id, $budget, $required_skills])) {
            $_SESSION['success'] = 'Your project has been posted successfully!';
            redirect('my_projects.php');
        } else {
            $_SESSION['error'] = 'Failed to post project. Please try again.';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

?>
<div class="form-section">
    <h2 class="mb-4">Post a New Project</h2>
    <p>Fill out the form below to post your project and start receiving bids from talented freelancers.</p>
    <hr>
    <form method="POST" action="post_project.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Project Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Build a modern e-commerce website" required>
            <div class="invalid-feedback">A project title is required.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Project Description</label>
            <textarea class="form-control" id="description" name="description" rows="6" placeholder="Describe your project in detail..." required></textarea>
            <div class="invalid-feedback">A detailed description is required.</div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="" disabled selected>Select a category...</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a project category.</div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="budget" class="form-label">Budget ($)</label>
                <input type="number" class="form-control" id="budget" name="budget" placeholder="e.g., 500" required step="0.01">
                <div class="invalid-feedback">Please enter a valid budget amount.</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="required_skills" class="form-label">Required Skills</label>
            <input type="text" class="form-control" id="required_skills" name="required_skills" placeholder="e.g., PHP, JavaScript, MySQL, HTML, CSS">
            <small class="form-text text-muted">Enter skills separated by commas.</small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Post Project</button>
    </form>
</div>

<?php require_once 'partials/footer.php'; ?>
