<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$jobs = $conn->query("
    SELECT j.JID, j.Title, j.JobType, j.JLocation, j.PostDate, u.Email AS CompanyEmail
    FROM Job j
    JOIN User u ON j.UID = u.UID
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Job Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="mb-4">All Published Jobs</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success text-center">
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Location</th>
                <th>Posted By</th>
                <th>Date</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $jobs->fetch_assoc()): ?>
            <tr>
                <td><?= $row['JID'] ?></td>
                <td><?= htmlspecialchars($row['Title']) ?></td>
                <td><?= $row['JobType'] ?></td>
                <td><?= $row['JLocation'] ?></td>
                <td><?= $row['CompanyEmail'] ?></td>
                <td><?= $row['PostDate'] ?></td>
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
</div>

</body>
</html>
