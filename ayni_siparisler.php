<?php
// Giriş kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once('DB.php');
$db = new DB();

$today = date('Y-m-d');

$sql = "
SELECT *
FROM siparisler s
WHERE DATE(s.siparis_tarihi) = ?
  AND s.deleted_at IS NULL
  AND EXISTS (
      SELECT 1
      FROM siparisler sub
      WHERE sub.musteri_ismi = s.musteri_ismi
        AND DATE(sub.siparis_tarihi) = ?
        AND sub.id != s.id
        AND sub.deleted_at IS NULL
  )
ORDER BY s.musteri_ismi ASC, s.id ASC
";
$result = $db->query($sql, [$today, $today], "ss");

// Müşteri ismine göre grupla
$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[$row['musteri_ismi']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satış Panel | Aynı Siparişler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5 !important;
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important;
        }
        .card {
            border-radius: 1rem !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(196,26,26,.08), 0 10px 30px -5px rgba(196,26,26,.10) !important;
        }
        .card-header {
            background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 14px 20px !important;
            border: none !important;
        }
        .card-header .card-title {
            color: #fff !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            margin: 0 !important;
        }
        .cs-page-title {
            display: inline-flex; align-items: center; gap: 10px;
            font-weight: 700 !important; color: #c41a1a !important;
        }
        .cs-page-title-bar {
            width: 4px; height: 28px;
            background: linear-gradient(135deg, #c41a1a, #8b0f0f);
            border-radius: 4px; display: inline-block;
        }
        .btn-cs-danger {
            background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important;
            border: none !important; border-radius: .65rem !important;
            color: #fff !important; font-weight: 600 !important;
            font-size: .85rem !important; padding: 8px 18px !important;
        }
        .btn-cs-danger:hover { opacity: .88 !important; color: #fff !important; }
        .btn-cs-outline {
            background: #fff !important; border: 1.5px solid #c41a1a !important;
            border-radius: .65rem !important; color: #c41a1a !important;
            font-weight: 600 !important; font-size: .85rem !important; padding: 8px 18px !important;
        }
        .btn-cs-outline:hover { background: #c41a1a !important; color: #fff !important; }
        /* Grup kartı */
        .group-card {
            border-radius: .85rem !important;
            border: none !important;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 8px rgba(196,26,26,.10) !important;
            overflow: hidden;
        }
        .group-header {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-bottom: 2px solid #fca5a5;
            padding: 12px 18px;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }
        .group-header .musteri-adi {
            font-weight: 700; font-size: .97rem; color: #991b1b;
        }
        .group-header .group-count {
            background: #c41a1a; color: #fff;
            border-radius: 9999px; font-size: .75rem;
            font-weight: 700; padding: 2px 10px;
        }
        .table thead th {
            background: #fef2f2 !important;
            color: #7f1d1d !important;
            font-weight: 700 !important; font-size: .75rem !important;
            text-transform: uppercase !important; letter-spacing: .04em !important;
            border-bottom: 2px solid #fca5a5 !important;
            white-space: nowrap; padding: 10px 12px !important;
        }
        .table tbody td {
            font-size: .84rem !important; vertical-align: middle !important;
            color: #374151 !important; border-color: #fef2f2 !important;
        }
        .table tbody tr:hover td { background-color: #fff5f5 !important; }
        .badge { font-weight: 600 !important; border-radius: 9999px !important; }
        .empty-state {
            text-align: center; padding: 60px 20px;
        }
        .empty-state .icon { font-size: 3rem; color: #d1d5db; margin-bottom: 12px; }
        .empty-state p { color: #6b7280; font-size: .95rem; }
    </style>
</head>
<body data-menu-color="light" data-sidebar="default">
<div id="app-layout">
    <?php include 'tema/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
                    <h4 class="fs-18 m-0 cs-page-title">
                        <span class="cs-page-title-bar"></span>
                        Aynı Siparişler
                        <?php if (!empty($groups)): ?>
                            <span class="badge bg-danger ms-2" style="font-size:.8rem;"><?= array_sum(array_map('count', $groups)) ?> kayıt</span>
                        <?php endif; ?>
                    </h4>
                    <form action="siparis_temizle.php" method="POST" onsubmit="return confirm('Mükerrer siparişleri temizlemek istediğinize emin misiniz?')">
                        <button type="submit" class="btn btn-cs-danger">
                            <span class="mdi mdi-delete-sweep me-1"></span> Temizle
                        </button>
                    </form>
                </div>

                <?php if (empty($groups)): ?>
                    <div class="card">
                        <div class="card-body empty-state">
                            <div class="icon"><span class="mdi mdi-check-circle-outline" style="color:#16a34a;"></span></div>
                            <p>Bugün girilen aynı müşteri adına sahip mükerrer sipariş bulunmamaktadır.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($groups as $musteriAdi => $rows): ?>
                        <div class="group-card card">
                            <div class="group-header">
                                <span class="musteri-adi">
                                    <span class="mdi mdi-account me-1"></span>
                                    <?= htmlspecialchars($musteriAdi) ?>
                                </span>
                                <span class="group-count"><?= count($rows) ?> sipariş</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ürünler</th>
                                            <th>Telefon</th>
                                            <th>Tutar</th>
                                            <th>Tarih</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['id']) ?></strong></td>
                                                <td style="max-width:260px;white-space:normal;"><?= htmlspecialchars($row['urunler']) ?></td>
                                                <td><?= htmlspecialchars($row['musteri_telefonu'] ?? '-') ?></td>
                                                <td><?= $row['odeme_sarti'] ? htmlspecialchars($row['odeme_sarti']) . ' TL' : '<span class="badge bg-warning text-dark">Bedelsiz</span>' ?></td>
                                                <td><?= date('d-m-Y H:i', strtotime($row['siparis_tarihi'])) ?></td>
                                                <td>
                                                    <a href="siparis_sil.php?id=<?= $row['id'] ?>"
                                                       class="btn btn-sm btn-cs-outline"
                                                       onclick="return confirm('Bu siparişi silmek istediğinize emin misiniz?')"
                                                       title="Sil">
                                                        <span class="mdi mdi-delete-outline me-1"></span>Sil
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
        <?php include 'tema/footer.php'; ?>
    </div>
</div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/app.js"></script>
<?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '<?= $_GET['status'] === 'success' ? 'success' : 'error' ?>',
        title: '<?= htmlspecialchars($_GET['message'], ENT_QUOTES) ?>',
        showConfirmButton: false,
        timer: 2000
    }).then(function() {
        window.history.replaceState(null, null, window.location.pathname);
    });
});
</script>
<?php endif; ?>
</body>
</html>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satış Panel | Girilen Siparişler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sipariş Yönetim Paneli" />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

</head>

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">
        <?php include 'tema/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Girilen Siparişler</h4>
                        </div>
                        
                        <!-- Temizle Butonu -->
                        <form action="siparis_temizle.php" method="POST" style="margin-bottom: 20px;">
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Mükerrer siparişleri temizlemek istediğinize emin misiniz?')">
                                Temizle
                            </button>
                        </form>
                    </div>

                    <?php
                    require_once('DB.php');
                    $db = new DB();

                    // Bugünün tarihini belirle
                    $today = date('Y-m-d');

                    // SQL sorgusu: Alt sorgu ile tüm mükerrer kayıtları listeleme
                    $sql = "
                    SELECT *
                    FROM siparisler s
                    WHERE DATE(s.siparis_tarihi) = '$today'
                      AND s.deleted_at IS NULL  -- Sadece silinmemişleri göster
                      AND EXISTS (
                          SELECT 1
                          FROM siparisler sub
                          WHERE sub.musteri_ismi = s.musteri_ismi
                            AND DATE(sub.siparis_tarihi) = '$today'
                            AND sub.id != s.id
                            AND sub.deleted_at IS NULL
                      )
                    ORDER BY s.id DESC
                    ";

                    $result = $db->query($sql);

                    // Sonuç sayısını kontrol et
                    $hasResults = $result->num_rows > 0;
                    ?>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Bugünün Mükerrer Siparişleri</h5>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <?php if ($hasResults): ?>
                                    <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>Sipariş No</th>
                                                <th>Müşteri Adı</th>
                                                <th>Ürünler</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['musteri_ismi']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['urunler']); ?></td>
                                                    <td>
                                                        <!-- Tek tek Silme Düğmesi -->
                                                    	<!-- Sil İkonu -->
                    <a href="siparis_sil.php?id=<?= $row['id'] ?>" 
   class="mdi mdi-delete-alert-outline text-danger" 
   style="font-size: 18px; cursor: pointer;" 
   data-bs-toggle="tooltip" 
   data-bs-placement="top" 
   title="Sil" 
   onmousedown="handleMiddleClick(event)">
   Sil
