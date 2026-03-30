<?php
require_once 'DB.php';
// Toplam sipariş sayısını döndüren fonksiyon
function getSiparisCount() {
    $db = new DB();
    try {
        $query = "SELECT COUNT(*) AS toplam FROM siparisler";
        $result = $db->query($query);
        
        if ($db->numRows($result) > 0) {
            $row = $db->fetchAssoc($result);
            return $row['toplam'];
        } else {
            return 0;
        }
    } catch (Exception $e) {
        return 0;
    }
}

// Bugünkü sipariş sayısını döndüren fonksiyon
function getBugunkuSiparisCount() {
    $db = new DB();
    try {
        $bugun = date('Y-m-d'); // Bugünün tarihi (YYYY-MM-DD formatında)
        $query = "SELECT COUNT(*) AS toplam FROM siparisler WHERE DATE(siparis_tarihi) = '$bugun'";
        $result = $db->query($query);
        
        if ($db->numRows($result) > 0) {
            $row = $db->fetchAssoc($result);
            return $row['toplam'];
        } else {
            return 0;
        }
    } catch (Exception $e) {
        return 0;
    }
}

// Bu ayki sipariş sayısını döndüren fonksiyon
function getBuAykiSiparisCount() {
    $db = new DB();
    try {
        $buAy = date('Y-m'); // Bu ayın başlangıcı (YYYY-MM formatında)
        $query = "SELECT COUNT(*) AS toplam FROM siparisler WHERE DATE_FORMAT(siparis_tarihi, '%Y-%m') = '$buAy'";
        $result = $db->query($query);
        
        if ($db->numRows($result) > 0) {
            $row = $db->fetchAssoc($result);
            return $row['toplam'];
        } else {
            return 0;
        }
    } catch (Exception $e) {
        return 0;
    }
}


// Bugününkü ciro

function bugununToplamOdeme() {
    // Veritabanı bağlantısı
    $db = new DB();

    // SQL sorgusu
    $query = "SELECT SUM(odeme_sarti) AS toplam_odeme FROM siparisler WHERE DATE(siparis_tarihi) = CURDATE()";
    $result = $db->query($query);

    // Sonuç döndürme
    if ($result && $row = $result->fetch_assoc()) {
        return $row['toplam_odeme'] ? $row['toplam_odeme'] : 0; // Null ise 0 döner
    }
    return 0; // Hata durumunda 0 döndür
}

// aylık ciro
function aylikToplamOdeme() {
    $db = new DB();
    $query = "SELECT SUM(odeme_sarti) AS toplam_odeme FROM siparisler WHERE MONTH(siparis_tarihi) = MONTH(CURDATE()) AND YEAR(siparis_tarihi) = YEAR(CURDATE())";
    $result = $db->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['toplam_odeme'] ? $row['toplam_odeme'] : 0;
    }
    return 0;
}


//kalan ptt barkodları

function bekleyenKargoSayisi() {
    $db = new DB();
    $query = "SELECT COUNT(*) AS bekleyen_kargo FROM ptt_kargo_barkodlari WHERE durum = 0";
    $result = $db->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['bekleyen_kargo'] ? $row['bekleyen_kargo'] : 0;
    }
    return 0; // Hata durumunda 0 döndür
}


//ikas sip say

function getUnprocessedOrderCount($db) {
    // SQL sorgusunu hazırlayın
    $sql = "SELECT COUNT(*) AS total FROM siparisler WHERE islem = 0 AND hangisayfa = 'ikas'";
    
    // Sorguyu çalıştır
    $result = $db->query($sql);
    
    // Sonucu al ve döndür
    if ($row = $result->fetch_assoc()) {
        return $row['total'];
    }
    
    return 0; // Eğer sonuç yoksa 0 döndür
}


// sipariş sayısı 12.01.2025 işlem klonu 0 olanlar
function getAllUnprocessedOrderCount($db) {
    // SQL sorgusunu hazırlayın
    $sql = "SELECT COUNT(*) AS total FROM siparisler WHERE islem = 0";
    
    // Sorguyu çalıştır
    $result = $db->query($sql);
    
    // Sonucu al ve döndür
    if ($row = $result->fetch_assoc()) {
        return $row['total'];
    }
    
    return 0; // Eğer sonuç yoksa 0 döndür
}





// Resmileşen sipariş sayısını döndürür
function getOfficialOrderCount($db) {
    // SQL sorgusu
    $sql = "SELECT COUNT(*) AS total FROM siparisler WHERE kargo = 'Ödeme Şartlı' AND resmilestir = 1 AND islem = 0";
    
    // Sorguyu çalıştır
    $result = $db->query($sql);
    
    // Sonucu al ve döndür
    if ($row = $result->fetch_assoc()) {
        return $row['total'];
    }
    
    return 0; // Eğer sonuç yoksa 0 döndür
}

// Resmileşmeyen sipariş sayısını döndürür
function getUnofficialOrderCount($db) {
    // SQL sorgusu
    $sql = "SELECT COUNT(*) AS total FROM siparisler WHERE kargo = 'Ödeme Şartlı' AND resmilestir = 0 AND islem = 0";
    
    // Sorguyu çalıştır
    $result = $db->query($sql);
    
    // Sonucu al ve döndür
    if ($row = $result->fetch_assoc()) {
        return $row['total'];
    }
    
    return 0; // Eğer sonuç yoksa 0 döndür
}

?>
