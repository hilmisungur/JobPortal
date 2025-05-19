<?php
$servername = "localhost";
$username = "root";
$password = ""; // Yerel sunucu için varsayılan; canlı sistemde değiştirin
$dbname = "job_portal";


// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname   );
$conn->set_charset("utf8mb4");

// Hata kontrolü
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    // Kullanıcıya hata gösterilmez, yönlendirme yapılabilir
    die("We are currently experiencing technical difficulties. Please try again later.");
}
?>
