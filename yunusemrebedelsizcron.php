<?php
$url = 'https://meyrupanel.com.tr/sadi_yunusemre_ptt_bedelsiz_odeme.php'; // Veya uzaktaysa tam URL
$tekrar_sayisi = 28;
$bekleme_suresi = 2; // saniye

for ($i = 1; $i <= $tekrar_sayisi; $i++) {
    echo "[$i] $url çağrılıyor...\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Maksimum bekleme süresi
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Yanıt kodu: $httpCode\n";

    if ($i < $tekrar_sayisi) {
        sleep($bekleme_suresi);
    }
}

echo "İşlem tamamlandı.\n";
?>
