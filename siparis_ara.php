<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Giriş kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'DB.php';
$db = new DB();

// Varsayılan tarih bugünün tarihi

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Toplam kayıt sayısı sorgusu
$totalQuery = $db->query("
    SELECT COUNT(*) AS total 
    FROM siparisler 
    WHERE musteri_ismi LIKE '%$search%'
");
$total = $db->fetchAssoc($totalQuery)['total'];
$totalPages = ceil($total / $limit);

// Ana veri sorgusu
$query = "
    SELECT 
    id,
    siparis_tarihi,
    musteri_ismi,
    urunler,
    odeme_sarti,
    kargo_barkodu,
    kargo,
    hangisayfa,
    hangikargo,
    faturalandirma_durumu,
    barkod_basilma_durumu,
    musteri_telefonu,
    kargolink,
    iptalmi,
    musteri_adresi
FROM siparisler
WHERE (musteri_ismi LIKE '%$search%' OR kargo_barkodu LIKE '%$search%')
ORDER BY siparis_tarihi DESC
";


$result = $db->query($query);


?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satış Panel | Tüm Siparişler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sipariş Yönetim Paneli" />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	
	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">


    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        .table-dark {
            background-color: #343a40;
            color: #fff;
        }
        .table-dark th {
            background-color: #454d55;
            color: #fff;
        }
        .badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .btn-red {
            background-color: #dc3545;
            color: white;
        }
        .btn-red:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">
        <?php include 'tema/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Tüm Siparişler</h4>
                        </div>
                    </div>
                
              

                <!-- Sipariş Tablosu -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tüm Siparişler</h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>PTT</th>
                                        <th>BARKOD NO</th>
                                        <th>ÜRÜNLER</th>
                                        <th>MÜŞTERİ</th>
                                        <th>KARGO DAHİL</th>
                                        <th>EKLEME TARİHİ</th>
                                        <th>FATURA</th>
                                        <th>BARKOD</th>
                                        <th>İŞLEM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $db->fetchAssoc($result)): ?>
                                        
                                        <tr>
                                          <td>
    <?= htmlspecialchars($row['id']); ?>
    <?php if ($row['iptalmi'] == 1): ?>
        <span class="badge rounded-pill bg-danger">
            İPTAL
        </span>
    <?php endif; ?>
</td>

                                            <td><?= htmlspecialchars($row['hangikargo']); ?></td>
                                        <td>
    <?php if (!empty($row['kargo_barkodu']) && strpos($row['kargo_barkodu'], 'SMR') === 0): ?>
        <a href="https://www.hepsijet.com/gonderi-takibi/<?= htmlspecialchars($row['kargo_barkodu']) ?>" target="_blank">
            <?= htmlspecialchars($row['kargo_barkodu']) ?>
        </a>
    <?php else: ?>
        <?php if (!empty($row['kargolink'])): ?>
            <a href="<?= htmlspecialchars($row['kargolink']) ?>" target="_blank">
                <?= htmlspecialchars($row['kargo_barkodu']) ?>
            </a>
        <?php else: ?>
            <?= htmlspecialchars($row['kargo_barkodu']) ?>
        <?php endif; ?>
    <?php endif; ?>
</td>
                                            <td><?= nl2br(htmlspecialchars($row['urunler'])); ?></td>
                                            <td>
    <?= htmlspecialchars($row['musteri_ismi']); ?><br>
    <?= htmlspecialchars($row['musteri_telefonu']); ?><br>
    <?= htmlspecialchars($row['musteri_adresi']); ?>
    <br>
    <span class="badge rounded-pill bg-danger">
        <?= htmlspecialchars($row['hangisayfa']); ?>
    </span>
</td>

                                            <td><?= htmlspecialchars($row['odeme_sarti']); ?></td>
                                            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['siparis_tarihi']))); ?></td>
                                            <td>
                                                <span class="badge <?= $row['faturalandirma_durumu'] === 'Faturalandı' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?= $row['faturalandirma_durumu']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $row['barkod_basilma_durumu'] === 'Basılmış' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?= $row['barkod_basilma_durumu']; ?>
													<br>
                                                </span>
												<br><br>
												
												 <span class="badge rounded-pill bg-warning">
       <?=  htmlspecialchars($row['kargo']); ?>
    </span>
												
												
												
                                            </td>
                                            <td>
                                           <div class="d-flex flex-column gap-1">
    <?php if($row['kargo'] == 'Bedelsiz' || $row['kargo'] == 'Ücreti Alıcıdan'): ?>
        <button class="btn btn-success rounded-pill" onclick="window.open('print_barcode_tekli.php?id=<?= $row['id']; ?>', '_blank')">10X10</button>
    <?php endif; ?>
    
    <?php if($row['kargo'] == 'Ödeme Şartlı'): ?>
        <button class="btn btn-dark rounded-pill w-100" onclick="window.open('odeme_sartli_tekli.php?id=<?= $row['id']; ?>', '_blank')">10X15</button>
    <?php endif; ?>
    
    <button class="btn btn-link rounded-pill w-100" onclick="cancelOrder(<?= $row['id']; ?>)">İptal</button>
	
	
	
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
	
	
	
</div>

                                        </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            

                <!-- Sayfalama -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i === $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?date=<?= htmlspecialchars($date); ?>&search=<?= htmlspecialchars($search); ?>&page=<?= $i; ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
            
        </div>
    </div>

        <!-- Vendor -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


        <!-- App js-->
        <script src="assets/js/app.js"></script>
        <script  src="./script.js"></script>

        <!-- JavaScript fonksiyonunu ekle -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        function cancelOrder(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu sipariş iptal edilecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, iptal et!',
                cancelButtonText: 'Hayır'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('cancel_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Sipariş başarıyla iptal edildi.'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                }
            });
        }
        </script>
        <style>
        .btn-sm {
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
        }
        </style>

</body>
</html>

