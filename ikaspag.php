<?php
// Veritabanı bağlantısı (DB.php kullanılıyor)


require_once 'DB.php';
$db = new DB();

// Authorization Bearer Token'ı veritabanından al
$result = $db->query("SELECT token FROM ikas WHERE id = 1");
$token = $result->fetch_assoc()['token'];

$curl = curl_init();

// Sayfa ve limit değerlerini dinamik olarak ayarlamak için değişkenler
$page = 130; // Başlangıç sayfası
$limit = 200; // Her sayfada gösterilecek kayıt sayısı

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
                    billingAddress {
                        firstName
                        lastName
                        city {
                            name
                        }
                    }
                    orderLineItems {
                        variant {
                            name
                        }
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

// Bootstrap HTML Başlangıcı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Listesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Sipariş Listesi</h2>
    <table id="orderTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Sipariş Numarası</th>
                <th>Toplam Fiyat</th>
                <th>Tarih</th>
                <th>Müşteri</th>
                <th>Şehir</th>
                <th>Ürün Adı</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($data['data']['listOrder']['data'])): ?>
                <?php foreach ($data['data']['listOrder']['data'] as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['orderNumber']); ?></td>
                        <td><?php echo htmlspecialchars($order['totalFinalPrice']); ?> TL</td>
                        <td><?php echo htmlspecialchars(date("d-m-Y H:i", $order['createdAt'] / 1000)); ?></td>
                        <td><?php echo htmlspecialchars($order['billingAddress']['firstName'] . ' ' . $order['billingAddress']['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($order['billingAddress']['city']['name']); ?></td>
                        <td>
                            <?php 
                            $productNames = array_map(function ($item) {
                                return $item['variant']['name'];
                            }, $order['orderLineItems']);
                            echo htmlspecialchars(implode(", ", $productNames)); 
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#orderTable').DataTable({
            responsive: true
        });
    });
</script>
</body>
</html>
