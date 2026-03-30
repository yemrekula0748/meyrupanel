<?php

// Hata ayıklama
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/autoload.php';
echo "Autoload başarıyla dahil edildi!";

require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/tecnickcom/tcpdf/tcpdf.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'DB.php';
$db = new DB();

// Parametre kontrolü
$odeme_sarti = isset($_GET['odeme_sarti']) ? $_GET['odeme_sarti'] : '';
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$sayfa = max($sayfa, 1);
$limit = 10;
$offset = ($sayfa - 1) * $limit;

$sorgu = "";
if ($odeme_sarti === 'UcretAlici') {
    $sorgu = "SELECT * FROM siparisler WHERE TRIM(kargo) IN ('Bedelsiz') AND (barkod_basilma_durumu IS NULL OR barkod_basilma_durumu = '' OR barkod_basilma_durumu = 'Basılmamış') ORDER BY id DESC LIMIT $limit OFFSET $offset";
} elseif ($odeme_sarti === 'OdemeSartli') {
    $sorgu = "SELECT * FROM siparisler WHERE TRIM(kargo) = 'Ödeme Şartlı' AND (barkod_basilma_durumu IS NULL OR barkod_basilma_durumu = '' OR barkod_basilma_durumu = 'Basılmamış') ORDER BY id DESC LIMIT $limit OFFSET $offset";
}



$siparisler = [];
if ($sorgu) {
    $result = $db->query($sorgu);
    while ($row = $db->fetchAssoc($result)) {
        $siparisler[] = $row;
    }
}

// Toplam kayıt sayısını al
$toplam_sorgu = "SELECT COUNT(*) as toplam FROM siparisler WHERE TRIM(kargo) ";
if ($odeme_sarti === 'UcretAlici') {
    $toplam_sorgu .= "IN ('Bedelsiz')";
} elseif ($odeme_sarti === 'OdemeSartli') {
    $toplam_sorgu .= "= 'Ödeme Şartlı'";
}
$toplam_result = $db->query($toplam_sorgu);
$toplam_kayit = $db->fetchAssoc($toplam_result)['toplam'];
$toplam_sayfa = ceil($toplam_kayit / $limit);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Barkod Basım | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Barkod yükleme işlemlerini gerçekleştirin." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">
        <?php include 'tema/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- Header -->
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Barkodları Yazdır</h4>
                        </div>
                    </div>

                    <!-- Filtreleme Butonları -->
                    <div class="mb-3">
                        <a href="?odeme_sarti=UcretAlici" class="btn btn-primary">Ücret Alıcıdan ve Bedelsiz</a>
                        <a href="?odeme_sarti=OdemeSartli" class="btn btn-secondary">Ödeme Şartlı</a>
                    </div>

                    <!-- Sipariş Listesi -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Siparişler</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Müşteri Adı</th>
                                            <th>Telefon</th>
                                            <th>Adres</th>
                                            <th>Ürünler</th>
                                            <th>Barkod</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($siparisler)): ?>
                                            <?php foreach ($siparisler as $siparis): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($siparis['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($siparis['musteri_ismi']); ?></td>
                                                    <td><?php echo htmlspecialchars($siparis['musteri_telefonu']); ?></td>
                                                    <td><?php echo htmlspecialchars($siparis['musteri_adresi']); ?></td>
                                                    <td><?php echo htmlspecialchars($siparis['urunler']); ?></td>
                                                    <td><?php echo htmlspecialchars($siparis['kargo_barkodu']); ?></td>
                                                    <td>
                                                        <?php if ($odeme_sarti === 'UcretAlici'): ?>
                                                            <!-- Ücret Alıcıdan için özel sayfaya yönlendirme -->
                                                            <a href="print_single_barcode.php?id=<?php echo $siparis['id']; ?>" class="btn btn-info btn-sm">Tekil Barkod Bas</a>
                                                        <?php else: ?>
                                                            <!-- Varsayılan davranış -->
                                                            <a href="e-Fatura.php?musteri_id=<?php echo $siparis['id']; ?>" class="btn btn-info btn-sm">Tekil Barkod Bas</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Hiçbir sipariş bulunamadı.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <?php if (!empty($siparisler)): ?>
                                    <div class="mt-3 text-center">
                                        <?php if ($odeme_sarti === 'UcretAlici'): ?>
                                            <!-- Ücret Alıcıdan için özel yönlendirme -->
                                            <a href="print_bulk_barcode.php?odeme_sarti=<?php echo $odeme_sarti; ?>" class="btn btn-success">
                                                Toplu Barkod Bas
                                            </a>
                                        <?php else: ?>
                                            <!-- Varsayılan davranış -->
                                            <a href="toplu_fatura.php?odeme_sarti=<?php echo $odeme_sarti; ?>" class="btn btn-success">
                                                Toplu Barkod Bas
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </div>



                            <!-- Sayfalandırma -->
                            <nav aria-label="Sayfalandırma">
                                <ul class="pagination justify-content-center mt-3">
                                    <?php for ($i = 1; $i <= $toplam_sayfa; $i++): ?>
                                        <li class="page-item <?php echo ($i === $sayfa) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?odeme_sarti=<?php echo $odeme_sarti; ?>&sayfa=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'tema/footer.php'; ?>
    </div>

    <!-- Vendor -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/app.js"></script>

    <style>
        .table {
            table-layout: fixed;
            word-wrap: break-word;
        }
        .table th, .table td {
            max-width: 200px; /* Hücre genişliklerini sınırlayın */
            overflow: hidden;
            text-overflow: ellipsis; /* Uzun metni kesmek için */
            white-space: nowrap;
        }
    </style>

</body>
</html>
