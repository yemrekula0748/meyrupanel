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
        $kod = trim($_POST['kod']); // Barkod verisini alın
        $db = new DB();

        // Barkodu güncelle
        $db->query("UPDATE ptt_kargo_barkodlari SET kod = '" . $db->escape($kod) . "' WHERE id = $id");

        // Başarı mesajı ile geri yönlendir
        header("Location: barkodlar.php?message=Barkod başarıyla güncellendi!");
        exit;
    } catch (Exception $e) {
        die("Hata: " . $e->getMessage());
    }
} else {
    header("Location: barkodlar.php");
    exit;
}
?>
