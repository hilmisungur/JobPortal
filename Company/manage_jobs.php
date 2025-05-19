<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$uid = $_SESSION['user_id'];
$jobs = $conn->prepare("SELECT JID, Title, JobType, JLocation, PostDate FROM Job WHERE UID = ?");
$jobs->bind_param("i", $uid);
$jobs->execute();
$result = $jobs->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">My Posted Jobs</h2>

    <!-- Buton grubu: Home Page - Add New Job - Logout -->
    <div class="mb-3 text-center">
        <a href="../Dashboard/company.php" class="btn btn-outline-primary me-2">
            <span class="me-1">🏠</span> Home Page
        </a>
        <a href="add_job.php" class="btn btn-primary me-2">➕ Add New Job</a>
        <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Date Posted</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Title']) ?></td>
                    <td><?= htmlspecialchars($row['JobType']) ?></td>
                    <td><?= htmlspecialchars($row['JLocation']) ?></td>
                    <td><?= $row['PostDate'] ?></td>
                    <td>
                        <a href="edit_job.php?id=<?= $row['JID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                    <td>
                        <form method="POST" action="delete_job.php" onsubmit="return confirm('Are you sure you want to delete this job?');">
                            <input type="hidden" name="jid" value="<?= $row['JID'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">You haven't posted any jobs yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
