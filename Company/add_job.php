<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $il_id = $_POST['il_id'];
    $ilce_id = $_POST['ilce_id'];
    $job_type = $_POST['job_type'];

    // İl ve ilçe adlarını al
    $ilName = '';
    $ilceName = '';
    $ilQuery = $conn->query("SELECT ad FROM il WHERE id = $il_id");
    if ($ilQuery && $ilQuery->num_rows > 0) {
        $ilName = $ilQuery->fetch_assoc()['ad'];
    }

    $ilceQuery = $conn->query("SELECT ad FROM ilce WHERE id = $ilce_id");
    if ($ilceQuery && $ilceQuery->num_rows > 0) {
        $ilceName = $ilceQuery->fetch_assoc()['ad'];
    }

    $location = $ilceName . ' • ' . $ilName;

    // ✅ Şirket daha önce eklenmemişse otomatik oluştur
    $companyCheck = $conn->prepare("SELECT UID FROM company WHERE UID = ?");
    $companyCheck->bind_param("i", $uid);
    $companyCheck->execute();
    $companyResult = $companyCheck->get_result();

    if ($companyResult->num_rows === 0) {
        $insertCompany = $conn->prepare("INSERT INTO company (UID, Location, LogoURL) VALUES (?, ?, ?)");
        $defaultLogo = "images/default_logo.png";
        $insertCompany->bind_param("iss", $uid, $location, $defaultLogo);
        $insertCompany->execute();
        $insertCompany->close();
    }
    $companyCheck->close();

    // İş ilanını ekle
    $stmt = $conn->prepare("INSERT INTO Job (UID, Title, Description, JLocation, JobType, PostDate, il_id, ilce_id)
                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("issssii", $uid, $title, $description, $location, $job_type, $il_id, $ilce_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_jobs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title mb-4">Add a New Job</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Job Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Job Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">City (İl)</label>
                    <select id="il" name="il_id" class="form-select" required>
                        <option value="">Select City</option>
                        <?php
                        $ilQuery = $conn->query("SELECT * FROM il ORDER BY ad");
                        while ($il = $ilQuery->fetch_assoc()) {
                            echo "<option value='{$il['id']}'>{$il['ad']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">District (İlçe)</label>
                    <select id="ilce" name="ilce_id" class="form-select" required>
                        <option value="">Select District</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Job Type</label>
                    <select name="job_type" class="form-select" required>
                        <option value="Full-Time">Full-Time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Internship">Internship</option>
                        <option value="Remote">Remote</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Post Job</button>
            </form>
        </div>
    </div>
</div>

<script>
    $('#il').on('change', function () {
        const il_id = $(this).val();
        $('#ilce').html('<option>Loading...</option>');

        $.post('../includes/load_districts.php', { city_ids: [il_id] }, function (data) {
            const districts = JSON.parse(data);
            $('#ilce').html('<option value="">Select District</option>');
            for (let i in districts) {
                $('#ilce').append(`<option value="${districts[i].id}">${districts[i].name}</option>`);
            }
        });
    });
</script>

</body>
</html>
