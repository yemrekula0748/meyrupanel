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

while (true) {
    $islemYapildi = false;

    // 1. Müşteri oluşturulmamış sipariş (listele_meyrukids_odemeli_bekleyen.php ile aynı filtreler)
    $siparis = $db->fetchAssoc(
        $db->query("SELECT * FROM siparisler WHERE parasut_resmilesme_durumu = 0 AND iptalmi = 0 AND hangikargo = 'MeyruKids' AND (parasut_fatura_numarasi IS NULL OR parasut_fatura_numarasi = '') AND (sales_invoice_id IS NULL OR sales_invoice_id = '') AND resmimi = 0 AND resmilestir = 0 AND kargo = 'Ödeme Şartlı' AND parasut_id = 0 LIMIT 1")
    );
    if ($siparis) {
        // Parasut token
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
        } else {
            $response_data = json_decode($response, true);
            if (isset($response_data['data']['id'])) {
                $parasut_id = $response_data['data']['id'];
                $db->query("UPDATE siparisler SET parasut_id = '" . $parasut_id . "' WHERE id = '" . $siparis['id'] . "'");
                echo "Müşteri oluşturuldu: Sipariş ID {$siparis['id']}<br>";
            } else {
                echo "Müşteri oluşturulamadı: Sipariş ID {$siparis['id']}<br>";
            }
        }
        curl_close($ch);
        $islemYapildi = true;
        bekle(2);
        continue;
    }

    // 2. Faturası oluşturulmamış, müşterisi olan sipariş
    $siparis = $db->fetchAssoc(
        $db->query("SELECT * FROM siparisler WHERE (hangikargo = 'MeyruKids' OR hangikargo = 'Yunus Emre - Hepsijet') AND parasut_id != 0 AND (parasut_fatura_numarasi IS NULL OR parasut_fatura_numarasi = '') AND kargo = 'Ödeme Şartlı' AND resmimi = 1 LIMIT 1")
    );
    if ($siparis) {
        $tokenRow = $db->fetchAssoc($db->query("SELECT parasut_token_yunusemre FROM parasut_token WHERE id = 1"));
        $access_token = $tokenRow['parasut_token_yunusemre'];
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
                    'invoice_series' => 'MEY',
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
                            'id' => $siparis['parasut_id'],
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
        } else {
            $response_data = json_decode($response, true);
            if (isset($response_data['data']['attributes']['invoice_no']) && isset($response_data['data']['id'])) {
                $fatura_no = $response_data['data']['attributes']['invoice_no'];
                $sales_invoice_id = $response_data['data']['id'];
                $db->query("UPDATE siparisler SET parasut_fatura_numarasi = '" . $fatura_no . "', sales_invoice_id = '" . $sales_invoice_id . "', resmimi = 1 WHERE id = '" . $siparis['id'] . "'");
                echo "Fatura oluşturuldu ve resmimi=1 yapıldı: Sipariş ID {$siparis['id']}<br>";
            } else {
                echo "Fatura oluşturulamadı: Sipariş ID {$siparis['id']}<br>";
            }
        }
        curl_close($ch);
        $islemYapildi = true;
        bekle(2);
        continue;
    }

    // 3. Resmileştirilmemiş, faturası olan sipariş
    $siparis = $db->fetchAssoc(
        $db->query("SELECT id, sales_invoice_id FROM siparisler WHERE resmimi = 1 AND sales_invoice_id IS NOT NULL AND iptalmi = 0 AND parasut_resmilesme_durumu = 0 AND (hangikargo = 'MeyruKids' OR hangikargo = 'Yunus Emre - Hepsijet') LIMIT 1")
    );
    if ($siparis) {
        $tokenRow = $db->fetchAssoc($db->query("SELECT parasut_token_yunusemre FROM parasut_token WHERE id = 1"));
        $access_token = $tokenRow['parasut_token_yunusemre'];
        $company_id = "624505";
        $api_url = "https://api.parasut.com/v4/$company_id/e_archives";
        $data = [
            "data" => [
                "type" => "e_archives",
                "relationships" => [
                    "sales_invoice" => [
                        "data" => [
                            "id" => $siparis['sales_invoice_id'],
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
            $db->query("UPDATE siparisler SET parasut_resmilesme_durumu = 1 WHERE id = '" . $siparis['id'] . "'");
            echo "Fatura resmileştirildi: Sipariş ID {$siparis['id']}<br>";
        } else {
            echo "Fatura resmileştirilemedi: Sipariş ID {$siparis['id']}<br>";
        }
        $islemYapildi = true;
        bekle(2);
        continue;
    }

    // Hiçbir işlem yapılmadıysa
    if (!$islemYapildi) {
        echo "Uygun kayıt bulunamadı.<br>";
        break;
    }
}
?>
