<?php

// Hataları gösterme ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use Parasut\Client;
use Parasut\Account;

$client = new Client([
    "client_id" => "pj6IOLrtu97oRe1qD3clLcTuVHEAgGBl4P3uaSNfdBU",
    "username" => "meyrukids@gmail.com",
    "password" => "Ebru1989",
    "grant_type" => "password",
    "redirect_uri" => "urn:ietf:wg:oauth:2.0:oob",
    'company_id' => "624505"
]);

try {
    // Yeni müşteri verileri
    $customer = [
        'data' => [
            'type' => 'contacts',
            'attributes' => [
                'email' => 'sevinc8590@gmail.com',
                'name' => 'yunus emre bacanak', // REQUIRED
                
                'contact_type' => 'person', // or company
                'district' => 'Kepez',
                'city' => 'Antalya',
                'address' => 'yeni emek',
                'phone' => '05436763863',
                'account_type' => 'customer', // REQUIRED
                'tax_number' => '11111111111', // TC no for person
                'tax_office' => 'kepez'
            ]
        ]
    ];

    // Müşteri oluşturma isteği
    $account = new Account($client);
    $response = $account->create($customer);

    if (is_array($response)) {
        // Yanıt array ise, durumu kontrol edin
        if (isset($response['data'])) {
            echo "Yeni müşteri başarıyla oluşturuldu!";
            echo "Müşteri ID'si: " . $response['data']['id'];
        } else {
            echo "Müşteri oluşturulamadı.";
            print_r($response); // Hata mesajını yazdırın
        }
    } else {
        // Yanıt nesne ise, durumu kontrol edin
        if ($response->getStatusCode() === 201) {
            echo "Yeni müşteri başarıyla oluşturuldu!";
            $responseData = json_decode($response->getBody(), true);
            echo "Müşteri ID'si: " . $responseData['data']['id'];
        } else {
            echo "Müşteri oluşturulamadı. Durum kodu: " . $response->getStatusCode();
        }
    }
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
