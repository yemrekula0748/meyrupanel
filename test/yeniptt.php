<?php
// WSDL dosyasının URL'si
$wsdl = "https://pttws.ptt.gov.tr/PttVeriYukleme/services/Sorgu?wsdl";

try {
    // SOAP istemcisini oluştur
    $client = new SoapClient($wsdl, [
        'trace' => true,       // İstek ve yanıtı görüntülemek için
        'exceptions' => true,  // Hata durumlarında istisna atmak için
        'cache_wsdl' => WSDL_CACHE_NONE, // WSDL önbelleğini devre dışı bırak
    ]);

    // Kabul ekle için parametreler
    $params = [
        'input' => [
            'kullanici' => 'pttws',
            'sifre' => 'JQkoqaMXDzK0I1YIHR4jA',
            'musteriId' => 704992342,
            'dosyaAdi' => 'kargo_dosyasi',
            'gonderiTip' => 'Paket',
            'gonderiTur' => 'Normal',
            'dongu' => [
                [
                    'aAdres' => 'Alici adresi',
                    'aliciAdi' => 'Alici Ad Soyad',
                    'agirlik' => 5, // kilogram cinsinden
                    'teslim_tip' => 'Kapıda Ödeme',
                ],
            ],
        ],
    ];

    // İlgili SOAP metodu çağır
    $response = $client->__soapCall('kabulEkle', [$params]);

    // Yanıtı görüntüle
    echo "<pre>";
    print_r($response);
    echo "</pre>";
} catch (Exception $e) {
    // Hata durumunda çıktıyı görüntüle
    echo "Hata: " . $e->getMessage();
}
?>
