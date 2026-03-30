<?php
function executeCurl($url) {
    $ch = curl_init(); // cURL oturumunu başlatır
    curl_setopt($ch, CURLOPT_URL, $url); // URL'i ayarlar
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Dönen veriyi bir değişkene alır
    curl_exec($ch); // İsteği gönderir
    curl_close($ch); // cURL oturumunu kapatır
}

for ($i = 0; $i < 50; $i++) {
    // İlk URL'yi çalıştır
    executeCurl('https://semre.hpanel.com.tr/sadi_yunusemre_musteri_cron.php');
    
    // 1 saniye bekle
    sleep(1);
    
    // İkinci URL'yi çalıştır
    executeCurl('https://semre.hpanel.com.tr/yunusemre_fatura_olustur_cron.php');
    
    // 1 saniye daha bekle
    sleep(1);
}
?>
