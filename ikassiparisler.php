<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'DB.php';
$db = new DB();

// Yılları al
$yearQuery = "SELECT DISTINCT YEAR(createdAt) AS year FROM ikas_siparisler ORDER BY year DESC";
$yearResult = $db->query($yearQuery);

$years = [];
while ($row = $db->fetchAssoc($yearResult)) {
    $years[] = $row['year'];
}

// Varsayılan olarak mevcut yılın verilerini çekelim
$currentYear = date('Y');
$yearFilter = $_GET['year'] ?? $currentYear;
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

// Filtreleme ve SQL sorgusunun belirlenmesi
$sql = '';
$params = [];
$types = '';

if ($startDate && $endDate) {
    $sql = "SELECT * FROM ikas_siparisler WHERE createdAt BETWEEN ? AND ? ORDER BY createdAt DESC";
    $params = [$startDate, $endDate];
    $types = 'ss';
} elseif ($yearFilter === "all") {
    $sql = "SELECT * FROM ikas_siparisler ORDER BY createdAt DESC";
} else {
    $sql = "SELECT * FROM ikas_siparisler WHERE YEAR(createdAt) = ? ORDER BY createdAt DESC";
    $params = [$yearFilter];
    $types = 's';
}

// SQL sorgusunu çalıştır
try {
    if (!empty($params)) {
        $result = $db->query($sql, $params, $types);
    } else {
        $result = $db->query($sql);
    }
} catch (mysqli_sql_exception $e) {
    die("SQL Sorgusu Hatası: " . $e->getMessage());
}

// Siparişleri çek
$orders = [];
while ($row = $db->fetchAssoc($result)) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>iKas Siparişleri | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">


    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- App CSS (Bootstrap, vs.) -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    

    <!-- DataTables CSS (Bootstrap 5) -->
    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"
    >
</head>
<body data-menu-color="light" data-sidebar="default">
<div id="app-layout">
<?php include 'tema/menu.php'; ?>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- Başlık / Header benzeri -->
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">iKas Sipariş Listesi</h4>
                        <p class="text-muted fs-14 mt-1">
                            Veritabanına kaydedilen iKas siparişlerini görüntüleyin.
                        </p>
                        <div class="d-flex align-items-center">
                        <!-- Bacanak istemedi :) -->
                        <!--<input type="date" id="startDate" class="form-control me-2" placeholder="Başlangıç Tarihi">
                        <input type="date" id="endDate" class="form-control me-2" placeholder="Bitiş Tarihi">
                        <button id="filterDate" class="btn btn-primary">Filtrele</button>-->
                    </div>
                    </div>
                </div>

                <!-- Yıl Butonları -->
                <div class="d-flex mb-3">
                    <button class="btn btn-secondary me-2 year-filter" data-year="all">Tüm Yıllar</button>
                    <?php foreach ($years as $year): ?>
                        <button 
                            class="btn btn-secondary me-2 year-filter <?= ($year == $yearFilter) ? 'btn-primary' : '' ?>" 
                            data-year="<?= $year ?>">
                            <?= $year ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Tablo Alanı -->
                <div class="table-responsive bg-white p-3 rounded shadow-sm">
                    <table
                        id="ikasOrderTable"
                        class="table table-bordered table-hover align-middle"
                    >
                        <thead class="table-light">
                            <tr>
                                <th>Sipariş Numarası</th>
                                <th>Toplam Fiyat</th>
                                <th>Tarih</th>
                                <th>Müşteri</th>
                                <th>Şehir</th>
                                <th>Ürün Adları</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['orderNumber']) ?></td>
                                    <td><?= htmlspecialchars($order['totalFinalPrice']) ?> TL</td>
                                    <td><?= htmlspecialchars($order['createdAt']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_firstname'] . ' ' . $order['customer_lastname']) ?></td>
                                    <td><?= htmlspecialchars($order['city']) ?></td>
                                    <td><?= htmlspecialchars($order['productNames']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-muted text-center">Henüz sipariş yok.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div> <!-- /.table-responsive -->

            </div><!-- /.container-fluid -->
        </div><!-- /.content -->
    </div><!-- /.content-page -->

    <!-- Footer (örnek) -->
    <?php include 'tema/footer.php'; ?>

</div><!-- /#app-layout -->



<!-- Gerekli JS Kütüphaneleri -->
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>        
<script src="assets/libs/jquery-ui/jquery-ui.min.js"></script>				
<script  src="./script.js"></script>


<!-- DataTables JS -->
<script
    src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"
></script>
<script
    src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"
></script>



<!-- App JS -->
<script src="assets/js/app.js"></script>

<script>
$('#ikasOrderTable').DataTable({
    pageLength: 10,  // İlk açılışta sayfa başına 10 kayıt göster
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Hepsi"]],
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
    },
    order: [[0, "desc"]] 
    // Burada 0 ilk kolonu temsil eder; "desc" ise azalan sıralama anlamına gelir.
});

</script>

<script>
$(document).ready(function(){
    $('#ikasOrderTable').DataTable();

    // Yıl Butonlarına Tıklama
    $('.year-filter').click(function() {
        const year = $(this).data('year');
        window.location.href = `?year=${year}`;
    });

    // Tarih Aralığı Filtreleme
    $('#filterDate').click(function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        if (startDate && endDate) {
            window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
        } else {
            alert("Lütfen her iki tarihi de giriniz.");
        }
    });
});
</script>
</body>
</html>
