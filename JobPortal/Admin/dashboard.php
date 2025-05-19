<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$currentAdminId = $_SESSION['user_id'];

// Kullanıcı sayısı
$userCount = $conn->query("SELECT COUNT(*) AS total FROM User WHERE UID != $currentAdminId")->fetch_assoc()['total'];
$jobCount = $conn->query("SELECT COUNT(*) AS total FROM Job")->fetch_assoc()['total'];
$appCount = $conn->query("SELECT COUNT(*) AS total FROM Application")->fetch_assoc()['total'];

// Kullanıcı listesi (admin hariç)
$users = $conn->query("SELECT UID, FName, LName, Email FROM User WHERE UID != $currentAdminId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center mb-4">Admin Control Panel</h2>

    <nav class="mb-4">
        <a href="jobs.php" class="btn btn-outline-primary me-2">📄 All Jobs</a>
        <a href="applications.php" class="btn btn-outline-primary me-2">📥 All Applications</a>
        <a href="../logout.php" class="btn btn-outline-danger">🚪 Logout</a>
    </nav>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <div class="row text-center mb-4">
        <div class="col-md-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <p class="display-6"><?= $userCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h5>Total Jobs</h5>
                    <p class="display-6"><?= $jobCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h5>Total Applications</h5>
                    <p class="display-6"><?= $appCount ?></p>
                </div>
            </div>
        </div>
    </div>

    <h3>Registered Users</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['UID'] ?></td>
                <td><?= htmlspecialchars($row['FName'] . ' ' . $row['LName']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
                <td>
                    <form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                        <input type="hidden" name="uid" value="<?= $row['UID'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
