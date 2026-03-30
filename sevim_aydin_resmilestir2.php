<?php
// Paraşüt API anahtarınızı burada tanımlayın
$api_key = 'nM2UhFf_dzBbQyBZWSBbBLeCTKF_PGdpsh7DordfyG4';
$base_url = 'https://api.parasut.com/v4/438580';

// E-Arşiv fatura bilgilerini içeren bir veri dizisi oluşturun
$invoice_data = [
    'data' => [
        'type' => 'e_archives',
        'attributes' => [
            'description' => 'Örnek E-Arşiv Fatura',
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'item_type' => 'invoice',
            'internet_sale' => true,
            'internet_sale_description' => 'Online Satış',
            'internet_sale_payment_type' => 'credit_card',
            'internet_sale_payment_platform' => 'YOUR_PAYMENT_PLATFORM',
            'internet_sale_payment_date' => date('Y-m-d')
        ],
        'relationships' => [
            'contact' => [
                'data' => [
                    'id' => '169926774',
                    'type' => 'contacts'
                ]
            ]
        ]
    ]
];

// Curl ile POST isteği gönderin
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base_url/e_archives");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoice_data));
$response = curl_exec($ch);
curl_close($ch);

$invoice = json_decode($response, true);

// Yanıtı kontrol edin
if (isset($invoice['data']['id'])) {
    echo "E-Arşiv faturası başarıyla oluşturuldu! Fatura ID: " . $invoice['data']['id'];
} else {
    echo "E-Arşiv faturası oluşturulurken bir hata oluştu.";
}
?>
