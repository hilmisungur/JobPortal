<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jid'])) {
    $jid = $_POST['jid'];
    $date = date("Y-m-d");

    // Kullanıcının daha önce başvurup başvurmadığını kontrol et
    $check = $conn->prepare("SELECT * FROM Application WHERE UID = ? AND JID = ?");
    $check->bind_param("ii", $uid, $jid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "You have already applied for this job.";
    } else {
        $insert = $conn->prepare("INSERT INTO Application (UID, JID, ApplyDate) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $uid, $jid, $date);
        if ($insert->execute()) {
            $_SESSION['success_message'] = "Your application was submitted successfully.";
        } else {
            $_SESSION['error_message'] = "Application failed: " . $conn->error;
        }
        $insert->close();
    }

    $check->close();
    $conn->close();

    header("Location: ../Jobseeker/view_jobs.php");
    exit();
} else {
    header("Location: ../Jobseeker/view_jobs.php");
    exit();
}
?>
