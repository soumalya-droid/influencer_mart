<?php
require_once 'config/database.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$page_title = "Login";
$header_title = "Login to Your Account";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        redirect('login.php');
    }

    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Not sanitized to allow all characters

    // Validation
    $errors = [];
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($password)) $errors[] = 'Password is required.';

    if (empty($errors)) {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture']; // Assuming this column exists

            $_SESSION['success'] = 'Welcome back, ' . htmlspecialchars($user['username']) . '!';
            redirect('dashboard.php');
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
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
                <form method="POST" action="login.php" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter your email.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me">
                        <label class="form-check-label" for="remember_me">Remember Me</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                <a href="#">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'partials/footer.php';
?>
