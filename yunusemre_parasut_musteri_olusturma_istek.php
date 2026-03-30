<?php

for ($i = 0; $i < 20; $i++) {
    // cURL işlemi başlat
    $curl = curl_init();
    
    // cURL ayarları
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://semre.hpanel.com.tr/sadi_yunusemre_musteri_cron.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10, // Maksimum bekleme süresi
    ));
    
    // cURL isteğini gerçekleştir
    $response = curl_exec($curl);
    
    // cURL işlemini kapat
    curl_close($curl);
    
    // 3 saniye beklet
    sleep(3);
}

?>
