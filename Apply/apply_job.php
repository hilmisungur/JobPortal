<?php
session_start();
include '../includes/db_connect.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
    die("Access denied. Please login first.");
}

if (isset($_GET['jid'])) {
    $jobId = intval($_GET['jid']);
    $userId = $_SESSION['user_id'];  // 🔧 DÜZELTİLEN YER
    $applyDate = date('Y-m-d');

    // Zaten başvurmuş mu?
    $checkQuery = "SELECT * FROM Application WHERE UID = ? AND JID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $jobId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../Jobseeker/view_jobs.php?status=already_applied");
        exit();
    }

    // Başvuruyu kaydet
    $insertQuery = "INSERT INTO Application (UID, JID, ApplyDate) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iis", $userId, $jobId, $applyDate);

    if ($stmt->execute()) {
        header("Location: ../Jobseeker/view_jobs.php?status=success");
        exit();
    } else {
        header("Location: ../Jobseeker/view_jobs.php?status=error");
        exit();
    }
} else {
    header("Location: ../Jobseeker/view_jobs.php?status=invalid");
    exit();
}
?>
