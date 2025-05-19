<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmApply(url) {
            if (confirm("Are you sure you want to apply for this job?")) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="row">

        <!-- SOL PANEL: Filtre -->
        <div class="col-md-3">
            <form method="GET" class="border rounded p-3 bg-white shadow-sm">
                <h5>Sort Options</h5>
                <div class="mb-3">
                    <label for="sort" class="form-label">Sort by:</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="">Latest</option>
                        <option value="popularity" <?= (isset($_GET['sort']) && $_GET['sort'] === 'popularity') ? 'selected' : '' ?>>Popularity</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>

        <!-- SAĞ PANEL: İş İlanları -->
        <div class="col-md-9">
            <h4 class="mb-3">Job Listings</h4>

            <?php
            if (isset($_GET['sort']) && $_GET['sort'] === 'popularity') {
                $query = "
                    SELECT j.*, c.LogoURL, COUNT(a.AID) AS ApplicationCount
                    FROM Job j
                    JOIN Company c ON j.UID = c.UID
                    LEFT JOIN Application a ON j.JID = a.JID
                    GROUP BY j.JID
                    ORDER BY ApplicationCount DESC
                ";
            } else {
                $query = "
                    SELECT j.*, c.LogoURL, COUNT(a.AID) AS ApplicationCount
                    FROM Job j
                    JOIN Company c ON j.UID = c.UID
                    LEFT JOIN Application a ON j.JID = a.JID
                    GROUP BY j.JID
                    ORDER BY j.PostDate DESC
                ";
            }

            $result = $conn->query($query);
            ?>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="row g-0">
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <img src="../<?= $row['LogoURL'] ?? 'images/default_logo.png' ?>" width="60" class="img-fluid rounded">
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['Title']) ?></h5>
                                    <p class="card-text mb-1">
                                        <strong><?= htmlspecialchars($row['JobType']) ?></strong> • <?= htmlspecialchars($row['JLocation']) ?>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Posted on: <?= date('Y-m-d', strtotime($row['PostDate'])) ?> • <?= $row['ApplicationCount'] ?> Applications
                                        </small>
                                    </p>
                                    <button onclick="confirmApply('../Apply/apply_job.php?jid=<?= $row['JID'] ?>')" class="btn btn-success btn-sm float-end">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">No job listings found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
