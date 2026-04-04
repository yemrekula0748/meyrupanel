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
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 400;


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
        musteri_adresi,
        resmilestir,
        kargolink,
        parasut_resmilesme_durumu,
        yunusemrekula
    FROM siparisler
    WHERE iptalmi = 0 
    AND DATE(siparis_tarihi) = '$date'  -- Tarih filtresi eklendi
    ORDER BY id DESC
    LIMIT $limit
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5 !important;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c41a1a' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") !important;
        }
        body, .content-page, .content, .container-fluid { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important; }
        .card { border-radius: 1rem !important; border: none !important; box-shadow: 0 4px 6px -1px rgba(196,26,26,0.08), 0 10px 30px -5px rgba(196,26,26,0.10) !important; }
        .card-header { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; border-radius: 1rem 1rem 0 0 !important; padding: 14px 20px !important; border: none !important; }
        .card-header .card-title { color: #fff !important; font-family: 'Inter', sans-serif !important; font-weight: 700 !important; font-size: 1rem !important; margin: 0 !important; }
        .table thead th { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; color: #fff !important; font-family: 'Inter', sans-serif !important; font-weight: 600 !important; font-size: 0.75rem !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; white-space: nowrap !important; border: none !important; padding: 12px 10px !important; }
        .table tbody td { font-family: 'Inter', sans-serif !important; font-size: 0.84rem !important; vertical-align: middle !important; color: #374151 !important; border-color: #f3f4f6 !important; }
        .table tbody tr:hover td { background-color: #fff5f5 !important; }
        .cs-page-title { display: inline-flex; align-items: center; gap: 10px; font-family: 'Inter', sans-serif !important; font-weight: 700 !important; color: #c41a1a !important; }
        .cs-page-title-bar { width: 4px; height: 28px; background: linear-gradient(135deg, #c41a1a, #8b0f0f); border-radius: 4px; display: inline-block; }
        .form-control, .form-select { font-family: 'Inter', sans-serif !important; border-radius: 0.5rem !important; font-size: 0.85rem !important; }
        .form-control:focus, .form-select:focus { border-color: #c41a1a !important; box-shadow: 0 0 0 3px rgba(196,26,26,0.12) !important; }
        .btn-cs-primary { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; border: none !important; border-radius: 0.65rem !important; color: #fff !important; font-family: 'Inter', sans-serif !important; font-weight: 600 !important; font-size: 0.85rem !important; padding: 8px 18px !important; box-shadow: 0 2px 8px rgba(196,26,26,0.25) !important; transition: opacity 0.15s !important; }
        .btn-cs-primary:hover { opacity: 0.88 !important; color: #fff !important; }
        .badge { font-family: 'Inter', sans-serif !important; font-weight: 600 !important; border-radius: 9999px !important; }
        .page-link { font-family: 'Inter', sans-serif !important; }
        .page-item.active .page-link { background-color: #c41a1a !important; border-color: #c41a1a !important; }
        .page-link:hover { color: #c41a1a !important; }
        .btn-success.rounded-pill, .btn-dark.rounded-pill, .btn-warning.rounded-pill { font-family: 'Inter', sans-serif !important; font-weight: 600 !important; font-size: 0.78rem !important; }
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
                            <h4 class="fs-18 m-0 cs-page-title">
                                <span class="cs-page-title-bar"></span>
                                Tüm Siparişler
                            </h4>
                        </div>
                    </div>
                
                <!-- Arama ve Tarih Seçimi -->
                <form method="GET" class="mb-4 d-flex align-items-center" style="gap:10px;">
                    <input type="date" name="date" class="form-control" style="max-width:200px;" value="<?= htmlspecialchars($date); ?>" required>
                    <button type="submit" class="btn btn-cs-primary">Ara</button>
                </form>

                <!-- Sipariş Tablosu -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tüm Siparişler</h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="anlikArama" class="form-control" placeholder="🔍 Barkod no veya müşteri bilgisi ile arayın..." style="max-width:400px;" autocomplete="off">
                        </div>
                        <div class="table-responsive">
                            <table id="siparisTablo" class="table table-striped table-bordered text-center align-middle">
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
                                    <?php while ($row = $db->fetchAssoc($result)):


$kargoDurumu = match ($row['kargo']) {
                                                'Ödeme Şartlı' => 'MH',
                                                'Bedelsiz' => 'B',
                                                'Ücreti Alıcıdan' => 'UA',
                                                default => $row['kargo']
                                            };




if ($row['odeme_sarti'] == 0 || $row['odeme_sarti'] === null) {
    $odemeSarti = $kargoDurumu;  // 0 veya null ise sadece $kargoDurumu yazdırılır.
} else {
    $odemeSarti = $row['odeme_sarti'] . " TL";  // Aksi takdirde, TL eklenir.
}













									?>
                                        
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']); ?></td>
                                            <td><?= htmlspecialchars($row['hangikargo']); ?></td>
                                            <td>
<?php
$barkod = $row['kargo_barkodu'] ?? '';
if ($barkod === null || $barkod === '' ) {
    echo '<span style="color:orange;font-weight:bold;">Oluşturuluyor</span>';
} else if (strpos($barkod, 'SMR') === 0) {
    echo '<a href="https://www.hepsijet.com/gonderi-takibi/' . htmlspecialchars($barkod) . '" target="_blank">' . htmlspecialchars($barkod) . '</a>';
} else if (!empty($row['kargolink'])) {
    echo '<a href="' . htmlspecialchars($row['kargolink']) . '" target="_blank">' . htmlspecialchars($barkod) . '</a>';
} else {
    echo htmlspecialchars($barkod);
}
?>
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

                                            <td><?= htmlspecialchars($odemeSarti) ?></td>
                                            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['siparis_tarihi']))); ?></td>
                                            <td>
                                              <span class="badge <?= $row['parasut_resmilesme_durumu'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
    <?= $row['parasut_resmilesme_durumu'] == 1 ? '✓' : '✖︎'; ?>
</span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $row['barkod_basilma_durumu'] === 'Basılmış' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?= $row['barkod_basilma_durumu']; ?>
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
	
	<?php if ($row['kargo'] === 'Ödeme Şartlı' && $row['resmilestir'] == 0 && $row['yunusemrekula'] == 0): ?>
    <button class="btn btn-warning rounded-pill w-100"
            onclick="resmilestirYunus(<?= $row['id']; ?>, this)">
        Resmileştir
    </button>
<?php endif; ?>

    
    <button class="btn btn-link rounded-pill w-100" onclick="cancelOrder(<?= $row['id']; ?>)">İptal</button>
	
	
	
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

<script>
document.getElementById('anlikArama').addEventListener('input', function () {
    var aramaMetni = this.value.toLowerCase().trim();
    var satirlar = document.querySelectorAll('#siparisTablo tbody tr');
    satirlar.forEach(function (satir) {
        var barkod   = (satir.cells[2] ? satir.cells[2].textContent : '').toLowerCase();
        var musteri  = (satir.cells[4] ? satir.cells[4].textContent : '').toLowerCase();
        var eslesme  = barkod.includes(aramaMetni) || musteri.includes(aramaMetni);
        satir.style.display = eslesme ? '' : 'none';
    });
});
</script>

<script>
function resmilestirYunus(id, btn) {
    if (!confirm(id + ' numaralı siparişi resmileştirme kuyruğuna almak istiyor musunuz?')) return;
    btn.disabled = true;
    btn.textContent = 'Bekleyin...';
    fetch('resmilestir_yunusemre_islem.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=update_single&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            btn.textContent = '✓ Kuyruğa Alındı';
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-success');
        } else {
            btn.textContent = 'Hata';
            btn.disabled = false;
        }
    })
    .catch(() => {
        btn.textContent = 'Hata';
        btn.disabled = false;
    });
}
</script>

</body>
</html>

