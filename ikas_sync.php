<?php
$urls = [
    'https://meyrupanel.com.tr/sadi_yunusemre_musteri_cron.php',
    'https://meyrupanel.com.tr/yunusemre_fatura_olustur_cron.php',
    'https://meyrupanel.com.tr/yunusemrehepsijet.php',
    'https://meyrupanel.com.tr/yunusemrehepsijetbedelsiz.php'
];

foreach ($urls as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    sleep(1); // Bir sonraki isteği göndermeden önce 1 saniye bekler
}
?>
