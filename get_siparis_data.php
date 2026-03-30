<?php
require_once 'DB.php';

$db = new DB();

// SQL Sorgusu
$sql = "
    SELECT 
        DATE(siparis_tarihi) AS gun,
        COUNT(*) AS toplam
    FROM siparisler
    WHERE siparis_tarihi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(siparis_tarihi)
    ORDER BY gun ASC
";

$result = $db->query($sql);
$data = [];

while ($row = $db->fetchAssoc($result)) {
    $data[] = [
        'gun' => $row['gun'],
        'toplam' => (int)$row['toplam']
    ];
}

// JSON Çıkışı
header('Content-Type: application/json');
echo json_encode($data);
?>
