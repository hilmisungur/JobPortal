<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$currentAdminId = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? $conn->real_escape_string($_GET['role']) : 'all';

// Admin rolünü dropdown'dan kaldırdık (gösterilmeyecek)
$roles = [
    'jobseeker' => 'Job Seeker',
    'company' => 'Company'
];

$whereConditions = ["UID != $currentAdminId"]; // Admin kendini görmesin

if (!empty($search)) {
    $whereConditions[] = "(FName LIKE '%$search%' OR LName LIKE '%$search%')";
}

if ($roleFilter !== 'all' && isset($roles[$roleFilter])) {
    $whereConditions[] = "Role = '$roleFilter'";
}

$whereClause = implode(' AND ', $whereConditions);
$query = "SELECT UID, FName, LName, Email, Role FROM User WHERE $whereClause";
$users = $conn->query($query);

$userCount = $conn->query("SELECT COUNT(*) AS total FROM User WHERE UID != $currentAdminId")->fetch_assoc()['total'];
$jobCount = $conn->query("SELECT COUNT(*) AS total FROM Job")->fetch_assoc()['total'];
$appCount = $conn->query("SELECT COUNT(*) AS total FROM Application")->fetch_assoc()['total'];
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

    <form class="row g-3 mb-3" method="GET" action="">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>All Roles</option>
                <?php foreach ($roles as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $roleFilter === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['UID'] ?></td>
                <td><?= htmlspecialchars($row['FName'] . ' ' . $row['LName']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
                <td><?= $roles[$row['Role']] ?? 'Unknown' ?></td>
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
