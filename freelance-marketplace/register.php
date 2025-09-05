<?php
require_once 'config/database.php';

$page_title = "Register";
$header_title = "Create Your Account";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        redirect('register.php');
    }

    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Not sanitized to allow all characters
    $password_confirm = $_POST['password_confirm'];
    $role = sanitizeInput($_POST['role']);

    // Validation
    $errors = [];
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($password) || strlen($password) < 8) $errors[] = 'Password must be at least 8 characters long.';
    if ($password !== $password_confirm) $errors[] = 'Passwords do not match.';
    if (!in_array($role, ['client', 'freelancer'])) $errors[] = 'Invalid role selected.';

    // Check if username or email already exists
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Username or email already taken.';
    }

    if (empty($errors)) {
        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password, $role])) {
            $_SESSION['success'] = 'Registration successful! Please log in.';
            redirect('login.php');
        } else {
            $_SESSION['error'] = 'Something went wrong. Please try again.';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

require_once 'partials/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $header_title; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="register.php" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">Please enter a username.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                        <div id="password-strength" class="form-text mt-1"></div>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Register as a:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_client" value="client" checked required>
                            <label class="form-check-label" for="role_client">Client (I want to hire)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_freelancer" value="freelancer" required>
                            <label class="form-check-label" for="role_freelancer">Freelancer (I want to work)</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'partials/footer.php';
?>
