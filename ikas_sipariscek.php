<?php
// API URL'si
$url = 'https://api.myikas.com/api/v1/admin/graphql';

// Authorization token (Değiştirin)
$accessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjQ4OWYxZGY5LTlmOWUtNDgwMi1iNTEyLThmNzgwYWNmNmNhMCIsImVtYWlsIjoidGVzdCIsImZpcnN0TmFtZSI6InRlc3QiLCJsYXN0TmFtZSI6IiIsInN0b3JlTmFtZSI6InNlbXJlYnV0aWsiLCJtZXJjaGFudElkIjoiZGQ1NDViZmUtYjM1NS00ZmFmLTk0ZTktNTEwMjZkZmNhNjRiIiwiZmVhdHVyZXMiOlsxMSwyLDQsOV0sImF1dGhvcml6ZWRBcHBJZCI6IjQ4OWYxZGY5LTlmOWUtNDgwMi1iNTEyLThmNzgwYWNmNmNhMCIsInR5cGUiOjQsImV4cCI6MTczNTUxODY3ODY1MCwiaWF0IjoxNzM1NTA0Mjc4NjUwLCJpc3MiOiJkZDU0NWJmZS1iMzU1LTRmYWYtOTRlOS01MTAyNmRmY2E2NGIiLCJzdWIiOiI0ODlmMWRmOS05ZjllLTQ4MDItYjUxMi04Zjc4MGFjZjZjYTAifQ.XcTu3e5bgzwmjfGyQuGJZ7ELD5XU5MZsojmcbQuG8BE';

// GraphQL sorgusu
$query = <<<GRAPHQL
{
  listOrder {
    data {
      orderNumber
      createdAt
      status
      totalFinalPrice
      customer {
        email
        firstName
        lastName
      }
      billingAddress {
        firstName
        lastName
        addressLine1
        city {
          name
        }
        country {
          name
        }
      }
    }
  }
}
GRAPHQL;

// cURL başlat
$ch = curl_init($url);

// cURL ayarları
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));

// İstek gönder ve yanıtı al
$response = curl_exec($ch);

// Hata kontrolü
if (curl_errno($ch)) {
    echo 'cURL Hatası: ' . curl_error($ch);
} else {
    // JSON yanıtı çözümle ve göster
    $responseData = json_decode($response, true);
    if (isset($responseData['errors'])) {
        echo "GraphQL Hataları:\n";
        print_r($responseData['errors']);
    } else {
        // Gelen sipariş verilerini al
        $orders = $responseData['data']['listOrder']['data'];
    }
}

// cURL oturumunu kapat
curl_close($ch);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Listesi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Sipariş Listesi</h2>
    <!-- DataTable -->
    <table id="orderTable" class="table table-striped table-bordered dt-responsive nowrap" style="width: 100%;">
        <thead class="thead-dark">
            <tr>
                <th>Sipariş Numarası</th>
                <th>Müşteri Adı</th>
                <th>Email</th>
                <th>Sipariş Tarihi</th>
                <th>Status</th>
                <th>Total Fiyat</th>
                <th>Fatura Adresi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)) : ?>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['orderNumber']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer']['firstName']) . ' ' . htmlspecialchars($order['customer']['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer']['email']); ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($order['createdAt'])); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo htmlspecialchars($order['totalFinalPrice']); ?></td>
                        <td><?php echo htmlspecialchars($order['billingAddress']['addressLine1'] . ', ' . $order['billingAddress']['city']['name'] . ', ' . $order['billingAddress']['country']['name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7">Veri bulunamadı.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (DataTable için gereklidir) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- DataTables Responsive JS -->
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- DataTable Başlatma -->
<script>
    $(document).ready(function() {
        $('#orderTable').DataTable({
            "paging": true,
            "searching": true,  // Arama özelliğini etkinleştir
            "ordering": true,   // Sıralama özelliğini etkinleştir
            "info": true,       // Sayfalama bilgisi
            "responsive": true  // Responsive özelliğini etkinleştir
        });
    });
</script>

</body>
</html>