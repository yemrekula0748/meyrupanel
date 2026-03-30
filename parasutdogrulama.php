<?php
// API URL
$url = "https://api.parasut.com/oauth/token";

// İstek için gerekli bilgiler
$data = [
    "grant_type" => "password",
    "client_id" => "pj6IOLrtu97oRe1qD3clLcTuVHEAgGBl4P3uaSNfdBU",
    "client_secret" => "aVCq_R1DXyGHqVuO7e1rJ1QcBZb0cr8ACNtiKSgTG_o",
    "username" => "svmagrmn@hotmail.com",
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
        echo "Access Token: " . $result['access_token'];
    } else {
        echo "Hata: " . $result['error_description'];
    }
} else {
    echo "API isteği başarısız!";
}
?>
