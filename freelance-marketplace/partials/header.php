<?php
require_once 'config/database.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">FreelanceHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="project_list.php">Projects</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRole == 'freelancer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="my_bids.php">My Bids</a>
                            </li>
                        <?php elseif ($userRole == 'client'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="my_projects.php">My Projects</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="post_project.php">Post Project</a>
                            </li>
                        <?php elseif ($userRole == 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    Admin
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="admin_dashboard.php">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="manage_users.php">Manage Users</a></li>
                                    <li><a class="dropdown-item" href="manage_projects.php">Manage Projects</a></li>
                                    <li><a class="dropdown-item" href="manage_categories.php">Manage Categories</a></li>
                                    <li><a class="dropdown-item" href="manage_payments.php">Manage Payments</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="notifications.php">
                                Notifications
                                <?php
                                if ($isLoggedIn) {
                                    $db = getDBConnection();
                                    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $unreadCount = $stmt->fetchColumn();
                                    if ($unreadCount > 0) {
                                        echo '<span class="badge bg-danger">' . $unreadCount . '</span>';
                                    }
                                }
                                ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo $_SESSION['profile_picture'] ?? 'https://via.placeholder.com/40'; ?>" class="profile-img me-2" alt="Profile">
                                <?php echo htmlspecialchars($username); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <?php if ($userRole == 'freelancer'): ?>
                                    <li><a class="dropdown-item" href="earnings.php">Earnings</a></li>
                                <?php elseif ($userRole == 'client'): ?>
                                    <li><a class="dropdown-item" href="payments.php">Payments</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-4">
        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
