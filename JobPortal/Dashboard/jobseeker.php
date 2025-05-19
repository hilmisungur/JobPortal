    <?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
        header("Location: ../login.php");
        exit();
    }

    $userName = $_SESSION['user_name'];
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Job Seeker Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body text-center">
                <h2 class="mb-4">Welcome, <?= htmlspecialchars($userName) ?> <span class="text-muted">(Job Seeker)</span></h2>
                <p class="lead mb-4">You can browse jobs and track your applications.</p>

                <div class="d-grid gap-3 col-6 mx-auto">
                    <a href="../Jobseeker/view_jobs.php" class="btn btn-primary btn-lg">🔍 Browse Job Listings</a>
                    <a href="../Jobseeker/my_applications.php" class="btn btn-info btn-lg text-white">📋 My Applications</a>
                    <a href="../logout.php" class="btn btn-outline-danger btn-lg">🚪 Logout</a>
                </div>
            </div>
        </div>
    </div>

    </body>
    </html>
