<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Kullanıcıya yönlendirildiğini bildiren ekran (1 saniye bekletmeli)
$target = "../Admin/dashboard.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="1;url=<?= $target ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <h4 class="mb-2">Redirecting to admin dashboard...</h4>
        <p class="text-muted">If you are not redirected, <a href="<?= $target ?>">click here</a>.</p>
    </div>
</body>
</html>
