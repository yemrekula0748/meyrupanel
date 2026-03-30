<?php
$store_name = 'semrebutik';
$client_id = '489f1df9-9f9e-4802-b512-8f780acf6ca0';
$client_secret = 's_l8ZD07ydZYI3NvyZaABxhjnS0ceaafa3875046e69767c15ef31f8171';

$url = "https://$store_name.myikas.com/api/admin/oauth/token";

$data = [
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['access_token'])) {
    echo "Access Token: " . $result['access_token'];
} else {
    echo "Hata: " . $result['error_description'];
}
?>
