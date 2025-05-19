<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user_id'];
    $title = $_POST['title'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $sector = $_POST['sector'];
    $postDate = date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO Job (UID, Title, JobType, JLocation, Description, Sector, PostDate)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $uid, $title, $type, $location, $description, $sector, $postDate);

    if ($stmt->execute()) {
        echo "<script>alert('Job posting added successfully.'); window.location.href='manage_jobs.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Add New Job Posting</h2>
    <form method="POST" action="add_job.php" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Job Type</label>
            <input type="text" name="type" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="5" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Sector</label>
            <select name="sector" class="form-select" required>
                <option value="">Select Sector</option>
                <option value="IT">IT</option>
                <option value="Education">Education</option>
                <option value="Finance">Finance</option>
                <option value="Energy">Energy</option>
                <option value="Healthcare">Healthcare</option>
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success">Add Job</button>
        </div>
    </form>
</div>
</body>
</html>
