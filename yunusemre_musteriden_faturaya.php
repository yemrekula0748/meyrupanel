<?php
include_once 'DB.php';

$db = new DB();

// Parasut token almak
$tokenQuery = $db->query("SELECT parasut_token_yunusemre FROM parasut_token WHERE id = 1");
$tokenResult = $db->fetchAssoc($tokenQuery);
$access_token = $tokenResult['parasut_token_yunusemre'];

// Sipariş verilerini almak
$siparisQuery = $db->query("SELECT * FROM siparisler WHERE hangikargo = 'MeyruKids' AND parasut_id = 0");
$siparisler = [];
while ($row = $db->fetchAssoc($siparisQuery)) {
    $siparisler[] = $row;
}

foreach ($siparisler as $siparis) {
    $data = [
        'data' => [
            'type' => 'contacts',
            'attributes' => [
                'email' => 'ornek@ornek.com', // Bu alan sabit bırakılmıştır, değiştirebilirsiniz
                'account_type' => 'customer', // 'customer' yerine 'person' olarak ayarlandı
                'name' => $siparis['musteri_ismi'],
                'contact_type' => 'person',
                'tax_office' => $siparis['musteri_ilce'],
                'tax_number' => '11111111111', // Bu alan sabit bırakılmıştır, değiştirebilirsiniz
                'city' => $siparis['musteri_il'],
                'district' => $siparis['musteri_ilce'],
                'address' => $siparis['musteri_adresi'] // Bu alan sabit bırakılmıştır, değiştirebilirsiniz
            ]
        ]
    ];

    $options = [
        CURLOPT_URL => 'https://api.parasut.com/v4/624505/contacts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Hata: ' . curl_error($ch);
    } else {
        $response_data = json_decode($response, true);
        print_r($response_data);

        // parasut_id güncelleme
        $updateQuery = $db->query("UPDATE siparisler SET parasut_id = '" . $response_data['data']['id'] . "' WHERE id = '" . $siparis['id'] . "'");
        
        // yunusemre_fatura_olustur.php sayfasını çalıştırma
        $fatura_url = 'https://meyrupanel.com.tr/yunusemre_fatura_olustur_cron.php';
        $fatura_ch = curl_init();
        curl_setopt($fatura_ch, CURLOPT_URL, $fatura_url);
        curl_setopt($fatura_ch, CURLOPT_RETURNTRANSFER, true);
        $fatura_response = curl_exec($fatura_ch);
        
        if (curl_errno($fatura_ch)) {
            echo 'Fatura Oluşturma Hatası: ' . curl_error($fatura_ch);
        } else {
            echo 'Fatura Başarıyla Oluşturuldu: ' . $fatura_response;
        }

        curl_close($fatura_ch);
    }

    curl_close($ch);
}
?>
