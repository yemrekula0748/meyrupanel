<?php
require_once 'DB.php';

$db = new DB();
$response = ['success' => false];

if(isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $sql = "UPDATE siparisler SET iptalmi = 1 WHERE id = ?";
    
    if($db->query($sql, [$id], 'i')) {
        $response['success'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);