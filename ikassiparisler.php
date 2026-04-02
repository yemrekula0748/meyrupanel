<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'DB.php';
$db = new DB();

// AJAX: siparisi siparisler tablosuna ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ekle_siparise') {
    header('Content-Type: application/json; charset=utf-8');
    $siparis_no = trim($_POST['siparis_no'] ?? '');
    if (empty($siparis_no)) {
        echo json_encode(['status' => 'error', 'message' => 'Siparis no eksik.']);
        exit;
    }

    // meyru_ikas_son tablosundan siparisi getir
    $row = $db->query("SELECT * FROM meyru_ikas_son WHERE siparis_no = ?", [$siparis_no], "s")->fetch_assoc();
    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Siparis bulunamadi.']);
        exit;
    }

    // Daha once eklenmis mi kontrol et
    $kontrol = $db->query("SELECT id FROM siparisler WHERE ikasno = ?", [$siparis_no], "s");
    if ($kontrol->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Bu siparis zaten siparisler tablosuna eklenmis.']);
        exit;
    }

    $odeme_yontemi = $row['odeme_yontemi'] ?? '';
    if (strpos($odeme_yontemi, 'CASH_ON_DELIVERY') !== false) {
        $kargo       = 'Ödeme Şartlı';
        $odeme_sarti = (int)$row['toplam_fiyat'];
    } elseif (strpos($odeme_yontemi, 'CREDIT_CARD') !== false) {
        $kargo       = 'Bedelsiz';
        $odeme_sarti = 0;
    } else {
        $kargo       = $odeme_yontemi;
        $odeme_sarti = (int)$row['toplam_fiyat'];
    }

    $user_name = $_SESSION['user_name'] ?? 'Bilinmiyor';

    $db->query(
        "INSERT INTO siparisler
        (ikasno, musteri_ismi, musteri_adresi, siparis_tarihi, musteri_il, musteri_ilce, urunler, musteri_telefonu, hangikargo, odeme_sarti, hangisayfa, desi, kargo, ikasmi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $siparis_no,
            $row['musteri_ismi'],
            $row['adres'],
            $row['tarih'],
            $row['sehir'],
            $row['ilce'],
            $row['urunler'],
            $row['telefon'],
            $user_name,
            $odeme_sarti,
            'iKas',
            1,
            $kargo,
            1
        ],
        "ssssssssssissi"
    );

    echo json_encode(['status' => 'success', 'message' => 'Siparis basariyla eklendi.']);
    exit;
}

// meyru_ikas_son tablosu yoksa bildir
$tableExists = $db->query("SHOW TABLES LIKE 'meyru_ikas_son'")->num_rows > 0;

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 50;
$fromDate = $_GET['from'] ?? date('Y-m-d', strtotime('-90 days'));
$toDate   = $_GET['to']   ?? date('Y-m-d');

$totalCount = 0;
$orders     = [];
$addedSet   = [];

if ($tableExists) {
    $countRow = $db->query(
        "SELECT COUNT(*) AS c FROM meyru_ikas_son WHERE DATE(tarih) BETWEEN ? AND ? AND (odeme_yontemi IS NULL OR odeme_yontemi NOT LIKE 'MONEY_ORDER%')",
        [$fromDate, $toDate], "ss"
    )->fetch_assoc();
    $totalCount = (int)($countRow['c'] ?? 0);

    $offset = ($page - 1) * $limit;
    $result = $db->query(
        "SELECT * FROM meyru_ikas_son WHERE DATE(tarih) BETWEEN ? AND ? AND (odeme_yontemi IS NULL OR odeme_yontemi NOT LIKE 'MONEY_ORDER%')
         ORDER BY tarih DESC LIMIT ? OFFSET ?",
        [$fromDate, $toDate, $limit, $offset], "ssii"
    );
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    // Hangi siparisler zaten siparisler tablosuna eklenmis?
    $addedSet = [];
    if (!empty($orders)) {
        $siparisNolar = array_filter(array_column($orders, 'siparis_no'));
        if (!empty($siparisNolar)) {
            $placeholders = implode(',', array_fill(0, count($siparisNolar), '?'));
            $types = str_repeat('s', count($siparisNolar));
            $addedResult = $db->query(
                "SELECT ikasno FROM siparisler WHERE ikasno IN ($placeholders)",
                $siparisNolar, $types
            );
            while ($addedRow = $addedResult->fetch_assoc()) {
                $addedSet[$addedRow['ikasno']] = true;
            }
        }
    }
}

