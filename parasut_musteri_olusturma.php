<?php
// Access Token ve Firma ID'nizi ekleyin
$accessToken = "WzaVnc_-ibr7kQwZy-KlM24GrzWR6fBNw8fjxLUs3MU";
$companyId = "624505";

<?php

$accessToken = 'WzaVnc_-ibr7kQwZy-KlM24GrzWR6fBNw8fjxLUs3MU';  // Erişim tokeni
$companyId = '624505';  // Şirket ID'si

$newCustomer = [
    'data' => [
        'type' => 'contacts',
        'attributes' => [
            'contact_type' => 'customer',
            'name' => 'Sadi Bacanak',
            'email' => 'yeni@example.com',
            'phone' => '5555555555',
            // Diğer müşteri bilgileri
        ]
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.parasut.com/v4/$companyId/contacts");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newCustomer));

$headers = [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Hata: ' . curl_error($ch);
} else {
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($responseCode === 201) {
        echo "Yeni müşteri başarıyla oluşturuldu!";
    } else {
        echo "Müşteri oluşturulamadı. Durum kodu: " . $responseCode;
    }
}

curl_close($ch);

?>
