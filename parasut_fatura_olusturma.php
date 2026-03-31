<?php
// Parasut API'yi kullanarak fatura oluşturma örneği
// Bu kodda müşteriyi ve fatura detaylarını Parasut sistemine göndermek için gerekli adımlar bulunmaktadır.

require_once __DIR__ . '/vendor/autoload.php';

use Parasut\Client;
use Parasut\Invoice;

// Parasut API istemcisini başlatıyoruz
$client = new Client([
    "client_id" => "pj6IOLrtu97oRe1qD3clLcTuVHEAgGBl4P3uaSNfdBU", // Parasut uygulamanızın Client ID'si
    "username" => "meyrukids@gmail.com",  // Parasut kullanıcı adınız (e-posta)
    "password" => "Ebru1989", // Parola
    "grant_type" => "password", // Sabit bir değer
    "redirect_uri" => "urn:ietf:wg:oauth:2.0:oob", // Sabit bir değer
    'company_id' => "624505" // Şirket ID'niz
]);

// Fatura oluşturmak için gerekli bilgiler
$invoice = [
    'data' => [
       'type' => 'sales_invoices', // Fatura türü ("sales_invoices" sabit bir değerdir)
       'attributes' => [
           'item_type' => 'invoice', // Fatura türü ("invoice" sabit bir değerdir)
           'description' => 'Açıklama', // Fatura açıklaması
           'issue_date' => '2024-12-30', // Fatura tarihi (Yıl-Ay-Gün formatında olmalı)
           'due_date' => '2024-12-30', // Ödeme tarihi
           'invoice_series' => 'MEY', // Fatura serisi
           'invoice_id' => '123456730122024', // Fatura numarası
           'currency' => 'TRL' // Para birimi ("TRL" Türk Lirası için kullanılır)
       ],
       'relationships' => [
           // Fatura detayları
           'details' => [
               'data' => [
                   [
                       'type' => 'sales_invoice_details',
                       'attributes' => [
                           'quantity' => 1, // Ürün miktarı
                           'unit_price' => 29.90, // Birim fiyat
                           'vat_rate' => 18, // KDV oranı
                           'description' => 'BAYAN GIYIM' // Ürün açıklaması
                       ],
                       "relationships" => [
                           "product" => [
                               "data" => [
                                   "id" => "1006666676", // Parasut'taki ürün ID'si
                                   "type" => "products" // Sabit bir değer
                               ]
                           ]
                       ]
                   ],
                   
               ],
           ],
           // Müşteri bilgileri
           'contact' => [
               'data' => [
                   'id' => '168900028', // Parasut'taki müşteri ID'si
                   'type' => 'contacts' // Sabit bir değer
               ]
           ]
       ],
    ]
];

// Fatura oluşturma işlemi
try {
    $invoiceResponse = $client->call(Invoice::class)->create($invoice);
    echo "Fatura başarıyla oluşturuldu: \n";
    print_r($invoiceResponse); // Oluşturulan faturanın detaylarını yazdırır
} catch (Exception $e) {
    echo "Fatura oluşturulurken hata oluştu: " . $e->getMessage();
}
