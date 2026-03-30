<?php
require_once 'DB.php';

$response = ['status' => 'error', 'message' => 'İşlem başarısız oldu.'];

try {
    $db = new DB();
    $updateQuery = "UPDATE siparisler SET islem = 1 WHERE islem = 0";
    $updatedRows = $db->query($updateQuery);

    if ($updatedRows) {
        $response['status'] = 'success';
        $response['message'] = 'Siparişler başarıyla yedeklendi ve boşaltıldı.';
    }
} catch (Exception $e) {
    $response['message'] = 'Bir hata oluştu: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);