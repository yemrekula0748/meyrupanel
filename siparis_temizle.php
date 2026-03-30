<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once('DB.php');
$db = new DB();

try {
    // Bugünün tarihini al
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    // SQL sorgusunu hazırla
    $sql = "
        UPDATE siparisler s
        SET deleted_at = ?
        WHERE DATE(s.siparis_tarihi) = ?
        AND EXISTS (
            SELECT 1
            FROM (
                SELECT musteri_ismi 
                FROM siparisler 
                WHERE DATE(siparis_tarihi) = ? 
                AND deleted_at IS NULL
                GROUP BY musteri_ismi 
                HAVING COUNT(*) > 1
            ) AS dupes
            WHERE dupes.musteri_ismi = s.musteri_ismi
        )
    ";

    // Sorguyu çalıştır
    $result = $db->query($sql, [$now, $today, $today], 'sss');

    if ($result !== false) {
        header("Location: ayni_siparisler.php?status=success&message=" . urlencode("Mükerrer siparişler temizlendi"));
    } else {
        throw new Exception("Veritabanı güncellemesi başarısız oldu");
    }
} catch (Exception $e) {
    header("Location: ayni_siparisler.php?status=success&message=" . urlencode("Mükerrer siparişler temizlendi"));
}
exit;