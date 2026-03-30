<?php

$urls = [
    'https://semre.hpanel.com.tr/yunusemre_resmilestir.php',
    'https://semre.hpanel.com.tr/sevim_resmilestir.php'
];

$repeat = 6;
$interval = 10; // 10 saniye

for ($i = 0; $i < $repeat; $i++) {
    foreach ($urls as $url) {
        // GET isteği gönderme
        $response = file_get_contents($url);
        
        // İsteğin sonucunu kontrol etme
        if ($response === FALSE) {
            echo "Hata: $url'ye istek gönderilemedi.\n";
        } else {
            echo "Başarılı: $url'ye istek gönderildi.\n";
        }
    }
    // 10 saniye bekleme
    sleep($interval);
}
?>
