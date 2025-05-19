<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$companyID = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT a.ApplyDate, u.FName, u.LName, u.Email, j.Title AS JobTitle
    FROM Application a
    JOIN Job j ON a.JID = j.JID
    JOIN User u ON a.UID = u.UID
    WHERE j.UID = ?
    ORDER BY a.ApplyDate DESC
");
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Applications to Your Job Postings</h2>

    <div class="mb-3 text-center">
        <a href="manage_jobs.php" class="btn btn-outline-secondary">← Back to Job Management</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Job Title</th>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Applied On</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['JobTitle']) ?></td>
                    <td><?= htmlspecialchars($row['FName'] . ' ' . $row['LName']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= $row['ApplyDate'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">No applications have been submitted yet.</div>
    <?php endif; ?>

</div>
</body>
</html>
