<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'includes/db_connect.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepared statement ile güvenli sorgu
    $stmt = $conn->prepare("SELECT * FROM User WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['Password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['UID'];
            $_SESSION['user_name'] = $row['FName'] . ' ' . $row['LName'];

            // Rol belirleme
            $uid = $row['UID'];
            $role = "admin";

            $roleCheck = $conn->prepare("SELECT UID FROM JobSeeker WHERE UID = ?");
            $roleCheck->bind_param("i", $uid);
            $roleCheck->execute();
            if ($roleCheck->get_result()->num_rows > 0) {
                $role = "jobseeker";
            } else {
                $roleCheck = $conn->prepare("SELECT UID FROM Company WHERE UID = ?");
                $roleCheck->bind_param("i", $uid);
                $roleCheck->execute();
                if ($roleCheck->get_result()->num_rows > 0) {
                    $role = "company";
                }
            }

            $_SESSION['user_role'] = $role;

            header("Location: Dashboard/{$role}.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
    $conn->close();
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
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">User Login</h3>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>

                    <p class="text-center mt-3 mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
