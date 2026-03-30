<?php

// API URL
$url = 'https://api.myikas.com/api/v1/admin/graphql';

// Authorization token
$accessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjQ4OWYxZGY5LTlmOWUtNDgwMi1iNTEyLThmNzgwYWNmNmNhMCIsImVtYWlsIjoidGVzdCIsImZpcnN0TmFtZSI6InRlc3QiLCJsYXN0TmFtZSI6IiIsInN0b3JlTmFtZSI6InNlbXJlYnV0aWsiLCJtZXJjaGFudElkIjoiZGQ1NDViZmUtYjM1NS00ZmFmLTk0ZTktNTEwMjZkZmNhNjRiIiwiZmVhdHVyZXMiOlsxMSwyLDQsOV0sImF1dGhvcml6ZWRBcHBJZCI6IjQ4OWYxZGY5LTlmOWUtNDgwMi1iNTEyLThmNzgwYWNmNmNhMCIsInR5cGUiOjQsImV4cCI6MTczNTUxODY3ODY1MCwiaWF0IjoxNzM1NTA0Mjc4NjUwLCJpc3MiOiJkZDU0NWJmZS1iMzU1LTRmYWYtOTRlOS01MTAyNmRmY2E2NGIiLCJzdWIiOiI0ODlmMWRmOS05ZjllLTQ4MDItYjUxMi04Zjc4MGFjZjZjYTAifQ.XcTu3e5bgzwmjfGyQuGJZ7ELD5XU5MZsojmcbQuG8BE';

// Veri yükü
$data = [
    'query' => '{me { id }}'
];

// cURL başlat
$ch = curl_init($url);

// cURL ayarları
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Yanıtı döndür
curl_setopt($ch, CURLOPT_POST, true); // POST isteği
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // JSON formatında veri

// İstek gönder ve yanıtı al
$response = curl_exec($ch);

// Hata kontrolü
if (curl_errno($ch)) {
    echo 'cURL Hatası: ' . curl_error($ch);
} else {
    // Yanıtı göster
    echo $response;
}

// cURL kapat
curl_close($ch);

?>
