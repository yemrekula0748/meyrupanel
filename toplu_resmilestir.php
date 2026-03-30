<?php
session_start();
require_once 'DB.php';

$db = new DB();
$response = ['success' => false, 'message' => ''];

$sql = "UPDATE siparisler 
        SET resmilestir = 1 
        WHERE resmilestir = 0 
        AND iptalmi = 0 
        AND parasut_resmilesme_durumu = 0 
        AND islem = 0 
        LIMIT 50";


if($db->query($sql)) {
    $affected = $db->affectedRows();
    $response = [
        'success' => true,
        'message' => "$affected adet sipariş resmileştirildi."
    ];
}

header('Content-Type: application/json');
echo json_encode($response);