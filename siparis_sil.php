<?php
require 'DB.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';
$status = '';

if (isset($_GET['id'])) {  // id parametresi URL'den alınıyor
    $id = intval($_GET['id']);
    try {
        $db = new DB();
        $db->query("DELETE FROM siparisler WHERE id = $id");
        $message = 'Silme işlemi başarılı.';
        $status = 'success';
    } catch (Exception $e) {
        $message = 'Silme işlemi başarısız. Hata: ' . $e->getMessage();
        $status = 'error';
    }
} else {
    $message = 'Geçersiz ID parametresi.';
    $status = 'error';
}

// Yönlendirme işlemi (geri dön)
header("Location: " . $_SERVER['HTTP_REFERER']);
exit(); // Yönlendirmeden sonra script'in çalışmasını durdurmak için exit() kullanılır
?>

<!-- HTML kısmı: Mesajı göster -->
<div class="alert alert-<?= $status ?>" role="alert">
    <?= $message ?>
</div>
