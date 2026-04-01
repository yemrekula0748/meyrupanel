<?php
require_once 'DB.php';

$store_name    = 'meyrukids'; // iKas mağaza adını buraya yaz (örn: meyrukids)
$client_id     = 'd77fdc51-f738-483f-9489-a0d62c7623a1';
$client_secret = 's_HY2KNGjr3PpOB10esPQb1onT1a84e2da75d4405bb53e0e5d88e7d526';

$url = "https://$store_name.myikas.com/api/admin/oauth/token";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'grant_type'    => 'client_credentials',
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "cURL Hatası: $curlError";
    exit;
}

$result = json_decode($response, true);

if (isset($result['access_token'])) {
    $access_token = $result['access_token'];

    $db = new DB();
    $escaped = $db->escape($access_token);
    $db->query("UPDATE ikas SET token = '$escaped' WHERE id = 1");

    echo "Token başarıyla kaydedildi.";
} else {
    $hata = $result['error_description'] ?? $result['error'] ?? $response;
    echo "Token alınamadı: $hata";
}
