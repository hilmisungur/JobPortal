<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$redirect = "";
switch ($_SESSION['user_role']) {
    case 'admin':
        $redirect = "Dashboard/admin.php";
        break;
    case 'company':
        $redirect = "Dashboard/company.php";
        break;
    case 'jobseeker':
        $redirect = "Dashboard/jobseeker.php";
        break;
    default:
        $redirect = "login.php";
        break;
}

// Yönlendirme + kısa HTML çıkışı (dilersen)
echo "<html><head>
        <meta http-equiv='refresh' content='0;url=$redirect'>
        <title>Redirecting...</title>
      </head><body>
      <p>Redirecting to your dashboard...</p>
      </body></html>";
exit;
?>
