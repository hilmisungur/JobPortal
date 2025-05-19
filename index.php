<?php
session_start();

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: Dashboard/admin.php");
            break;
        case 'company':
            header("Location: Dashboard/company.php");
            break;
        case 'jobseeker':
            header("Location: Dashboard/jobseeker.php");
            break;
        default:
            echo "<p>Unknown role detected. Please contact system administrator.</p>";
            exit();
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Portal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container text-center mt-5">
    <h1 class="mb-4">Welcome to <span class="text-primary">Job Portal</span></h1>
    <p class="lead mb-5">Find your next opportunity. Connect with top companies or talented professionals.</p>

    <a href="login.php" class="btn btn-primary btn-lg me-3">Login</a>
    <a href="register.php" class="btn btn-outline-secondary btn-lg">Register</a>

    <footer class="mt-5 text-muted">
        <p>&copy; <?= date("Y") ?> Job Portal. All rights reserved.</p>
    </footer>
</div>

</body>
</html>
