<?php

set_time_limit(0);
require_once 'DB.php';

function bekle($saniye) {
    if (PHP_SAPI === 'cli') {
        sleep($saniye);
    } else {
        echo str_repeat(" ", 4096); // output flush hack
        flush();
        ob_flush();
        usleep($saniye * 1000000);
    }
}

$db = new DB();
$maxKayit = 10;
$islemSayisi = 0;

while ($islemSayisi < $maxKayit) {
    // 1. Tüm işlemleri yapılmamış uygun siparişi bul
    $siparis = $db->fetchAssoc(
        $db->query("SELECT * FROM siparisler WHERE parasut_resmilesme_durumu = 0 AND iptalmi = 0 AND hangikargo = 'MeyruKids' AND (parasut_fatura_numarasi IS NULL OR parasut_fatura_numarasi = '') AND (sales_invoice_id IS NULL OR sales_invoice_id = '') AND resmimi = 0 AND resmilestir = 0 AND kargo = 'Ödeme Şartlı' AND yunusemrekula = 1 LIMIT 1")
    );
    if (!$siparis) {
        echo "Uygun kayıt bulunamadı.<br>";
        break;
    }

    // 1. Müşteri oluştur
    $tokenRow = $db->fetchAssoc($db->query("SELECT parasut_token_yunusemre FROM parasut_token WHERE id = 1"));
    $access_token = $tokenRow['parasut_token_yunusemre'];
    $data = [
        'data' => [
            'type' => 'contacts',
            'attributes' => [
                'email' => 'ornek@ornek.com',
                'account_type' => 'customer',
                'name' => $siparis['musteri_ismi'],
                'contact_type' => 'person',
                'tax_office' => $siparis['musteri_ilce'],
                'tax_number' => '11111111111',
                'city' => $siparis['musteri_il'],
                'district' => $siparis['musteri_ilce'],
                'address' => $siparis['musteri_adresi']
            ]
        ]
    ];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.parasut.com/v4/624505/contacts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Müşteri Oluşturma Hatası: ' . curl_error($ch) . "<br>";
        $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = '" . $siparis['id'] . "'");
        curl_close($ch);
        continue;
    }
    $response_data = json_decode($response, true);
    if (!isset($response_data['data']['id'])) {
        echo "Müşteri oluşturulamadı: Sipariş ID {$siparis['id']}<br>";
        $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = '" . $siparis['id'] . "'");
        curl_close($ch);
        continue;
    }
    $parasut_id = $response_data['data']['id'];
    $db->query("UPDATE siparisler SET parasut_id = '" . $parasut_id . "' WHERE id = '" . $siparis['id'] . "'");
    echo "Müşteri oluşturuldu: Sipariş ID {$siparis['id']}<br>";
    curl_close($ch);

    // 2. Fatura oluştur
    $today = date('Y-m-d');
    $today_formatted = date('dmY');
    $random_number = mt_rand(100000, 999999);
    $vat_rate = 10;
    $kdv_dahil_fiyat = $siparis['odeme_sarti'];
    $unit_price = $kdv_dahil_fiyat / (1 + ($vat_rate / 100));
    $invoice = [
        'data' => [
            'type' => 'sales_invoices',
            'attributes' => [
                'item_type' => 'invoice',
                'description' => 'Açıklama',
                'issue_date' => $today,
                'due_date' => $today,
                'invoice_series' => 'EA',
                'invoice_id' => $random_number . $today_formatted,
                'currency' => 'TRL',
            ],
            'relationships' => [
                'details' => [
                    'data' => [[
                        'type' => 'sales_invoice_details',
                        'attributes' => [
                            'quantity' => 1,
                            'unit_price' => round($unit_price, 2),
                            'vat_rate' => $vat_rate,
                            'description' => 'BAYAN GIYIM',
                        ],
                        'relationships' => [
                            'product' => [
                                'data' => [
                                    'id' => '1006666676',
                                    'type' => 'products',
                                ],
                            ],
                        ],
                    ]],
                ],
                'contact' => [
                    'data' => [
                        'id' => $parasut_id,
                        'type' => 'contacts',
                    ],
                ],
            ],
        ],
    ];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.parasut.com/v4/624505/sales_invoices',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($invoice),
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Fatura Oluşturma Hatası: ' . curl_error($ch) . "<br>";
        $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = '" . $siparis['id'] . "'");
        curl_close($ch);
        continue;
    }
    $response_data = json_decode($response, true);
    if (!isset($response_data['data']['attributes']['invoice_no']) || !isset($response_data['data']['id'])) {
        echo "Fatura oluşturulamadı: Sipariş ID {$siparis['id']}<br>";
        $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = '" . $siparis['id'] . "'");
        curl_close($ch);
        continue;
    }
    $fatura_no = $response_data['data']['attributes']['invoice_no'];
    $sales_invoice_id = $response_data['data']['id'];
    $db->query("UPDATE siparisler SET parasut_fatura_numarasi = '" . $fatura_no . "', sales_invoice_id = '" . $sales_invoice_id . "', resmimi = 1 WHERE id = '" . $siparis['id'] . "'");
    echo "Fatura oluşturuldu ve resmimi=1 yapıldı: Sipariş ID {$siparis['id']}<br>";
    curl_close($ch);

    // 3. Faturayı resmileştir
    $company_id = "624505";
    $api_url = "https://api.parasut.com/v4/$company_id/e_archives";
    $data = [
        "data" => [
            "type" => "e_archives",
            "relationships" => [
                "sales_invoice" => [
                    "data" => [
                        "id" => $sales_invoice_id,
                        "type" => "sales_invoices"
                    ]
                ]
            ]
        ]
    ];
    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode == 201 || $httpCode == 202) {
        // Yanıttan job id'yi al
        $json = json_decode($response, true);
        $jobId = $json['data']['id'] ?? null;
        $jobStatus = null;
        $jobResult = null;
        if ($jobId) {
            // Trackable job'ı sorgula (max 5 deneme, 2 sn arayla)
            for ($try = 0; $try < 5; $try++) {
                $chJob = curl_init("https://api.parasut.com/v4/624505/trackable_jobs/" . $jobId);
                curl_setopt_array($chJob, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ],
                    CURLOPT_SSL_VERIFYPEER => false
                ]);
                $jobResp = curl_exec($chJob);
                curl_close($chJob);
                $jobJson = json_decode($jobResp, true);
                $jobStatus = $jobJson['data']['attributes']['status'] ?? null;
                $jobResult = $jobJson['data']['attributes']['result'] ?? null;
                echo '<pre>Trackable Job Yanıtı ['.($try+1).']: ' . htmlspecialchars($jobResp) . '</pre>';
                if ($jobStatus === 'success' || $jobStatus === 'failed') {
                    break;
                }
                bekle(2);
            }
            if ($jobStatus === 'success') {
                $db->query("UPDATE siparisler SET parasut_resmilesme_durumu = 1 WHERE id = '" . $siparis['id'] . "'");
                echo "BAŞARILI: Fatura gerçekten resmileşti (ID: {$siparis['id']})<br>";
            } elseif ($jobStatus === 'failed') {
                echo "HATA: Parasut job başarısız oldu (ID: {$siparis['id']})<br>";
                $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = '" . $siparis['id'] . "'");
            } else {
                echo "UYARI: Parasut job halen tamamlanmadı (ID: {$siparis['id']})<br>";
            }
        } else {
            echo "UYARI: Job ID alınamadı, sadece ilk yanıt gösteriliyor.<br>";
        }
        echo '<pre>API JSON Yanıtı: ' . htmlspecialchars($response) . '</pre>';
    } else {
        echo "HATA: Fatura resmileştirilemedi (ID: {$siparis['id']}) - HTTP: $httpCode<br>";
        echo '<pre>API JSON Yanıtı: ' . htmlspecialchars($response) . '</pre>';
        $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = '" . $siparis['id'] . "'");
        continue;
    }

    $islemSayisi++;
    bekle(2);
}
?>
