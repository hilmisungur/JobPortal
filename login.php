<?php
session_start();
require 'includes/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT UID, FName, LName, Password, Role FROM User WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['Password'])) {
            // Başarılı giriş
            $_SESSION['user_id'] = $user['UID'];
            $_SESSION['user_name'] = $user['FName'] . ' ' . $user['LName'];
            $_SESSION['user_role'] = $user['Role'];

            // Role bazlı yönlendirme
            if ($user['Role'] === 'admin') {
                header("Location: Admin/dashboard.php");
            } elseif ($user['Role'] === 'company') {
                header("Location: Dashboard/company.php");
            } elseif ($user['Role'] === 'jobseeker') {
                header("Location: Dashboard/jobseeker.php");
            } else {
                $error = "Unrecognized user role.";
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="mx-auto p-4 bg-white shadow rounded" style="max-width: 400px;">
        <h3 class="mb-4 text-center">Login</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

</body>
</html>