</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p class="text-center text-danger">
                                        Bugün girilen aynı müşteri adına sahip mükerrer sipariş bulunmamaktadır.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div> <!-- container-fluid -->
            </div> <!-- content -->
        </div> <!-- content-page -->

        <?php include 'tema/footer.php'; ?>
    </div><!-- app-layout -->


    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

    <!-- Initialize Datatable -->
    <script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            responsive: true
        });
    });
    </script>

    <!-- Vendor -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="./script.js"></script>

    <?php
    if (isset($_GET['status']) && isset($_GET['message'])) {
        $status = $_GET['status'];
        $message = $_GET['message'];
        $icon = $status === 'success' ? 'success' : 'error';

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{$icon}',
                    title: '{$message}',
                    showConfirmButton: false,
                    timer: 2000
                }).then(function() {
                    // URL'den parametreleri kaldır
                    window.history.replaceState(null, null, window.location.pathname);
                });
            });
        </script>";
    }
    ?>

    <style>
        /* Responsive tablolar */
        .table-responsive {
            overflow-x: auto;
        }
        .table-hover tbody tr:hover {
            background-color: #f9f9f9;
        }
        .table thead th {
            text-align: center;
            white-space: nowrap;
        }
        .btn-primary, .btn-danger {
            margin: 2px;
        }
        #pageSearch {
            margin-bottom: 15px;
            max-width: 400px;
        }
    </style>

</body>
</html>
