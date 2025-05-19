<?php
session_start();
include '../includes/db_connect.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
    die("Access denied. Please login first.");
}

if (isset($_GET['jid'])) {
    $jobId = intval($_GET['jid']);
    $userId = $_SESSION['user_id'];
    $applyDate = date('Y-m-d');

    // Daha önce başvuru yapılmış mı kontrolü
    $checkQuery = "SELECT * FROM Application WHERE UID = ? AND JID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $jobId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['apply_feedback'] = ['type' => 'warning', 'message' => 'You have already applied to this job.'];
        header("Location: ../Jobseeker/view_jobs.php");
        exit();
    }

    // Yeni başvuru ekle
    $insertQuery = "INSERT INTO Application (UID, JID, ApplyDate) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iis", $userId, $jobId, $applyDate);

    if ($stmt->execute()) {
        $_SESSION['apply_feedback'] = ['type' => 'success', 'message' => 'Application submitted successfully!'];
    } else {
        $_SESSION['apply_feedback'] = ['type' => 'danger', 'message' => 'Failed to apply. Please try again.'];
    }

    header("Location: ../Jobseeker/view_jobs.php");
    exit();
} else {
    $_SESSION['apply_feedback'] = ['type' => 'danger', 'message' => 'Invalid job selection.'];
    header("Location: ../Jobseeker/view_jobs.php");
    exit();
}
?>
