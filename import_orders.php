<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'DB.php';
$db = new DB();

// Veritabanından token çekme
$sql = "SELECT token FROM ikas WHERE id = 1";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$token = $row['token'] ?? '';

// -- BURADA 2 AY ÖNCESİNİ HESAPLIYORUZ (milisaniye cinsinden) --
$twoMonthsAgo = strtotime("-2 months") * 1000; 
// strtotime("-2 months") = 2 ay öncenin "Unix epoch" (saniye)
//   1000 ile çarparak milisaniyeye çeviriyoruz

// API'den siparişleri çekme fonksiyonu
function getOrdersFromIkas($token, $page, $limit, $twoMonthsAgo) {
    $curl = curl_init();

    // Sorguya ekleyeceğimiz filter parametresi
    //   orderedAt: { gte: $twoMonthsAgo }
    $postData = [
        "query" => "query listOrder(\$pagination: PaginationInput!, \$filter: OrderFilterInput) {
            listOrder(pagination: \$pagination, filter: \$filter) {
                data {
                    id
                    orderNumber
                    totalFinalPrice
                    createdAt
                    billingAddress {
                        firstName
                        lastName
                        city { name }
                    }
                    orderLineItems {
                        variant { name }
                    }
                }
            }
        }",
        "variables" => [
            "pagination" => [
                "page" => $page,
                "limit" => $limit
            ],
            "filter" => [
                // Sadece son 2 ayın siparişlerini çekecek şekilde
                "orderedAt" => [
                    "gte" => $twoMonthsAgo
                ]
            ]
        ]
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.myikas.com/api/v1/admin/graphql',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo "cURL Hatası: " . curl_error($curl);
        exit;
    }

    $data = json_decode($response, true);

    if (!$data) {
        echo "JSON Çözümleme Hatası: " . json_last_error_msg();
        exit;
    }

    curl_close($curl);

    if (isset($data['data']['listOrder']['data'])) {
        return $data['data']['listOrder']['data'];
    }

    return [];
}

// Veritabanına kayıt fonksiyonu
function saveOrdersToDB($orders, $db) {
    foreach ($orders as $order) {
        $sql = "INSERT INTO ikas_siparisler (order_id, orderNumber, totalFinalPrice, createdAt,
                 customer_firstname, customer_lastname, city, productNames)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    orderNumber = VALUES(orderNumber),
                    totalFinalPrice = VALUES(totalFinalPrice),
                    createdAt = VALUES(createdAt),
                    customer_firstname = VALUES(customer_firstname),
                    customer_lastname = VALUES(customer_lastname),
                    city = VALUES(city),
                    productNames = VALUES(productNames)";

        $params = [
            $order['id'],
            $order['orderNumber'],
            $order['totalFinalPrice'],
            date("Y-m-d H:i:s", intval($order['createdAt'] / 1000)), // Milisaniyeden saniyeye
            $order['billingAddress']['firstName'] ?? '',
            $order['billingAddress']['lastName'] ?? '',
            $order['billingAddress']['city']['name'] ?? '',
            implode(", ", array_map(fn($item) => $item['variant']['name'], $order['orderLineItems']))
        ];
        $types = "ssdsssss";

        try {
            $stmt = $db->getConn()->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Veritabanı Hatası: " . $e->getMessage();
            exit;
        }
    }
}

// Verileri döngüyle çekip kaydet
$page = 1;
$limit = 200;

while (true) {
    $orders = getOrdersFromIkas($token, $page, $limit, $twoMonthsAgo);

    if (empty($orders)) {
        break;
    }

    saveOrdersToDB($orders, $db);

    // Eğer çekilen sipariş sayısı limitin altındaysa, sayfa arttırmaya gerek kalmaz
    if (count($orders) < $limit) {
        break;
    }

    $page++;
}

echo "Son 2 ayın verileri başarıyla import edildi!";
?>
