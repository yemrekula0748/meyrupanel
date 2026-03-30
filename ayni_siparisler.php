<?php
// Giriş kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

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
