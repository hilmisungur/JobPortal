<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'jobseeker') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #ced4da;
            padding: 4px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="../Dashboard/jobseeker.php" class="btn btn-outline-primary">
            <span class="me-1">🏠</span> Home
        </a>
    </div>

    <div class="row">
        <!-- Sol Panel: Filtreleme -->
        <div class="col-md-3">
            <form method="GET" class="border rounded p-3 bg-white shadow-sm" id="filterForm">
                <h5>Filter Jobs</h5>

                <!-- İl -->
                <div class="mb-3">
                    <label for="city" class="form-label">City (İl)</label>
                    <select id="city" name="city[]" class="form-select js-example-basic-multiple" multiple="multiple" onchange="loadDistricts()">
                        <?php
                        $selectedCities = $_GET['city'] ?? [];
                        $cityQuery = $conn->query("SELECT * FROM il ORDER BY ad");
                        while ($city = $cityQuery->fetch_assoc()) {
                            $selected = in_array($city['id'], $selectedCities) ? 'selected' : '';
                            echo "<option value='{$city['id']}' $selected>{$city['ad']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- İlçe -->
                <div class="mb-3">
                    <label for="district" class="form-label">District (İlçe)</label>
                    <select id="district" name="district[]" class="form-select js-example-basic-multiple" multiple="multiple">
                        <!-- Seçilen illere göre ilçeler AJAX ile yüklenecek -->
                    </select>
                </div>

                <!-- Sıralama -->
                <div class="mb-3">
                    <label for="sort" class="form-label">Sort by:</label>
                    <select class="form-select" name="sort" id="sort">
                        <option value="latest" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'latest') ? 'selected' : '' ?>>Latest</option>
                        <option value="popularity" <?= (isset($_GET['sort']) && $_GET['sort'] == 'popularity') ? 'selected' : '' ?>>Popularity</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>

        <!-- Sağ Panel: İş İlanları -->
        <div class="col-md-9">
            <h4 class="mb-3">Job Listings</h4>

            <?php if (isset($_SESSION['apply_feedback'])): ?>
                <div class="alert alert-<?= $_SESSION['apply_feedback']['type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['apply_feedback']['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['apply_feedback']); ?>
            <?php endif; ?>

            <?php
            // Filtreleri al ve integer diziye çevir
            $cityFilter = isset($_GET['city']) ? array_map('intval', $_GET['city']) : [];
            $districtFilter = isset($_GET['district']) ? array_map('intval', $_GET['district']) : [];
            $sort = $_GET['sort'] ?? 'latest';

            // SQL WHERE cümlesi
            $where = "1";
            if (!empty($cityFilter)) {
                $ids = implode(",", $cityFilter);
                $where .= " AND j.il_id IN ($ids)";
            }
            if (!empty($districtFilter)) {
                $ids = implode(",", $districtFilter);
                $where .= " AND j.ilce_id IN ($ids)";
            }

            // Sıralama
            if ($sort == 'popularity') {
                $query = "
                    SELECT j.*, c.LogoURL, COUNT(a.AID) AS app_count 
                    FROM Job j
                    JOIN Company c ON j.UID = c.UID
                    LEFT JOIN Application a ON j.JID = a.JID
                    WHERE $where
                    GROUP BY j.JID
                    ORDER BY app_count DESC, j.PostDate DESC
                ";
            } else {
                $query = "
                    SELECT j.*, c.LogoURL,
                    (SELECT COUNT(*) FROM Application a WHERE a.JID = j.JID) AS app_count
                    FROM Job j
                    JOIN Company c ON j.UID = c.UID
                    WHERE $where
                    ORDER BY j.PostDate DESC
                ";
            }

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
                                    <p class="card-text">
                                        <small class="text-muted">Posted on: <?= date("Y-m-d", strtotime($row['PostDate'])) ?> • <?= $row['app_count'] ?> Applications</small>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-multiple').select2();

        <?php if (!empty($selectedCities)): ?>
        loadDistricts(<?= json_encode($selectedCities) ?>, <?= json_encode($_GET['district'] ?? []) ?>);
        <?php endif; ?>
    });

function loadDistricts(selectedCities = null, selectedDistricts = []) {
    let cities = selectedCities || $('#city').val();

    $.ajax({
        url: '../includes/load_districts.php',
        method: 'POST',
        data: { city_ids: cities },
        success: function (data) {
            $('#district').html('');
            const districts = JSON.parse(data);
            for (let i in districts) {
                // selectedDistricts değerlerini string'e çevirerek karşılaştır
                let selected = selectedDistricts.map(String).includes(String(districts[i].id)) ? 'selected' : '';
                $('#district').append(`<option value="${districts[i].id}" ${selected}>${districts[i].name}</option>`);
            }
            $('#district').trigger('change');
        }
    });
}


    function confirmApply(url) {
        if (confirm("Are you sure you want to apply for this job?")) {
            window.location.href = url;
        }
    }
</script>
</body>
</html>
