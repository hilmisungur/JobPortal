<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$uid = $_SESSION['user_id'];
$jid = $_GET['id'] ?? null;

if (!$jid) {
    $_SESSION['error_message'] = "Invalid job ID.";
    header("Location: manage_jobs.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM Job WHERE JID = ? AND UID = ?");
$stmt->bind_param("ii", $jid, $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['error_message'] = "Job not found or you do not have permission.";
    header("Location: manage_jobs.php");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $sector = $_POST['sector'];

    $update = $conn->prepare("UPDATE Job SET Title = ?, JobType = ?, JLocation = ?, Description = ?, Sector = ? WHERE JID = ? AND UID = ?");
    $update->bind_param("sssssii", $title, $type, $location, $description, $sector, $jid, $uid);

    if ($update->execute()) {
        $_SESSION['success_message'] = "Job updated successfully.";
        header("Location: manage_jobs.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }

    $update->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Edit Job Posting</h3>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($row['Title']) ?>" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Type</label>
                            <input type="text" name="type" value="<?= htmlspecialchars($row['JobType']) ?>" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($row['JLocation']) ?>" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="5" class="form-control" required><?= htmlspecialchars($row['Description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sector</label>
                            <select name="sector" class="form-select" required>
                                <option value="">Select Sector</option>
                                <?php
                                $sectors = ['IT', 'Education', 'Finance', 'Energy', 'Healthcare'];
                                foreach ($sectors as $s) {
                                    $selected = $row['Sector'] === $s ? 'selected' : '';
                                    echo "<option value='$s' $selected>$s</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Job</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="manage_jobs.php" class="btn btn-outline-secondary">← Back to Job Management</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
