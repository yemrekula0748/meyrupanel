<?php

// DB sınıfını dahil et
require_once 'DB.php';

// API URL
$url = "https://api.parasut.com/oauth/token";

// İstek için gerekli bilgiler
$data = [
    "grant_type" => "password",
    "client_id" => "EarLX0fCgD1U9eCDkeOZRriiymQVh2y915ASLDJRavo",
    "client_secret" => "_BmN5Hm90JSqIIqckkMnhmQzaJToz6piNRc0M1lIE58",
    "username" => "emre_ajan_007@hotmail.com",
    "password" => "230819",
    "redirect_uri" => "urn:ietf:wg:oauth:2.0:oob"
];

// CURL ile POST isteği
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

// Yanıtı işleme
if ($response) {
    $result = json_decode($response, true);
    if (isset($result['access_token'])) {
        $access_token = $result['access_token'];
        
        // DB sınıfını kullanarak veritabanına bağlan
        $db = new DB();

        // Access token'ı parasut_token tablosunda id=1 kaydına güncelle
        $sql = "UPDATE parasut_token SET parasut_token_yunusemre = ? WHERE id = 1";
        $params = [$access_token];
        $types = "s"; // 's' string veri tipi

        try {
            // Sorguyu çalıştır
            $db->query($sql, $params, $types);
            echo "Access token başarıyla kaydedildi!";
        } catch (Exception $e) {
            echo "Hata: " . $e->getMessage();
        }

    } else {
        echo "Hata: " . $result['error_description'];
    }
} else {
    echo "API isteği başarısız!";
}
?>
