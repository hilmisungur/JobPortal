<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $type = $_POST['type'];

    $postDate = date('Y-m-d H:i:s'); // Saatli tarih formatı
    $uid = $_SESSION['user_id'];

    // Firma var mı kontrol et
    $check = $conn->prepare("SELECT * FROM Company WHERE UID = ?");
    $check->bind_param("i", $uid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Firma varsa ilan ekle
        $stmt = $conn->prepare("INSERT INTO Job (Title, Description, JLocation, JobType, PostDate, UID) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $title, $description, $location, $type, $postDate, $uid);

        if ($stmt->execute()) {
            $message = "Job posted successfully!";
        } else {
            $message = "Error posting job.";
        }
    } else {
        $message = "Company account not found.";
    }
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
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title mb-4">Add a New Job</h3>

            <?php if ($message): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="title" class="form-label">Job Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Job Description</label>
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" required>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Job Type</label>
                    <select class="form-select" name="type" required>
                        <option value="Full-Time">Full-Time</option>
                        <option value="Part-Time">Part-Time</option>
                        <option value="Remote">Remote</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Post Job</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
