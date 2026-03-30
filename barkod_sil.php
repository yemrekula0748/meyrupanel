<?php
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'DB.php'; // Veritabanı bağlantısını dahil edin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = intval($_POST['id']); // ID'yi alın
        $db = new DB();

        // Barkodu sil
        $db->query("DELETE FROM ptt_kargo_barkodlari WHERE id = $id");

        // Başarı mesajı ile geri yönlendir
        header("Location: barkodlar.php?message=Barkod başarıyla silindi!");
        exit;
    } catch (Exception $e) {
        die("Hata: " . $e->getMessage());
    }
} else {
    header("Location: barkodlar.php");
    exit;
}
?>
