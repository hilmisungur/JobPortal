<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$companyID = $_SESSION['user_id'];
$jobTitleFilter = isset($_GET['jobtitle']) ? trim($_GET['jobtitle']) : '';

// Filtreli sorgu
$sql = "
    SELECT a.ApplyDate, u.FName, u.LName, u.Email, j.Title AS JobTitle
    FROM Application a
    JOIN Job j ON a.JID = j.JID
    JOIN User u ON a.UID = u.UID
    WHERE j.UID = ?
";
if ($jobTitleFilter !== '') {
    $sql .= " AND j.Title LIKE ?";
}
$sql .= " ORDER BY a.ApplyDate DESC";

$stmt = $conn->prepare($sql);

if ($jobTitleFilter !== '') {
    $likeTitle = '%' . $jobTitleFilter . '%';
    $stmt->bind_param("is", $companyID, $likeTitle);
} else {
    $stmt->bind_param("i", $companyID);
}
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

    <!-- Job Title Arama/Filtre Formu -->
    <form method="get" class="row mb-4 justify-content-center">
        <div class="col-md-4">
            <input type="text" name="jobtitle" class="form-control" placeholder="Search by Job Title..." value="<?= htmlspecialchars($jobTitleFilter) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <?php if ($jobTitleFilter !== ''): ?>
            <div class="col-md-2">
                <a href="view_applications.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        <?php endif; ?>
    </form>

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
        <div class="alert alert-info text-center">No applications found<?= $jobTitleFilter ? " for job title \"$jobTitleFilter\"" : "" ?>.</div>
    <?php endif; ?>
</div>
</body>
</html>
