
<?php
require_once 'DB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı adı oturumdan alınır
$user_name = $_SESSION['user_name'] ?? 'Kullanıcı Adı Bulunamadı';

$db = new DB();
$bugun = date('Y-m-d');

// Bugünkü siparişleri çek
$sql = "SELECT * FROM ikas_son WHERE DATE(tarih) = ?";
$result = $db->query($sql, [$bugun], "s");

while ($row = $result->fetch_assoc()) {
    // Aynı sipariş daha önce eklenmiş mi kontrol et
    $kontrol = $db->query("SELECT id FROM siparisler WHERE ikasno = ?", [$row['siparis_no']], "s");
    if ($kontrol->num_rows > 0) {
        continue; // Zaten eklenmiş, atla
    }

    // toplam_fiyat'ın noktadan öncesini al
    $odeme_sarti = explode('.', $row['toplam_fiyat'])[0];

    // Siparişi ekle (hangikargo artık $user_name'den geliyor, ikasmi eklendi)
    $ekle = $db->query(
        "INSERT INTO siparisler 
        (ikasno, musteri_ismi, musteri_adresi, siparis_tarihi, musteri_il, musteri_ilce, urunler, musteri_telefonu, hangikargo, odeme_sarti, hangisayfa, desi, kargo, ikasmi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $row['siparis_no'],
            $row['musteri_ismi'],
            $row['adres'],
            $row['tarih'],
            $row['sehir'],
            $row['ilce'],
            $row['urunler'],
            $row['telefon'],
            $user_name, // hangikargo
            $odeme_sarti,
            'iKas',
            1, // desi
            $row['kargo'], // kargo
            1 // ikasmi
        ],
        "sssssssssssisi"
    );
}

echo "Aktarım tamamlandı.";
?>