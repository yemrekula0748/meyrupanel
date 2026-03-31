<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    $successCount = 0;
    $failCount = 0;
    $failIDs = [];

    while ($row = $db->fetchAssoc($result)) {
        $api_url = "https://api.parasut.com/v4/$company_id/e_archives";
        $data = [
            "data" => [
                "type" => "e_archives",
                "relationships" => [
                    "sales_invoice" => [
                        "data" => [
                            "id" => $row['sales_invoice_id'],
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
                'Authorization: Bearer ' . $accessToken,
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
            $updateSql = "UPDATE siparisler SET parasut_resmilesme_durumu = 1 WHERE id = ?";
            $updateResult = $db->query($updateSql, [$row['id']], 'i');
            if($updateResult) {
                $successCount++;
                echo "BAŞARILI: Fatura resmileştirildi (ID: {$row['id']})<br>";
            } else {
                $failCount++;
                $failIDs[] = $row['id'];
                echo "HATA: SQL güncellenemedi (ID: {$row['id']})<br>";
            }
        } else {
            $failCount++;
            $failIDs[] = $row['id'];
            // Hatalı kaydı işaretle
            $db->query("UPDATE siparisler SET resmilestir = -1 WHERE id = ?", [$row['id']], 'i');
            echo "HATA: Fatura resmileştirilemedi (ID: {$row['id']}) - HTTP: $httpCode<br>";
        }
    }

    echo "<br>Toplam $successCount başarılı, $failCount hatalı işlem yapıldı.";
    if ($failCount > 0) {
        echo "<br>Resmileşmeyen sipariş ID'leri: " . implode(", ", $failIDs);
    }
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // 201 (Created) veya 202 (Accepted) başarılı kabul edilir
        if ($httpCode == 201 || $httpCode == 202) {
            // Debug için yazdır
            echo "Fatura resmileştirildi (ID: {$row['id']})<br>";
            
            // SQL güncelleme ve hata kontrolü
            $updateSql = "UPDATE siparisler SET parasut_resmilesme_durumu = 1 WHERE id = ?";
            $updateResult = $db->query($updateSql, [$row['id']], 'i');
            
            if($updateResult) {
                $successCount++;
                echo "Durum güncellendi (ID: {$row['id']})<br>";
            } else {
                echo "Durum güncellenemedi (ID: {$row['id']}) - SQL Hatası<br>";
                error_log("SQL Güncelleme Hatası - ID: {$row['id']}");
            }
        } else {
            echo "API Hatası - HTTP Kodu: $httpCode (ID: {$row['id']})<br>";
            echo "API Yanıtı: $response<br>";
        }
    }
    
    echo "Toplam $successCount adet fatura resmileştirildi.";
    
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Hata: " . $e->getMessage();
    error_log("Parasut E-Arsiv Hatası: " . $e->getMessage());
}
?>
