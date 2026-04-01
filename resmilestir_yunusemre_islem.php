<?php
require_once 'DB.php';
$db = new DB();

$action = $_POST['action'] ?? '';

$where = "parasut_resmilesme_durumu = 0 AND iptalmi = 0 AND hangikargo = 'MeyruKids'
    AND (parasut_fatura_numarasi IS NULL OR parasut_fatura_numarasi = '')
    AND (sales_invoice_id IS NULL OR sales_invoice_id = '')
    AND resmimi = 0 AND resmilestir = 0 AND kargo = 'Ödeme Şartlı'";

if ($action === 'count') {
    $result = $db->query("SELECT COUNT(*) AS toplam FROM siparisler WHERE $where");
    $row = $result->fetch_assoc();
    echo json_encode(['count' => (int)$row['toplam']]);

} elseif ($action === 'update') {
    $db->query("UPDATE siparisler SET yunusemrekula = 1 WHERE $where");
    echo json_encode(['status' => 'success']);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek']);
}
