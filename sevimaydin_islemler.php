<?php
// 4 farklı URL belirlenir.
$urls = [
    "https://meyrupanel.com.tr/sadi_sevim_fatura_cron.php",
    "https://meyrupanel.com.tr/sevimaydin_musteri_olusturma_cron.php",
    "https://meyrupanel.com.tr/sevimaydinpttsartliodeme.php",
    "https://meyrupanel.com.tr/sevimaydinpttbedelsizodeme.php"
];

// Toplam döngü sayısı ve bekleme süresi
$totalRequests = 58;
$intervalSeconds = 1;

// cURL işlemini başlatan bir fonksiyon
function sendCurlRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Maksimum 10 saniye bekle
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'response' => $response,
        'http_code' => $httpCode,
    ];
}

// Döngü başlatılır.
for ($i = 0; $i < $totalRequests; $i++) {
    // İstek yapılacak URL'yi seç
    $currentUrl = $urls[$i % count($urls)]; // URL'ler arasında döner.

    // İstek gönder
    $result = sendCurlRequest($currentUrl);

    // Sonucu yazdır
    echo "Request #".($i + 1)." to ".$currentUrl.": HTTP Code ".$result['http_code']."\n";

    // Döngüler arasında bekle
    if ($i < $totalRequests - 1) { // Son istekten sonra bekleme yok.
        sleep($intervalSeconds);
    }
}

echo "İşlem tamamlandı.\n";
