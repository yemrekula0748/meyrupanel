<?php
/**
 * meyru_ikas_vericek.php
 * MeyruKids iKas API'den tüm siparişleri çekip meyru_ikas_son tablosuna kaydeder.
 * Cron veya AJAX ile çağrılabilir.
 */

require_once 'DB.php';
$db = new DB();

set_time_limit(300);
header('Content-Type: application/json; charset=utf-8');

// Token — ikas tablosundan id=2 (MeyruKids)
$tokenRow     = $db->query("SELECT token FROM ikas WHERE id = 2")->fetch_assoc();
$access_token = $tokenRow['token'] ?? '';

if (empty($access_token)) {
    echo json_encode(['status' => 'error', 'message' => 'Token bulunamadi (ikas id=2 bos)']);
    exit;
}

// Tablo yoksa oluştur
$db->query("CREATE TABLE IF NOT EXISTS meyru_ikas_son (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    ikas_uuid     VARCHAR(100) NOT NULL UNIQUE,
    siparis_no    VARCHAR(50),
    musteri_ismi  VARCHAR(255),
    telefon       VARCHAR(50),
    adres         TEXT,
    sehir         VARCHAR(100),
    ilce          VARCHAR(100),
    urunler       TEXT,
    toplam_fiyat  DECIMAL(10,2),
    odeme_durumu  VARCHAR(50),
    odeme_yontemi VARCHAR(150),
    tarih         DATETIME,
    ekleme_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_siparis_no (siparis_no),
    INDEX idx_tarih (tarih)
)");

// GraphQL sorgusu — sayfalama için tek sorgu (sort top-level)
$query = <<<'GQL'
query listOrder($pagination: PaginationInput!) {
    listOrder(pagination: $pagination, sort: "createdAt:DESC") {
        count
        data {
            id
            orderNumber
            totalFinalPrice
            createdAt
            orderPaymentStatus
            billingAddress {
                firstName
                lastName
                phone
                addressLine1
                city { name }
                district { name }
            }
            orderLineItems {
                variant { name }
                quantity
            }
            paymentMethods {
                type
                paymentGatewayName
            }
        }
    }
}
GQL;

$limit      = 200;
$page       = 1;
$totalPages = 1;
$islemSayisi = 0;

do {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://api.myikas.com/api/v1/admin/graphql',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'query'     => $query,
            'variables' => ['pagination' => ['page' => $page, 'limit' => $limit]],
        ]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 60,
    ]);

    $response  = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        echo json_encode(['status' => 'error', 'message' => 'cURL hata (sayfa ' . $page . '): ' . $curlError]);
        exit;
    }

    $data = json_decode($response, true);

    if (isset($data['errors'])) {
        $errMsg = $data['errors'][0]['message'] ?? 'Bilinmeyen API hatasi';
        echo json_encode(['status' => 'error', 'message' => 'API hata (sayfa ' . $page . '): ' . $errMsg]);
        exit;
    }

    $orders     = $data['data']['listOrder']['data']  ?? [];
    $totalCount = (int)($data['data']['listOrder']['count'] ?? 0);
    $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $limit) : 1;

    foreach ($orders as $order) {
        $firstName = $order['billingAddress']['firstName'] ?? '';
        $lastName  = $order['billingAddress']['lastName']  ?? '';
        $musteri   = trim($firstName . ' ' . $lastName);

        $urunler = [];
        foreach ($order['orderLineItems'] ?? [] as $item) {
            $ad   = $item['variant']['name'] ?? '?';
            $adet = $item['quantity'] ?? 1;
            $urunler[] = $ad . ' x' . $adet;
        }
        $urunlerStr = implode(', ', $urunler);

        $pmType = '';
        $pmList = $order['paymentMethods'] ?? [];
        if (!empty($pmList)) {
            $pmType = $pmList[0]['type'] ?? '';
            $gwName = $pmList[0]['paymentGatewayName'] ?? '';
            if ($gwName) $pmType .= ' (' . $gwName . ')';
        }

        $tarihMs = $order['createdAt'] ?? 0;
        $tarih   = $tarihMs ? date('Y-m-d H:i:s', (int)($tarihMs / 1000)) : null;

        $db->query(
            "INSERT INTO meyru_ikas_son
             (ikas_uuid, siparis_no, musteri_ismi, telefon, adres, sehir, ilce, urunler, toplam_fiyat, odeme_durumu, odeme_yontemi, tarih)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               siparis_no    = VALUES(siparis_no),
               musteri_ismi  = VALUES(musteri_ismi),
               telefon       = VALUES(telefon),
               adres         = VALUES(adres),
               sehir         = VALUES(sehir),
               ilce          = VALUES(ilce),
               urunler       = VALUES(urunler),
               toplam_fiyat  = VALUES(toplam_fiyat),
               odeme_durumu  = VALUES(odeme_durumu),
               odeme_yontemi = VALUES(odeme_yontemi),
               tarih         = VALUES(tarih)",
            [
                $order['id'],
                $order['orderNumber'] ?? '',
                $musteri,
                $order['billingAddress']['phone'] ?? '',
                $order['billingAddress']['addressLine1'] ?? '',
                $order['billingAddress']['city']['name'] ?? '',
                $order['billingAddress']['district']['name'] ?? '',
                $urunlerStr,
                $order['totalFinalPrice'] ?? 0,
                $order['orderPaymentStatus'] ?? '',
                $pmType,
                $tarih,
            ],
            "ssssssssdsss"
        );

        $islemSayisi++;
    }

    $page++;

} while ($page <= $totalPages);

$toplamDB = $db->query("SELECT COUNT(*) AS c FROM meyru_ikas_son")->fetch_assoc()['c'] ?? 0;

echo json_encode([
    'status'  => 'success',
    'message' => $islemSayisi . ' siparis islendi. Veritabaninda toplam ' . $toplamDB . ' kayit var.',
    'toplam'  => $toplamDB,
    'islem'   => $islemSayisi,
]);
