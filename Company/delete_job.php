<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jid'])) {
    $jid = $_POST['jid'];
    $uid = $_SESSION['user_id'];

    $stmt1 = $conn->prepare("DELETE FROM Application WHERE JID = ?");
    $stmt1->bind_param("i", $jid);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("DELETE FROM Job WHERE JID = ? AND UID = ?");
    $stmt2->bind_param("ii", $jid, $uid);

    if ($stmt2->execute()) {
        $_SESSION['success_message'] = "Job deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting job: " . $conn->error;
    }

    $stmt2->close();
    $conn->close();

    header("Location: manage_jobs.php");
    exit();
} else {
    header("Location: manage_jobs.php");
    exit();
}
?>
