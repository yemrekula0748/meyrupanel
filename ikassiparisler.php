<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'DB.php';
$db = new DB();

// --- Ara tablo yoksa oluştur ---
$db->query("CREATE TABLE IF NOT EXISTS ikas_bekleyen (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    siparis_no  VARCHAR(50) NOT NULL UNIQUE,
    musteri_ismi VARCHAR(255),
    adres       TEXT,
    tarih       DATETIME,
    sehir       VARCHAR(100),
    ilce        VARCHAR(100),
    urunler     TEXT,
    telefon     VARCHAR(50),
    kargo       VARCHAR(100),
    toplam_fiyat VARCHAR(50),
    ekleme_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// --- ikas_son'dan siparis_no > 2400 ve ikas_bekleyen'e eklenmemiş olanları al ---
$bekleyenler = $db->query(
    "SELECT * FROM ikas_son
     WHERE CAST(siparis_no AS UNSIGNED) > 2400
       AND siparis_no NOT IN (SELECT siparis_no FROM ikas_bekleyen)
     ORDER BY CAST(siparis_no AS UNSIGNED) DESC"
);

$eklenenler  = 0;
$hata_sayisi = 0;

while ($row = $bekleyenler->fetch_assoc()) {
    $ekle = $db->query(
        "INSERT IGNORE INTO ikas_bekleyen
         (siparis_no, musteri_ismi, adres, tarih, sehir, ilce, urunler, telefon, kargo, toplam_fiyat)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $row['siparis_no'],
            $row['musteri_ismi'],
            $row['adres'],
            $row['tarih'],
            $row['sehir'],
            $row['ilce'],
            $row['urunler'],
            $row['telefon'],
            $row['kargo'],
            $row['toplam_fiyat'],
        ],
        "ssssssssss"
    );
    if ($ekle) {
        $eklenenler++;
    } else {
        $hata_sayisi++;
    }
}

// --- Tüm ikas_bekleyen kayıtlarını listele (yeniden sorgula) ---�m ikas_bekleyen kayitlarini listele (yeniden sorgula) ---
$tumListele = $db->query(
    "SELECT * FROM ikas_bekleyen ORDER BY CAST(siparis_no AS UNSIGNED) DESC"
);
$toplamSayim = $db->query("SELECT COUNT(*) AS c FROM ikas_bekleyen")->fetch_assoc()['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satış Panel | iKas Bekleyen (2400+)</title>
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
        .cs-page-title { display: inline-flex; align-items: center; gap: 10px; font-weight: 700 !important; color: #c41a1a !important; }
        .cs-page-title-bar { width: 4px; height: 28px; background: linear-gradient(135deg, #c41a1a, #8b0f0f); border-radius: 4px; display: inline-block; }
        .alert-cs { padding: 12px 16px; border-left: 4px solid #c41a1a; background: #fff5f5; border-radius: .5rem; font-size: .9rem; }
        .badge { font-weight: 600 !important; border-radius: 9999px !important; }
        .tr-new td { background-color: #f0fff4 !important; }
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
                        iKas Bekleyen Siparişler — No &gt; 2400
                    </h4>
                </div>

                <div class="alert-cs mb-3">
                    <?php if ($eklenenler > 0): ?>
                        <strong class="text-success">✓ <?= $eklenenler ?> yeni sipariş <code>ikas_bekleyen</code> tablosuna eklendi.</strong>
                    <?php else: ?>
                        <strong>Yeni eklenecek sipariş bulunamadı.</strong> (Tüm 2400+ siparişler zaten kayıtlı.)
                    <?php endif; ?>
                    <?php if ($hata_sayisi > 0): ?>
                        <br><span class="text-danger"><?= $hata_sayisi ?> sipariş eklenirken hata oluştu.</span>
                    <?php endif; ?>
                    <span class="ms-3 text-muted" style="font-size:.82rem;">Toplam <strong><?= $toplamSayim ?></strong> kayıt</span>�m 2400+ siparisler zaten kayitli.)
                    <?php endif; ?>
                    <?php if ($hata_sayisi > 0): ?>
                        <br><span class="text-danger"><?= $hata_sayisi ?> siparis eklenirken hata olustu.</span>
                    <?php endif; ?>
                    <span class="ms-3 text-muted" style="font-size:.82rem;">Toplam <strong><?= $toplamSayim ?></strong> kayıt</span>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">ikas_bekleyen Tablosu</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($toplamSayim == 0): ?>
                            <p class="text-center text-muted py-4">Kayıt bulunamadı.</p>
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
                                        <th>Kargo</th>
                                        <th>Sipariş Tarihi</th>
                                        <th>Eklenme</th>�steri</th>
                                        <th>Telefon</th>
                                        <th>Il / Il�e</th>
                                        <th>�r�nler</th>
                                        <th>Tutar</th>
                                        <th>Kargo</th>
                                        <th>Siparis Tarihi</th>
                                        <th>Eklenme</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sayac = 1;
                                while ($r = $tumListele->fetch_assoc()):
                                    $isYeni = ($eklenenler > 0 && strtotime($r['ekleme_tarihi']) >= strtotime('-10 seconds'));
                                ?>
                                    <tr<?= $isYeni ? ' class="tr-new"' : '' ?>>
                                        <td><?= $sayac++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($r['siparis_no']) ?></strong>
                                            <?php if ($isYeni): ?><span class="badge bg-success ms-1">Yeni</span><?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($r['musteri_ismi'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($r['telefon'] ?? '') ?></td>
                                        <td><?= htmlspecialchars(($r['sehir'] ?? '-') . ' / ' . ($r['ilce'] ?? '-')) ?></td>
                                        <td class="text-start" style="font-size:.78rem;"><?= nl2br(htmlspecialchars($r['urunler'] ?? '')) ?></td>
                                        <td><strong><?= htmlspecialchars($r['toplam_fiyat'] ?? '-') ?> TL</strong></td>
                                        <td><?= htmlspecialchars($r['kargo'] ?? '-') ?></td>
                                        <td><?= !empty($r['tarih']) ? date('d-m-Y H:i', strtotime($r['tarih'])) : '-' ?></td>
                                        <td style="font-size:.75rem;color:#6b7280;"><?= !empty($r['ekleme_tarihi']) ? date('d-m-Y H:i', strtotime($r['ekleme_tarihi'])) : '-' ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <?php include 'tema/footer.php'; ?>
    </div>
</div>
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