$totalPages = $totalCount > 0 ? (int)ceil($totalCount / $limit) : 1;
$qs = http_build_query(['from' => $fromDate, 'to' => $toDate]);

$paymentLabels = [
    'WAITING'   => ['Bekliyor',   'warning'],
    'PAID'      => ['Odendi',     'success'],
    'REFUNDED'  => ['Iade',       'danger'],
    'CANCELLED' => ['Iptal',      'secondary'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satis Panel | MeyruKids iKas Siparisler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5 !important; font-family: 'Inter', sans-serif !important; }
        .card { border-radius: 1rem !important; border: none !important; box-shadow: 0 4px 6px -1px rgba(196,26,26,.08), 0 10px 30px -5px rgba(196,26,26,.10) !important; }
        .card-header { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; border-radius: 1rem 1rem 0 0 !important; padding: 14px 20px !important; border: none !important; }
        .card-header .card-title { color: #fff !important; font-weight: 700 !important; font-size: 1rem !important; margin: 0 !important; }
        .table thead th { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; color: #fff !important; font-weight: 600 !important; font-size: .75rem !important; text-transform: uppercase !important; letter-spacing: .05em !important; white-space: nowrap !important; border: none !important; padding: 12px 10px !important; }
        .table tbody td { font-size: .84rem !important; vertical-align: middle !important; color: #374151 !important; border-color: #f3f4f6 !important; }
        .table tbody tr:hover td { background-color: #fff5f5 !important; }
        .siparis-eklendi td { text-decoration: line-through; opacity: .55; }
        .siparis-eklendi .ekle-btn { pointer-events: none; opacity: .4; cursor: not-allowed; }
        .cs-page-title { display: inline-flex; align-items: center; gap: 10px; font-weight: 700 !important; color: #c41a1a !important; }
        .cs-page-title-bar { width: 4px; height: 28px; background: linear-gradient(135deg, #c41a1a, #8b0f0f); border-radius: 4px; display: inline-block; }
        .badge { font-weight: 600 !important; border-radius: 9999px !important; }
        .btn-cs-outline { background: #fff !important; border: 1.5px solid #c41a1a !important; border-radius: .65rem !important; color: #c41a1a !important; font-weight: 600 !important; font-size: .85rem !important; padding: 8px 18px !important; }
        .btn-cs-outline:hover { background: #c41a1a !important; color: #fff !important; }
        .btn-cs-green { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important; border: none !important; border-radius: .65rem !important; color: #fff !important; font-weight: 600 !important; font-size: .85rem !important; padding: 8px 18px !important; }
        .pagination .page-link { color: #c41a1a; border-radius: .5rem !important; margin: 0 2px; }
        .pagination .page-item.active .page-link { background: #c41a1a; border-color: #c41a1a; color: #fff; }
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
                        MeyruKids iKas Siparisler
                    </h4>
                    <button class="btn btn-cs-green" id="veriCekBtn">
                        <span class="mdi mdi-refresh me-1"></span> Verileri Guncelle
                    </button>
                </div>

                <div id="veriCekSonuc" class="mb-3" style="display:none;"></div>

                <?php if (!$tableExists): ?>
                    <div class="alert alert-warning">
                        <strong>meyru_ikas_son</strong> tablosu henuz olusturulmamis.
                        "Verileri Guncelle" butonuna basin.
                    </div>
                <?php endif; ?>

                <!-- Tarih Filtresi -->
                <form method="get" class="card mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 fw-semibold" style="font-size:.85rem;white-space:nowrap;">Baslangic:</label>
                                <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($fromDate) ?>" style="width:145px;">
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 fw-semibold" style="font-size:.85rem;white-space:nowrap;">Bitis:</label>
                                <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($toDate) ?>" style="width:145px;">
                            </div>
                            <input type="hidden" name="page" value="1">
                            <button type="submit" class="btn btn-cs-outline btn-sm">Filtrele</button>
                            <a href="?" class="btn btn-cs-outline btn-sm">Sifirla</a>
                        </div>
                    </div>
                </form>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            Siparis Listesi &mdash; Sayfa <?= $page ?> / <?= $totalPages ?>
                            <small class="fw-normal ms-2">(toplam <?= $totalCount ?> siparis)</small>
                        </h5>
                        <div class="d-flex gap-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&<?= $qs ?>" class="btn btn-cs-outline btn-sm">&#8249; Onceki</a>
                            <?php endif; ?>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&<?= $qs ?>" class="btn btn-cs-outline btn-sm">Sonraki &#8250;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($orders)): ?>
                            <p class="text-center text-muted py-4">Kayit bulunamadi.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>Siparis No</th>
                                        <th>Musteri</th>
                                        <th>Telefon</th>
                                        <th>Il / Ilce</th>
                                        <th>Urunler</th>
                                        <th>Tutar</th>
                                        <th>Odeme Yontemi</th>
                                        <th>Odeme Durumu</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $counter = ($page - 1) * $limit + 1;
                                foreach ($orders as $r):
                                    $tutar = number_format($r['toplam_fiyat'] ?? 0, 2, ',', '.') . ' TL';
                                    $tarih = !empty($r['tarih']) ? date('d-m-Y H:i', strtotime($r['tarih'])) : '-';
                                    $status = $r['odeme_durumu'] ?? '';
                                    [$statusLabel, $statusColor] = $paymentLabels[$status] ?? [$status, 'secondary'];
                                    $zatenEklendi = isset($addedSet[$r['siparis_no']]);
                                ?>
                                    <tr<?= $zatenEklendi ? ' class="siparis-eklendi"' : '' ?>>
                                        <td>
                                            <button class="btn btn-sm btn-success ekle-btn" data-siparis="<?= htmlspecialchars($r['siparis_no'] ?? '', ENT_QUOTES) ?>" title="Siparislerime Ekle" style="font-size:1rem;line-height:1;padding:2px 8px;"><?= $zatenEklendi ? '✓' : '+' ?></button>
                                        </td>
                                        <td><?= $counter++ ?></td>
                                        <td><strong><?= htmlspecialchars($r['siparis_no'] ?? '') ?></strong></td>
                                        <td><?= htmlspecialchars($r['musteri_ismi'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['telefon'] ?? '') ?></td>
                                        <td><?= htmlspecialchars(($r['sehir'] ?? '-') . ' / ' . ($r['ilce'] ?? '-')) ?></td>
                                        <td class="text-start" style="font-size:.78rem;"><?= nl2br(htmlspecialchars($r['urunler'] ?? '')) ?></td>
                                        <td><strong><?= $tutar ?></strong></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['odeme_yontemi'] ?? '-') ?></span></td>
                                        <td><span class="badge bg-<?= $statusColor ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
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
                        <a href="?page=<?= $page - 1 ?>&<?= $qs ?>" class="btn btn-cs-outline">&#8249; Onceki</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&<?= $qs ?>" class="btn btn-cs-outline">Sonraki &#8250;</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <?php include 'tema/footer.php'; ?>
    </div>
</div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/app.js"></script>
<script>
document.querySelectorAll('.ekle-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const siparisNo = this.dataset.siparis;
        Swal.fire({
            title: 'Onay',
            text: 'Bu sipariş siparişlerim tablosuna eklenecek. Onaylıyor musunuz?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Evet, Ekle',
            cancelButtonText: 'İptal',
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#c41a1a'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            const formData = new FormData();
            formData.append('action', 'ekle_siparise');
            formData.append('siparis_no', siparisNo);
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                Swal.fire({
                    title: data.status === 'success' ? 'Başarılı!' : 'Hata!',
                    text: data.message,
                    icon: data.status === 'success' ? 'success' : 'error',
                    confirmButtonText: 'Tamam'
                });
                if (data.status === 'success') {
                    const row = btn.closest('tr');
                    row.classList.add('siparis-eklendi');
                    btn.textContent = '✓';
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.4';
                }
            })
            .catch(() => {
                Swal.fire('Hata', 'Bağlantı hatası oluştu.', 'error');
            });
        });
    });
});

document.getElementById('veriCekBtn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="mdi mdi-loading mdi-spin me-1"></span> Yukleniyor...';

    Swal.fire({
        title: 'Veriler Guncelleniyor',
        text: 'iKas API\'den MeyruKids siparisleri cekiliyor. Bu islem 10-30 saniye surebilir.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('meyru_ikas_vericek.php')
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                title: data.status === 'success' ? 'Tamamlandi!' : 'Hata!',
                text: data.message,
                icon: data.status === 'success' ? 'success' : 'error',
                confirmButtonText: 'Tamam'
            }).then(() => {
                if (data.status === 'success') location.reload();
            });
        })
        .catch(() => {
            Swal.fire('Hata', 'Baglanti hatasi olustu.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="mdi mdi-refresh me-1"></span> Verileri Guncelle';
        });
});
</script>
</body>
</html>