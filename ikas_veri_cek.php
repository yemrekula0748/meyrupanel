
<?php
require_once 'DB.php';
session_start();
$db = new DB();

$result = $db->query("SELECT token FROM ikas WHERE id = 1");
$token = $result->fetch_assoc()['token'];

$pageRow = $db->query("SELECT sayfa FROM ikas_sayfa WHERE id = 1");
$page = (int)$pageRow->fetch_assoc()['sayfa'];
$limit = 200;

// API'den veri çek
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.myikas.com/api/v1/admin/graphql',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(array(
        "query" => "query listOrder(\$pagination: PaginationInput!) {
            listOrder(pagination: \$pagination) {
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
                        country { name }
                    }
                    orderLineItems {
                        variant { name }
                    }
                }
            }
        }",
        "variables" => array(
            "pagination" => array(
                "page" => $page,
                "limit" => $limit
            )
        )
    )),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);
$orders = $data['data']['listOrder']['data'] ?? [];

foreach ($orders as $order) {
    $siparis_no = $order['orderNumber'] ?? '';
    $toplam_fiyat = $order['totalFinalPrice'] ?? 0;
    $tarih = isset($order['createdAt']) ? date('Y-m-d', $order['createdAt'] / 1000) : '';
    $firstName = $order['billingAddress']['firstName'] ?? '';
    $lastName = $order['billingAddress']['lastName'] ?? '';
    $musteri_ismi = mb_substr(trim($firstName . ' ' . $lastName), 0, 100);
    $telefon = $order['billingAddress']['phone'] ?? '';
    $sehir = $order['billingAddress']['city']['name'] ?? '';
    $ilce = $order['billingAddress']['district']['name'] ?? '';
    $adres = $order['billingAddress']['addressLine1'] ?? '';
    $urunler = '';
    if (isset($order['orderLineItems']) && is_array($order['orderLineItems'])) {
        $urunler = implode(", ", array_map(function ($item) {
            return $item['variant']['name'] ?? '';
        }, $order['orderLineItems']));
    }

    // orderPaymentStatus'a göre kargo değeri
    $orderPaymentStatus = $order['orderPaymentStatus'] ?? '';
    $kargo = ($orderPaymentStatus === 'PAID') ? 'Bedelsiz' : 'Ödeme Şartlı';

    // Aynı sipariş zaten varsa ekleme
    $kontrol = $db->query("SELECT id FROM ikas_son WHERE siparis_no = ?", [$siparis_no], "s");
    if ($kontrol->num_rows == 0) {
        $db->query(
            "INSERT INTO ikas_son (siparis_no, toplam_fiyat, tarih, musteri_ismi, sehir, ilce, urunler, adres, telefon, kargo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$siparis_no, $toplam_fiyat, $tarih, $musteri_ismi, $sehir, $ilce, $urunler, $adres, $telefon, $kargo],
            "sdssssssss"
        );
    }
}

if (count($orders) > 0 && count($orders) == $limit) {
    $db->query("UPDATE ikas_sayfa SET sayfa = sayfa + 1 WHERE id = 1");
    echo "Sayfa $page işlendi, " . count($orders) . " kayıt çekildi ve veritabanına kaydedildi. Sonraki sayfa için tekrar çalıştırabilirsiniz.";
} elseif (count($orders) == 0) {
    echo "Bu sayfada hiç sipariş yok. Sayfa numarası değişmedi.";
} else {
    echo "Son sayfadasınız ($page). " . count($orders) . " kayıt çekildi. Sayfa numarası değişmedi.";
}
?>