<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uid'])) {
    $uidToDelete = intval($_POST['uid']);
    $currentAdminUID = $_SESSION['user_id'];

    if ($uidToDelete == $currentAdminUID) {
        $_SESSION['error_message'] = "You cannot delete your own admin account.";
        header("Location: dashboard.php");
        exit();
    }

    // 1. Başvuruları sil
    $conn->query("DELETE FROM Application WHERE UID = $uidToDelete");

    // 2. JobSeeker ve Company kayıtlarını sil
    $conn->query("DELETE FROM JobSeeker WHERE UID = $uidToDelete");
    $conn->query("DELETE FROM Company WHERE UID = $uidToDelete");

    // 3. User kaydını sil
    $stmt = $conn->prepare("DELETE FROM User WHERE UID = ?");
    $stmt->bind_param("i", $uidToDelete);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting user: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
