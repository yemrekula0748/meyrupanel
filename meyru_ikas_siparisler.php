<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'DB.php';
$db = new DB();

// Token'ı veritabanından al
$tokenRow = $db->query("SELECT token FROM ikas WHERE id = 1")->fetch_assoc();
$access_token = $tokenRow['token'] ?? '';

// Filtreler
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 50;

// GraphQL sorgusu
$query = <<<'GQL'
query listOrder($pagination: PaginationInput!) {
    listOrder(pagination: $pagination) {
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
                finalPrice
            }
        }
    }
}
GQL;

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
]);

$response  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

$orders   = [];
$apiError = '';

if ($curlError) {
    $apiError = "cURL Hatası: $curlError";
} else {
    $data = json_decode($response, true);
    if (isset($data['errors'])) {
        $apiError = $data['errors'][0]['message'] ?? 'Bilinmeyen API hatası';
    } else {
        $orders = $data['data']['listOrder']['data'] ?? [];
    }
}

$paymentLabels = [
    'WAITING'   => ['Bekliyor',   'warning'],
    'PAID'      => ['Ödendi',     'success'],
    'REFUNDED'  => ['İade',       'danger'],
    'CANCELLED' => ['İptal',      'secondary'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satış Panel | MeyruKids iKas Siparişleri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5 !important; font-family: 'Inter', sans-serif !important; }
        .card { border-radius: 1rem !important; border: none !important; box-shadow: 0 4px 6px -1px rgba(196,26,26,0.08), 0 10px 30px -5px rgba(196,26,26,0.10) !important; }
        .card-header { background: linear-gradient(135deg,#c41a1a 0%,#8b0f0f 100%) !important; border-radius: 1rem 1rem 0 0 !important; padding: 14px 20px !important; border: none !important; }
        .card-header .card-title { color:#fff !important; font-weight:700 !important; font-size:1rem !important; margin:0 !important; }
        .table thead th { background: linear-gradient(135deg,#c41a1a 0%,#8b0f0f 100%) !important; color:#fff !important; font-weight:600 !important; font-size:0.75rem !important; text-transform:uppercase !important; letter-spacing:.05em !important; white-space:nowrap !important; border:none !important; padding:12px 10px !important; }
        .table tbody td { font-size:0.84rem !important; vertical-align:middle !important; color:#374151 !important; border-color:#f3f4f6 !important; }
        .table tbody tr:hover td { background-color:#fff5f5 !important; }
        .cs-page-title { display:inline-flex; align-items:center; gap:10px; font-weight:700 !important; color:#c41a1a !important; }
        .cs-page-title-bar { width:4px; height:28px; background:linear-gradient(135deg,#c41a1a,#8b0f0f); border-radius:4px; display:inline-block; }
        .badge { font-weight:600 !important; border-radius:9999px !important; }
        .btn-cs-outline { background:#fff !important; border:1.5px solid #c41a1a !important; border-radius:.65rem !important; color:#c41a1a !important; font-weight:600 !important; font-size:.85rem !important; padding:8px 18px !important; }
        .btn-cs-outline:hover { background:#c41a1a !important; color:#fff !important; }
        .pagination .page-link { color:#c41a1a; border-radius:.5rem !important; margin:0 2px; }
        .pagination .page-item.active .page-link { background:#c41a1a; border-color:#c41a1a; color:#fff; }
    </style>
</head>
<body data-menu-color="light" data-sidebar="default">
<div id="app-layout">
    <?php include 'tema/menu.php'; ?>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="py-3 d-flex align-items-center">
                    <h4 class="fs-18 m-0 cs-page-title">
                        <span class="cs-page-title-bar"></span>
                        MeyruKids iKas Siparişleri
                    </h4>
                </div>

                <?php if ($apiError): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($apiError) ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sipariş Listesi — Sayfa <?= $page ?></h5>
                        <div class="d-flex gap-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" class="btn btn-cs-outline btn-sm">&#8249; Önceki</a>
                            <?php endif; ?>
                            <?php if (count($orders) === $limit): ?>
                                <a href="?page=<?= $page + 1 ?>" class="btn btn-cs-outline btn-sm">Sonraki &#8250;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($orders)): ?>
                            <p class="text-center text-muted py-4">Sipariş bulunamadı.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sipariş No</th>
                                        <th>Müşteri</th>
                                        <th>Telefon</th>
                                        <th>İl / İlçe</th>
                                        <th>Ürünler</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Durumu</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $counter = ($page - 1) * $limit + 1;
                                foreach ($orders as $order):
                                    $firstName = $order['billingAddress']['firstName'] ?? '';
                                    $lastName  = $order['billingAddress']['lastName'] ?? '';
                                    $musteri   = trim($firstName . ' ' . $lastName);
                                    $telefon   = $order['billingAddress']['phone'] ?? '-';
                                    $il        = $order['billingAddress']['city']['name'] ?? '-';
                                    $ilce      = $order['billingAddress']['district']['name'] ?? '-';
                                    $tutar     = number_format($order['totalFinalPrice'] ?? 0, 2, ',', '.') . ' ₺';
                                    $tarih     = isset($order['createdAt']) ? date('d-m-Y H:i', (int)($order['createdAt'] / 1000)) : '-';
                                    $status    = $order['orderPaymentStatus'] ?? '';
                                    [$statusLabel, $statusColor] = $paymentLabels[$status] ?? [$status, 'secondary'];

                                    $urunler = [];
                                    foreach ($order['orderLineItems'] ?? [] as $item) {
                                        $urunAdi = $item['variant']['name'] ?? '?';
                                        $adet    = $item['quantity'] ?? 1;
                                        $urunler[] = htmlspecialchars($urunAdi) . ' x' . $adet;
                                    }
                                    $urunlerStr = implode('<br>', $urunler);
                                ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><strong><?= htmlspecialchars($order['orderNumber'] ?? '') ?></strong></td>
                                        <td><?= htmlspecialchars($musteri) ?></td>
                                        <td><?= htmlspecialchars($telefon) ?></td>
                                        <td><?= htmlspecialchars($il) ?> / <?= htmlspecialchars($ilce) ?></td>
                                        <td class="text-start" style="font-size:0.78rem;"><?= $urunlerStr ?></td>
                                        <td><strong><?= $tutar ?></strong></td>
                                        <td><span class="badge bg-<?= $statusColor ?>"><?= $statusLabel ?></span></td>
                                        <td><?= $tarih ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-3 gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="btn btn-cs-outline">&#8249; Önceki</a>
                    <?php endif; ?>
                    <?php if (count($orders) === $limit): ?>
                        <a href="?page=<?= $page + 1 ?>" class="btn btn-cs-outline">Sonraki &#8250;</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <?php include 'tema/footer.php'; ?>
    </div>
</div>
</body>
</html>
