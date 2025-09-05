<?php
require_once 'partials/header.php';
checkRole(['client', 'freelancer', 'admin']); // Must be logged in

$db = getDBConnection();
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        redirect('profile.php');
    }

    // Sanitize and validate inputs
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $bio = sanitizeInput($_POST['bio']);
    $skills = sanitizeInput($_POST['skills']);

    $errors = [];
    if (empty($username)) $errors[] = 'Username cannot be empty.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';

    // Check for unique username/email if changed
    if ($username !== $user['username'] || $email !== $user['email']) {
        $check_stmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $check_stmt->execute([$username, $email, $user_id]);
        if ($check_stmt->fetch()) {
            $errors[] = 'Username or email is already in use by another account.';
        }
    }

    // Handle profile picture upload
    $profile_picture_path = $user['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2 MB

        if (in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $upload_dir = 'uploads/profile_pictures/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $filename = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
            $profile_picture_path = $upload_dir . $filename;

            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path)) {
                $errors[] = 'Failed to upload profile picture.';
            }
        } else {
            $errors[] = 'Invalid file type or size. Max 2MB, JPG, PNG, GIF allowed.';
        }
    }

    if (empty($errors)) {
        // Update user data
        $sql = "UPDATE users SET username = ?, email = ?, bio = ?, skills = ?, profile_picture = ? WHERE id = ?";
        $update_stmt = $db->prepare($sql);
        $update_stmt->execute([$username, $email, $bio, $skills, $profile_picture_path, $user_id]);

        // Update password if provided
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                $_SESSION['error'] = 'New passwords do not match.';
            } elseif (strlen($_POST['new_password']) < 8) {
                $_SESSION['error'] = 'New password must be at least 8 characters long.';
            } else {
                $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $pass_stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $pass_stmt->execute([$hashed_password, $user_id]);
            }
        }

        // Update session variables
        $_SESSION['username'] = $username;
        $_SESSION['profile_picture'] = $profile_picture_path;

        $_SESSION['success'] = 'Profile updated successfully!';
        redirect('profile.php');
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}
?>

<div class="container">
    <h1 class="mb-4">Edit Profile</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'https://via.placeholder.com/150'); ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="profile.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio / Tagline</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        </div>

                        <?php if ($user['role'] === 'freelancer'): ?>
                        <div class="mb-3">
                            <label for="skills" class="form-label">Your Skills</label>
                            <input type="text" class="form-control" id="skills" name="skills" value="<?php echo htmlspecialchars($user['skills']); ?>" placeholder="e.g., PHP, JavaScript, Graphic Design">
                            <small class="form-text text-muted">Enter skills separated by commas.</small>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Change Profile Picture</label>
                            <input class="form-control" type="file" id="profile_picture" name="profile_picture">
                        </div>

                        <hr>
                        <h5 class="mb-3">Change Password</h5>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
