<?php
include "db_config.php"; // Kendi bağlantı ayarlarını buradan alır

// Port tanımlıysa 5 parametreli, değilse 4 parametreli bağlantı kullan
if (isset($port)) {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
} else {
    $conn = new mysqli($servername, $username, $password, $dbname);
}

$conn->set_charset("utf8mb4");

// Hata kontrolü
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("We are currently experiencing technical difficulties. Please try again later.");
}
?>