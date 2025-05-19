<?php
session_start();
require '../includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Listings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
  <div class="row">

    <!-- Left Panel: Filters -->
    <div class="col-md-3">
      <form method="GET" class="border rounded p-3 bg-white shadow-sm">

        <h5>Location</h5>
        <div class="mb-2">
          <select name="country" class="form-select">
            <option value="">All Countries</option>
            <option value="Turkey" <?= ($_GET['country'] ?? '') == 'Turkey' ? 'selected' : '' ?>>Turkey</option>
            <option value="Germany" <?= ($_GET['country'] ?? '') == 'Germany' ? 'selected' : '' ?>>Germany</option>
          </select>
        </div>
        <div class="mb-2">
          <select name="city" class="form-select">
            <option value="">All Cities</option>
            <option value="Eskisehir" <?= ($_GET['city'] ?? '') == 'Eskisehir' ? 'selected' : '' ?>>Eskisehir</option>
            <option value="Istanbul" <?= ($_GET['city'] ?? '') == 'Istanbul' ? 'selected' : '' ?>>Istanbul</option>
          </select>
        </div>
        <div class="mb-3">
          <select name="district" class="form-select">
            <option value="">All Districts</option>
            <option value="Tepebasi" <?= ($_GET['district'] ?? '') == 'Tepebasi' ? 'selected' : '' ?>>Tepebasi</option>
            <option value="Odunpazari" <?= ($_GET['district'] ?? '') == 'Odunpazari' ? 'selected' : '' ?>>Odunpazari</option>
          </select>
        </div>

        <h5>Job Type</h5>
        <select name="type" class="form-select mb-3">
          <option value="">All Types</option>
          <?php foreach (['Full-Time', 'Part-Time', 'Remote', 'Internship'] as $type): ?>
            <option value="<?= $type ?>" <?= ($_GET['type'] ?? '') === $type ? 'selected' : '' ?>><?= $type ?></option>
          <?php endforeach; ?>
        </select>

        <h5>Sector</h5>
        <select name="sector" class="form-select mb-3">
          <option value="">All Sectors</option>
          <option value="IT" <?= ($_GET['sector'] ?? '') === 'IT' ? 'selected' : '' ?>>IT</option>
          <option value="Education" <?= ($_GET['sector'] ?? '') === 'Education' ? 'selected' : '' ?>>Education</option>
          <option value="Finance" <?= ($_GET['sector'] ?? '') === 'Finance' ? 'selected' : '' ?>>Finance</option>
          <option value="Energy" <?= ($_GET['sector'] ?? '') === 'Energy' ? 'selected' : '' ?>>Energy</option>
          <option value="Healthcare" <?= ($_GET['sector'] ?? '') === 'Healthcare' ? 'selected' : '' ?>>Healthcare</option>
        </select>

        <h5>Date</h5>
        <select name="date" class="form-select mb-3">
          <option value="">Any Time</option>
          <option value="1" <?= ($_GET['date'] ?? '') == '1' ? 'selected' : '' ?>>Today</option>
          <option value="3" <?= ($_GET['date'] ?? '') == '3' ? 'selected' : '' ?>>Last 3 days</option>
          <option value="7" <?= ($_GET['date'] ?? '') == '7' ? 'selected' : '' ?>>Last 7 days</option>
          <option value="15" <?= ($_GET['date'] ?? '') == '15' ? 'selected' : '' ?>>Last 15 days</option>
        </select>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </form>
    </div>

    <!-- Right Panel: Job Listings -->
    <div class="col-md-9">
      <h4 class="mb-3">Job Listings</h4>

      <?php
      $query = "SELECT Job.*, Company.LogoURL FROM Job 
                JOIN Company ON Job.UID = Company.UID 
                WHERE 1=1";

      if (!empty($_GET['country'])) {
        $country = $conn->real_escape_string($_GET['country']);
        $query .= " AND Job.JLocation LIKE '%$country%'";
      }
      if (!empty($_GET['city'])) {
        $city = $conn->real_escape_string($_GET['city']);
        $query .= " AND Job.JLocation LIKE '%$city%'";
      }
      if (!empty($_GET['district'])) {
        $district = $conn->real_escape_string($_GET['district']);
        $query .= " AND Job.JLocation LIKE '%$district%'";
      }
      if (!empty($_GET['type'])) {
        $type = $conn->real_escape_string($_GET['type']);
        $query .= " AND Job.JobType = '$type'";
      }
      if (!empty($_GET['sector'])) {
        $sector = $conn->real_escape_string($_GET['sector']);
        $query .= " AND Job.Sector = '$sector'";
      }
      if (!empty($_GET['date'])) {
        $days = intval($_GET['date']);
        $query .= " AND PostDate >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
      }

      $query .= " ORDER BY PostDate DESC";
      $result = $conn->query($query);
      ?>

      <?php if ($result->num_rows > 0): ?>
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
                  <p class="card-text"><small class="text-muted">Posted: <?= $row['PostDate'] ?></small></p>
                  <a href="../Apply/apply_job.php?jid=<?= $row['JID'] ?>" class="btn btn-success btn-sm float-end">Apply</a>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="alert alert-info">No job listings found for your filter.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
