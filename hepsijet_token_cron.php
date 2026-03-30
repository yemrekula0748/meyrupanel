<?php

// Hataları gösterme ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'DB.php'; // DB sınıfını dahil ediyoruz

// Veritabanı bağlantısı
$db = new DB();

for ($i = 0; $i < 5; $i++) {
    // cURL isteği
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://integration.hepsijet.com/auth/getToken',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Basic c2VtcmVidXRpa19pbnRlZ3JhdGlvbjoxNDk2OTUxMg=='
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // Yanıtı kontrol et ve token değerini al
    $responseArray = json_decode($response, true);
    if (isset($responseArray['data']['token'])) {
        $token = $responseArray['data']['token'];

        // Veritabanına token değerini kaydet
        $sql = "UPDATE hepsijet SET token = '" . $db->escape($token) . "' WHERE id = 1";
        $db->query($sql);
        
        echo "Token başarıyla kaydedildi: $token\n";
    } else {
        echo "Token alınamadı. Yanıt: $response\n";
    }

    // 10 saniye bekle
    sleep(10);
}

?>
