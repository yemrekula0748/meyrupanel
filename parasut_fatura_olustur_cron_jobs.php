<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/vendor/autoload.php';

use Parasut\Client;
use Parasut\Invoice;

// Parasut API istemcisini başlatıyoruz
$client = new Client([
    "client_id" => "EarLX0fCgD1U9eCDkeOZRriiymQVh2y915ASLDJRavo", // Parasut uygulamanızın Client ID'si
    "username" => "emre_ajan_007@hotmail.com",  // Parasut kullanıcı adınız (e-posta)
    "password" => "230819", // Parola
    "grant_type" => "password", // Sabit bir değer
    "redirect_uri" => "urn:ietf:wg:oauth:2.0:oob", // Sabit bir değer
    'company_id' => "50038" // Şirket ID'niz
]);

// Veritabanı bağlantısını oluştur
$db = new DB();

// Koşullara uyan siparişleri getir
$query = "
    SELECT * FROM siparisler 
    WHERE faturalandirma_durumu = 'Faturalandırılmadı' 
      AND hangikargo = 'Yunus Emre - PTT' 
      AND kargo = 'Ödeme Şartlı'
";
$siparisler = $db->query($query);

// Eğer faturalandırılacak sipariş yoksa
if ($siparisler->num_rows == 0) {
    echo "Faturalandırılacak sipariş bulunamadı.";
    exit;
}

while ($siparis = $siparisler->fetch_assoc()) {
    // Güncel tarih
    $currentDate = date('Y-m-d');
    // Fatura numarası oluştur
    $randomNumber = rand(100000, 999999);
    $invoiceId = $randomNumber . date('dmY');

    // Birim fiyatı hesapla (ödeme_sarti %10 eksiği)
    $unitPrice = $siparis['odeme_sarti'] * 0.9;

    // Parasut API'ye gönderilecek fatura bilgisi
    $invoice = [
        'data' => [
            'type' => 'sales_invoices',
            'attributes' => [
                'item_type' => 'invoice',
                'description' => 'Açıklama', // Burayı istediğiniz açıklama ile değiştirebilirsiniz
                'issue_date' => $currentDate,
                'due_date' => $currentDate,
                'invoice_series' => 'SM',
                'invoice_id' => $invoiceId,
                'currency' => 'TRL'
            ],
            'relationships' => [
                'details' => [
                    'data' => [
                        [
                            'type' => 'sales_invoice_details',
                            'attributes' => [
                                'quantity' => 1,
                                'unit_price' => $unitPrice,
                                'vat_rate' => 10,
                                'description' => 'BAYAN GIYIM'
                            ],
                            'relationships' => [
                                'product' => [
                                    'data' => [
                                        'id' => '63211935',
                                        'type' => 'products'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'contact' => [
                    'data' => [
                        'id' => $siparis['parasut_id'], // Sipariş tablosundan Parasut ID'yi kullanıyoruz
                        'type' => 'contacts'
                    ]
                ]
            ]
        ]
    ];

    try {
        // Fatura oluşturma işlemi
        $invoiceResponse = $client->call(Invoice::class)->create($invoice);

        // Fatura ID'sini alıyoruz
        $generatedInvoiceId = "SM" . $invoice['data']['attributes']['invoice_id'];

        // Oluşturulan fatura bilgilerini veritabanına kaydet
        $updateQuery = "
            UPDATE siparisler 
            SET parasut_fatura_numarasi = '$generatedInvoiceId', 
                faturalandirma_durumu = 'Faturalandı' 
            WHERE id = {$siparis['id']}
        ";
        $db->query($updateQuery);

        echo "Fatura başarıyla oluşturuldu: \n";
        print_r($invoiceResponse);
    } catch (Exception $e) {
        echo "Fatura oluşturulurken hata oluştu: " . $e->getMessage();
    }
}
?>
