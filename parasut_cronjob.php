<?php

// Hataları gösterme ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once 'DB.php'; // DB sınıfını dahil ediyoruz

use Parasut\Client;
use Parasut\Account;

// Parasut Client ayarları
$client = new Client([
    "client_id" => "pj6IOLrtu97oRe1qD3clLcTuVHEAgGBl4P3uaSNfdBU",
    "username" => "meyrukids@gmail.com",
    "password" => "Ebru1989",
    "grant_type" => "password",
    "redirect_uri" => "urn:ietf:wg:oauth:2.0:oob",
    'company_id' => "624505"
]);

try {
    // Veritabanı bağlantısı
    $db = new DB();
    
    // Siparişleri çek
    $sql = "SELECT * FROM siparisler WHERE kargo = 'Ödeme Şartlı' AND parasut_id = 0";
    $result = $db->query($sql);
    
    while ($order = $db->fetchAssoc($result)) {
        // Yeni müşteri verileri
        $customer = [
            'data' => [
                'type' => 'contacts',
                'attributes' => [
                    
                    'name' => $db->escape($order['musteri_ismi']), // REQUIRED
                    'contact_type' => 'person', // or company
                    'district' => $db->escape($order['musteri_ilce']),
                    'city' => $db->escape($order['musteri_il']),
                    'address' => $db->escape($order['musteri_adresi']),
                    'phone' => $db->escape($order['musteri_telefonu']),
                    'account_type' => 'customer', // REQUIRED
                    'tax_number' => '11111111111', // TC no for person
                    'tax_office' => $db->escape($order['musteri_ilce'])
                ]
            ]
        ];

        // Müşteri oluşturma isteği
        $account = new Account($client);
        $response = $account->create($customer);

        if (is_array($response)) {
            if (isset($response['data'])) {
                $customerId = $response['data']['id'];
                
                // Müşteri ID'sini veritabanına kaydet
                $updateSql = "UPDATE siparisler SET parasut_id = '" . $db->escape($customerId) . "' WHERE id = '" . $db->escape($order['id']) . "'";
                $db->query($updateSql);
                
                echo "Yeni müşteri başarıyla oluşturuldu! Müşteri ID'si: $customerId";
            } else {
                echo "Müşteri oluşturulamadı.";
                print_r($response); // Hata mesajını yazdırın
            }
        } else {
            if ($response->getStatusCode() === 201) {
                $responseData = json_decode($response->getBody(), true);
                $customerId = $responseData['data']['id'];
                
                // Müşteri ID'sini veritabanına kaydet
                $updateSql = "UPDATE siparisler SET parasut_id = '" . $db->escape($customerId) . "' WHERE id = '" . $db->escape($order['id']) . "'";
                $db->query($updateSql);
                
                echo "Yeni müşteri başarıyla oluşturuldu! Müşteri ID'si: $customerId";
            } else {
                echo "Müşteri oluşturulamadı. Durum kodu: " . $response->getStatusCode();
            }
        }
    }
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
