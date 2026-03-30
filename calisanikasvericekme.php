<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'DB.php';
$db = new DB();

// Veritabanından token çekme
$sql = "SELECT token FROM ikas WHERE id = 1";
$result = $db->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "Hata: Veritabanında token bulunamadı.\n";
    exit;
}

$row = $result->fetch_assoc();
$token = $row['token'] ?? '';

if (empty($token)) {
    echo "Hata: Token alınamadı. Lütfen veritabanınızı kontrol edin.\n";
    exit;
}

// Veritabanından başlangıç sayfasını çekme
function getStartingPage($db) {
    $sql = "SELECT sayfa FROM ikas_sayfa WHERE id = 1";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    if (!isset($row['sayfa'])) {
        echo "Hata: Veritabanında başlangıç sayfası bulunamadı.\n";
        exit;
    }
    return (int)$row['sayfa'];
}

// İşlenen sayfayı güncelleme
function updateCurrentPage($db, $page) {
    $sql = "UPDATE ikas_sayfa SET sayfa = ? WHERE id = 1";
    $stmt = $db->getConn()->prepare($sql);
    $stmt->bind_param("i", $page);
    $stmt->execute();
    $stmt->close();
}

// Veritabanındaki mevcut orderNumber değerlerini çekme
function getExistingOrderNumbers($db) {
    $existingOrders = [];
    $sql = "SELECT orderNumber FROM ikas_siparisler";
    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $existingOrders[] = $row['orderNumber'];
    }

    return $existingOrders;
}

// API'den siparişleri çekme
function getOrdersFromIkas($token, $page, $limit) {
    $variables = [
        "pagination" => ["page" => (int)$page, "limit" => (int)$limit]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.myikas.com/api/v1/admin/graphql',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_POSTFIELDS => json_encode([
            "query" => "query listOrder(\$pagination: PaginationInput!) {
                listOrder(pagination: \$pagination) {
                    data {
                        id
                        orderNumber
                        totalFinalPrice
                        createdAt
                        billingAddress {
                            firstName
                            lastName
                            city { name }
                            phone
                        }
                        shippingAddress {
                            firstName
                            lastName
                            city { name }
                            district { 
                                code 
                                id 
                                name 
                            }
                            addressLine1
                            addressLine2
                        }
                        orderLineItems {
                            variant { name }
                        }
                        paymentMethods {
                            type
                        }
                        orderPaymentStatus
                        orderedAt
                    }
                }
            }",
            "variables" => $variables
        ]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo "cURL Hatası: " . curl_error($curl) . "\n";
        exit;
    }

    $data = json_decode($response, true);

    if (!$data) {
        echo "JSON Çözümleme Hatası: " . json_last_error_msg() . "\n";
        exit;
    }

    curl_close($curl);

    if (isset($data['data']['listOrder']['data'])) {
        return $data['data']['listOrder']['data'];
    }

    echo "API Yanıtı Geçersiz: " . json_encode($data) . "\n";
    return [];
}

// Siparişleri kaydetme
function saveOrdersToDB($orders, $existingOrders, $db) {
    $newRecords = 0;

    foreach ($orders as $order) {
        if (in_array($order['orderNumber'], $existingOrders)) {
            echo "Zaten Mevcut: " . $order['orderNumber'] . "\n";
            continue; // Eğer orderNumber zaten varsa atla
        }

        echo "Yeni Kayıt Ekleniyor: " . $order['orderNumber'] . "\n";

        // Adres bilgilerini birleştir
        $shippingAddress = trim($order['shippingAddress']['addressLine1'] . ' ' . ($order['shippingAddress']['addressLine2'] ?? ''));

        $sql = "INSERT INTO ikas_siparisler (order_id, orderNumber, totalFinalPrice, createdAt,
                 customer_firstname, customer_lastname, city, productNames, phone, district, shippingAddress, billingAddress, orderLineItems, paymentMethods, orderPaymentStatus, orderedAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $order['id'],
            $order['orderNumber'],
            $order['totalFinalPrice'],
            date("Y-m-d H:i:s", intval($order['createdAt'] / 1000)),
            $order['billingAddress']['firstName'] ?? '',
            $order['billingAddress']['lastName'] ?? '',
            $order['billingAddress']['city']['name'] ?? '',
            implode(", ", array_map(fn($item) => $item['variant']['name'], $order['orderLineItems'])),
            $order['billingAddress']['phone'] ?? '',
            $order['shippingAddress']['district']['name'] ?? '',
            $shippingAddress,
            json_encode($order['billingAddress'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            json_encode($order['orderLineItems'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            implode(", ", array_map(fn($method) => $method['type'], $order['paymentMethods'])),
            $order['orderPaymentStatus'] ?? '',
            date("Y-m-d H:i:s", intval($order['orderedAt'] / 1000))
        ];

        $types = "ssssssssssssssss";

        try {
            $stmt = $db->getConn()->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
			
			
			
		
			
			
			
			
			
			
			
			
			
			
			
            echo "Kayıt başarılı: " . $order['orderNumber'] . "\n";
            $stmt->close();
            $newRecords++;
        } catch (mysqli_sql_exception $e) {
            echo "Veritabanı Hatası: " . $e->getMessage() . "\n";
            exit;
        }
    }

    echo "Yeni Kayıt Sayısı: $newRecords\n";
    return $newRecords;
}

// Ana işlem döngüsü
$page = getStartingPage($db);
$limit = 1;

while (true) {
    echo "Sayfa: $page için işlem başlatılıyor...\n";

    $orders = getOrdersFromIkas($token, $page, $limit);
    if (empty($orders)) {
        echo "API'den gelen siparişler boş. Sayfa: $page\n";
        break;
    }

    echo "Kaydedilecek Siparişler: " . json_encode($orders) . "\n";

    $existingOrders = getExistingOrderNumbers($db);

    $newRecords = saveOrdersToDB($orders, $existingOrders, $db);

    if ($newRecords > 0) {
        echo "Sayfa $page işlendi, $newRecords yeni kayıt eklendi.\n";
		
		
		
		
					
		
		
		
		
		
		
		
		
		
		
		
		
    } else {
        echo "Sayfa $page işlendi, yeni kayıt bulunamadı.\n";
		
		
		
		
			
		
		
		
		
		
		
    }

    $page++;
    updateCurrentPage($db, $page);
}
?>