<?php
require_once 'DB.php';

// Veritabanı bağlantısı
$db = new DB();

// Sorgu: barkod_basilma_durumu = 'Basılmamış' ve kargo_barkodu IS NOT NULL ve kargo ('Bedelsiz', 'Ücreti Alıcıdan')
$query = "
    SELECT 
        musteri_ismi, 
        musteri_telefonu, 
        musteri_adresi, 
        kargo_barkodu 
    FROM 
        siparisler
    WHERE 
        barkod_basilma_durumu = 'Basılmamış' 
        AND kargo_barkodu IS NOT NULL 
        AND kargo IN ('Bedelsiz', 'Ücreti Alıcıdan')
";

$result = $db->query($query);

// Elle girilen gönderici bilgileri
$gonderici_adi = "Yunus Emre Aydın";
$gonderici_adres = "Gönderici Adresi Buraya Gelecek";

// HTML çıktısı
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='page-break-after: always; border: 1px solid #000; padding: 10px; margin: 20px auto; width: 10cm; height: 10cm; font-family: Arial, sans-serif; box-sizing: border-box;'>";
        echo "<div style='float: left; width: 45%;'>";
        echo "<p><strong>Gönderici:</strong></p>";
        echo "<p>$gonderici_adi</p>";
        echo "<p>$gonderici_adres</p>";
        echo "</div>";

        echo "<div style='float: right; width: 45%; text-align: right;'>";
        echo "<p><strong>Alıcı:</strong></p>";
        echo "<p>{$row['musteri_ismi']}</p>";
        echo "<p><strong>Telefon:</strong> {$row['musteri_telefonu']}</p>";
        echo "<p>{$row['musteri_ismi']}</p>";
        echo "</div>";

        echo "<div style='clear: both; text-align: center; margin-top: 20px;'>";
        echo "<p><strong>ADRES:</strong> {$row['musteri_adresi']}</p>";
        echo "<p><strong>Kargo Barkodu:</strong> {$row['kargo_barkodu']}</p>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>Koşullara uygun kayıt bulunamadı.</p>";
}
