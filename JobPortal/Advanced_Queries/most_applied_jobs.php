<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top 5 Most Applied Jobs (Last 30 Days)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Top 5 Most Applied Jobs (Last 30 Days)</h4>
        </div>
        <div class="card-body">
            <?php
            $sql = "
                SELECT j.Title, COUNT(a.AppID) AS ApplicationCount
                FROM Application a
                JOIN Job j ON a.JID = j.JID
                WHERE a.ApplyDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY j.JID
                ORDER BY ApplicationCount DESC
                LIMIT 5
            ";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0): ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Job Title</th>
                            <th scope="col">Number of Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $rank++ ?></td>
                                <td><?= htmlspecialchars($row['Title']) ?></td>
                                <td><?= $row['ApplicationCount'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">No applications found in the last 30 days.</div>
            <?php endif; ?>
        </div>
        <div class="card-footer text-end">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
