<?php
require 'db_connect.php';

$districts = [];

if (isset($_POST['city_ids'])) {
    $cityIds = $_POST['city_ids'];

    // Tek bir değer gelirse onu diziye çevir
    if (!is_array($cityIds)) {
        $cityIds = [$cityIds];
    }

    // SQL Injection'dan korunmak için integer dönüşümü
    $cityIds = array_map('intval', $cityIds);
    $ids = implode(',', $cityIds);

    $query = "SELECT id, ad, il_id FROM ilce WHERE il_id IN ($ids) ORDER BY ad";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $districts[] = [
            'id' => $row['id'],
            'name' => $row['ad'],
            'il_id' => $row['il_id']
        ];
    }
}

echo json_encode($districts);
?>
