<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jid'])) {
    $jid = $_POST['jid'];

    $stmt1 = $conn->prepare("DELETE FROM Application WHERE JID = ?");
    $stmt1->bind_param("i", $jid);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM Job WHERE JID = ?");
    $stmt2->bind_param("i", $jid);
    $stmt2->execute();

    $stmt1->close();
    $stmt2->close();

    $_SESSION['success_message'] = "Job deleted successfully.";
    header("Location: jobs.php");
    exit();
} else {
    header("Location: jobs.php");
    exit();
}
?>
