<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT j.Title, j.JobType, j.JLocation, j.PostDate, a.ApplyDate
    FROM Job j
    JOIN Application a ON j.JID = a.JID
    WHERE a.UID = ?
    ORDER BY a.ApplyDate DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">My Job Applications</h2>

    <div class="mb-3 text-center">
        <a href="../Dashboard/jobseeker.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Job Title</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Posted On</th>
                    <th>Applied On</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Title']) ?></td>
                    <td><?= htmlspecialchars($row['JobType']) ?></td>
                    <td><?= htmlspecialchars($row['JLocation']) ?></td>
                    <td><?= $row['PostDate'] ?></td>
                    <td><?= $row['ApplyDate'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">
            You have not applied to any jobs yet.
        </div>
    <?php endif; ?>

</div>
</body>
</html>
