<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'DB.php';

try {
    $db = new DB();
    
    // Uygun siparişleri seç
    $sqlSelect = "
    SELECT id, sales_invoice_id 
    FROM siparisler 
    WHERE resmimi = 1 
      AND sales_invoice_id IS NOT NULL 
      AND iptalmi = 0 
      AND parasut_resmilesme_durumu = 0 
      AND (hangikargo = 'MeyruKids' OR hangikargo = 'Yunus Emre - Hepsijet')
";

                 
    $result = $db->query($sqlSelect);
    
    // Token al
    $tokenSql = "SELECT parasut_token_yunusemre FROM parasut_token WHERE id = 1 LIMIT 1";
    $tokenResult = $db->query($tokenSql);
    $tokenRow = $db->fetchAssoc($tokenResult);
    $accessToken = $tokenRow['parasut_token_yunusemre'] ?? null;
    
    if (!$accessToken) {
        throw new Exception("Access token bulunamadı!");
    }
    
    $company_id = "624505";
    $successCount = 0;
    
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
