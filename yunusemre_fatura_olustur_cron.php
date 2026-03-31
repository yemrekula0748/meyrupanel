<?php
// Veritabanı bağlantısı
include_once 'DB.php';
$db = new DB();

// Günün tarihini almak
$today = date('Y-m-d');
$today_formatted = date('dmY');
$random_number = mt_rand(100000, 999999);

// Access token almak
$tokenQuery = $db->query("SELECT parasut_token_yunusemre FROM parasut_token WHERE id = 1");
$tokenResult = $db->fetchAssoc($tokenQuery);
$access_token = $tokenResult['parasut_token_yunusemre'];

// Sipariş verilerini almak
$siparisQuery = $db->query("SELECT * 
FROM siparisler 
WHERE (hangikargo = 'MeyruKids' OR hangikargo = 'Yunus Emre - Hepsijet') 
  AND parasut_id != 0 
  AND (parasut_fatura_numarasi IS NULL OR parasut_fatura_numarasi = '') 
  AND kargo = 'Ödeme Şartlı' 
  AND resmimi = 1 
LIMIT 3");
$siparisler = [];
while ($row = $db->fetchAssoc($siparisQuery)) {
    $siparisler[] = $row;
}

foreach ($siparisler as $siparis) {
    // KDV oranı
    $vat_rate = 10; // %10 KDV

    // KDV dahil fiyat (siparişin her şey dahil fiyatı)
    $kdv_dahil_fiyat = $siparis['odeme_sarti'];

    // KDV hariç fiyat hesaplama
    $unit_price = $kdv_dahil_fiyat / (1 + ($vat_rate / 100));

    // KDV tutarı hesaplama
    $calculated_vat = $unit_price * ($vat_rate / 100);

    // Fatura detaylarını hazırlama
    $invoice = [
        'data' => [
            'type' => 'sales_invoices', // Fatura türü
            'attributes' => [
                'item_type' => 'invoice', // Fatura türü
                'description' => 'Açıklama', // Fatura açıklaması
                'issue_date' => $today, // Fatura tarihi
                'due_date' => $today, // Ödeme tarihi
                'invoice_series' => 'MEY', // Fatura serisi
                'invoice_id' => $random_number . $today_formatted, // Benzersiz Fatura Numarası
                'currency' => 'TRL', // Para birimi
            ],
            'relationships' => [
                'details' => [
                    'data' => [
                        [
                            'type' => 'sales_invoice_details',
                            'attributes' => [
                                'quantity' => 1, // Ürün miktarı
                                'unit_price' => round($unit_price, 2), // KDV hariç fiyat
                                'vat_rate' => $vat_rate, // %10 KDV
                                'description' => 'BAYAN GIYIM', // Ürün açıklaması
                            ],
                            'relationships' => [
                                'product' => [
                                    'data' => [
                                        'id' => '1006666676', // Parasut'taki ürün ID'si
                                        'type' => 'products',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'contact' => [
                    'data' => [
                        'id' => $siparis['parasut_id'], // Parasut'taki müşteri ID'si
                        'type' => 'contacts', // Sabit bir değer
                    ],
                ],
            ],
        ],
    ];

    // CURL istek ayarları
    $options = [
        CURLOPT_URL => 'https://api.parasut.com/v4/624505/sales_invoices',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($invoice),
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Hata: ' . curl_error($ch);
    } else {
        $response_data = json_decode($response, true);
        print_r($response_data);

        // Geri dönen invoice_no'yu kaydetmek
        $updateQuery = $db->query( "UPDATE siparisler SET parasut_fatura_numarasi = '" . $response_data['data']['attributes']['invoice_no'] . "', sales_invoice_id = '" . $response_data['data']['id'] . "' WHERE id = '" . $siparis['id'] . "'" );
    }

    curl_close($ch);
}
?>
