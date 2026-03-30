<?php
// Giriş kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Kullanıcı giriş yapmadıysa login sayfasına yönlendir
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
	
	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">


    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
	
	   <!-- excel indir js dosyası -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	
	 <!-- pdf indir js dosyası -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">


    


	<script src="font.js"></script>
	



    
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
                        </div>
                    

               

<?php

require_once 'DB.php';
$db = new DB();
$bugun = date('Y-m-d');

// iKas Son tablosu için
$sorgu = $db->query("SELECT COUNT(*) AS toplam FROM ikas_son WHERE DATE(tarih) = ?", [$bugun], "s");
$sayi = 0;
if ($sorgu && $row = $sorgu->fetch_assoc()) {
    $sayi = $row['toplam'];
}

// Siparişler tablosu için (sisteme girilen iKas sipariş sayısı)
$sorgu2 = $db->query("SELECT COUNT(*) AS toplam FROM siparisler WHERE DATE(siparis_tarihi) = ? AND ikasmi = 1", [$bugun], "s");
$sayi2 = 0;
if ($sorgu2 && $row2 = $sorgu2->fetch_assoc()) {
    $sayi2 = $row2['toplam'];
}
?>
<div class="row mb-2">
    <div class="col-auto">
        <div class="card border-success shadow-sm" style="min-width:180px; max-width:220px;">
            <div class="card-body py-2 px-3 text-center">
                <div class="small text-muted mb-1" style="font-size: 0.95rem;">Web Sitesine Bugün Gelen İkas Sipariş Sayısı</div>
                <span class="fw-semibold text-success" style="font-size: 1.5rem;"><?= $sayi ?></span>
            </div>
        </div>
    </div>
    <div class="col-auto">
        <div class="card border-primary shadow-sm" style="min-width:180px; max-width:220px;">
            <div class="card-body py-2 px-3 text-center">
                <div class="small text-muted mb-1" style="font-size: 0.95rem;">Panele Çekilen iKas Sipariş Sayısı</div>
                <span class="fw-semibold text-warning" style="font-size: 1.5rem;"><?= $sayi2 ?></span>
            </div>
        </div>
    </div>
</div>       

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-start align-items-center mb-3">
                            <!-- Sol tarafta tüm butonlar -->
                            
    <button class="btn btn-success me-2" id="ikasAktarBtn">
        iKas Siparişlerini Aktar
    </button>
               <script>
document.getElementById('ikasAktarBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'iKas Siparişlerini Aktar',
        text: 'iKas siparişlerini siparişler tablosuna aktarmak istediğinize emin misiniz?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Evet, Aktar',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('ikassiparislerisiparislereaktar.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: data.status === 'success' ? 'Başarılı!' : 'Hata!',
                    text: data.message,
                    icon: data.status === 'success' ? 'success' : 'error',
                    confirmButtonText: 'Tamam'
                });
            })
            .catch(() => {
                Swal.fire({
                    title: 'İşlem Başlatıldı',
                    text: 'Siparişler Aktarılıyor',
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                });
            });
        }
    });
});
</script>             

							<button class="btn btn-warning me-2" onclick="window.location.href='export_excel.php'">Excel İndir</button>
                            
                            <button class="btn btn-warning" id="guncelleBtn">Siparişleri Resmileştir</button>



                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#siparisGirModal">
                                Sipariş Gir
                            </button>

                        </div>


                    <div class="card">
                          <div id="sonuc"></div>
                        <div class="card-header">
                            <h5 class="card-title mb-0">Siparişler Tablosu</h5>
                        </div>

                        <?php
$counter = 1; // Sayaç başlangıç değeri ?>

                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center align-middle">
                                <thead>
                                        <tr>
                                                                                            <th>Sipariş</th>
                                                                                        <th>ID</th>
                                                                                        <th>BARKOD NO</th>
                                            <th>FATURA NO</th>
                                            <th>ÜRÜNLER</th>
                                            <th>MÜŞTERİ</th>
                                            <th>KARGO DAHIL</th>
                                            <th>EKLEME TARİHİ</th>
                                            <th>RESMİ FATURA</th>
                                            <th>BARKOD</th>
                                            <th>TUTAR</th>
                                            <th>İŞLEM</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once 'DB.php';
                                        $db = new DB();
                                        $result = $db->query("SELECT * FROM siparisler WHERE islem = 0 ORDER BY id DESC LIMIT 100");

                                        while ($row = $result->fetch_assoc()) {
                                            // Müşteri bilgileri tek hücrede alt alta
                                            $musteriBilgileri = $row['musteri_ismi'] . "<br>" . $row['musteri_telefonu'] . "<br>" . $row['musteri_adresi'];

                                            // Kargo durumu
                                            $kargoDurumu = match ($row['kargo']) {
                                                'Ödeme Şartlı' => 'MH',
                                                'Bedelsiz' => 'B',
                                                'Ücreti Alıcıdan' => 'UA',
                                                default => $row['kargo']
                                            };

                                            $odemeDurumu = match ($row['hangikargo']) {
                                                'MeyruKids' => 'Meyru Kids',
                                                'Sevim Aydın - PTT' => 'Sevim Aydın',
                                                'Yunus Emre - Hepsijet' => 'HepsiJET',
                                                '' => 'İkas',
                                                default => ''
                                            };

                                            // Resmileşme durumu
                                            $resmilesmeDurumu = $row['faturalandirma_durumu'] === "Faturalandırılmadı" ? '✖︎' : '✓';

                                            // Barkod durumu
                                            $barkodDurumu = $row['barkod_basilma_durumu'] === "Basılmamış" ? '✖︎' : '✓';

                                            // Ödeme şartı
                                            if ($row['odeme_sarti'] == 0 || $row['odeme_sarti'] === null) {
    $odemeSarti = $kargoDurumu;  // 0 veya null ise sadece $kargoDurumu yazdırılır.
} else {
    $odemeSarti = $row['odeme_sarti'] . " TL";  // Aksi takdirde, TL eklenir.
}

                                        ?>
                                            <tr>
                                         

<td><?= $counter++ ?></td>
<td><?= htmlspecialchars($row['id'] ?? '') ?></td>
                                                <td>
    <?php
        $barkodNo = $row['kargo_barkodu'] ?? '';
        $kargoLink = $row['kargolink'] ?? '';
        if ($barkodNo === null || $barkodNo === '') {
            echo 'Oluşturuluyor';
        } elseif (strpos($barkodNo, 'SMR') === 0) {
            echo '<a href="https://www.hepsijet.com/gonderi-takibi/' . htmlspecialchars($barkodNo) . '" target="_blank">' . htmlspecialchars($barkodNo) . '</a>';
        } elseif (!empty($kargoLink)) {
            echo '<a href="' . htmlspecialchars($kargoLink) . '" target="_blank">' . htmlspecialchars($barkodNo) . '</a>';
        } else {
            echo htmlspecialchars($barkodNo);
        }
    ?>
</td>



                                              <td>
    <?php if (!empty($row['parasut_fatura_numarasi'])): ?>
        <?= htmlspecialchars($row['parasut_fatura_numarasi']) ?>
    <?php elseif ($row['kargo'] === 'Bedelsiz'): ?>
        <span class="badge rounded-pill text-bg-warning">Bedelsiz</span>
    <?php elseif ($row['kargo'] === 'Ücreti Alıcıdan'): ?>
        <span class="badge rounded-pill text-bg-warning">UA</span>
    <?php else: ?>
        <span class="badge rounded-pill text-bg-info">MEY<?= htmlspecialchars($row['id']) ?></span>

    <?php endif; ?>
</td>


                                                <style>
                                                    .text-white {
                                                        color: red;
                                                    }
                                                </style>
                                                <td><?= htmlspecialchars($row['urunler']) ?></td>
                                                <td><?= $musteriBilgileri ?></td>
                                                <td><?= htmlspecialchars($kargoDurumu) ?></td>
                                               <td><?= date('d-m-Y H:i', strtotime($row['siparis_tarihi'])) ?></td>
                                                <td><?= htmlspecialchars($resmilesmeDurumu) ?></td>
                                                <td><?= htmlspecialchars($barkodDurumu) ?></td>
                                                <td><?= htmlspecialchars($odemeSarti) ?></td>
                                                <td class="text-center">
                                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                                    <!-- Düzenle İkonu -->
                                                    <span class="mdi mdi-pencil-outline text-primary" 
                                                        style="font-size: 18px; cursor: pointer;" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#duzenleModal<?= htmlspecialchars($row['id']) ?>" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Düzenle">
                                                    </span>

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

<script>
function handleMiddleClick(event) {
    if (event.button === 1) { // Orta fare tuşu
        event.preventDefault(); // Sayfanın yenilenmesini engelle
        var id = new URL(event.target.href).searchParams.get('id');
        
        // AJAX isteği ile silme işlemi
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'siparis_sil.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Başarılı silme işleminden sonra öğeyi sayfadan sil
                var item = document.getElementById('item-' + id); // öğeyi id'siyle bul
                if (item) {
                    item.remove(); // öğeyi kaldır
                }
            }
        };
        xhr.send('id=' + id);
    }
}
</script>


                                                </div>
                                            </td>
                                            <!-- Resmileştir butonu ekle -->
                                            <!--<td>
                                                <?php if($row['parasut_resmilesme_durumu'] == 0 && $row['islem'] == 0 && $row['iptalmi'] == 0): ?>
                                                    <button class="btn btn-warning btn-sm" onclick="resmilestir(<?= $row['id'] ?>)">Resmileştir</button>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Resmileştirildi</span>
                                                <?php endif; ?>
                                            </td>-->

                                            </tr><!-- Düzenle Modal -->
                                            <div class="modal fade" id="duzenleModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="duzenleModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="siparis_duzenle.php" method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="duzenleModalLabel<?php echo $row['id']; ?>">Sipariş Düzenle</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                                <div class="mb-3">
                                                                    <label for="musteri_ismi" class="form-label">Müşteri Adı</label>
                                                                    <input type="text" class="form-control" name="musteri_ismi" value="<?php echo htmlspecialchars($row['musteri_ismi']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="musteri_telefonu" class="form-label">Telefon</label>
                                                                    <input type="text" class="form-control" name="musteri_telefonu" value="<?php echo htmlspecialchars($row['musteri_telefonu']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="musteri_adresi" class="form-label">Adres</label>
                                                                    <textarea class="form-control" name="musteri_adresi" required><?php echo htmlspecialchars($row['musteri_adresi']); ?></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="siparis_tarihi" class="form-label">Sipariş Tarihi</label>
                                                                    <input type="date" class="form-control" name="siparis_tarihi" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($row['siparis_tarihi']))); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="odeme_sarti" class="form-label">Ödeme Şartı</label>
                                                                    <input type="text" class="form-control" name="odeme_sarti" value="<?php echo htmlspecialchars($row['odeme_sarti']); ?>" disabled>
                                                                    </div>
                                                                <div class="mb-3">
                                                                    <label for="urunler" class="form-label">Ürünler</label>
                                                                    <textarea class="form-control" name="urunler"><?php echo htmlspecialchars($row['urunler']); ?></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="yonetici_notu" class="form-label">Yönetici Notu</label>
                                                                    <textarea class="form-control" name="yonetici_notu"><?php echo htmlspecialchars($row['yonetici_notu']); ?></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="faturalandirma_durumu" class="form-label">Fatura Durumu</label>
                                                                    <select class="form-select" name="faturalandirma_durumu">
                                                                        <option value="1" <?php echo $row['faturalandirma_durumu'] == 1 ? 'selected' : ''; ?>>Faturalandı</option>
                                                                        <option value="0" <?php echo $row['faturalandirma_durumu'] == 0 ? 'selected' : ''; ?>>Faturalandırılmadı</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="barkod_basilma_durumu" class="form-label">Barkod Durumu</label>
                                                                    <select class="form-select" name="barkod_basilma_durumu">
                                                                        <option value="1" <?php echo $row['barkod_basilma_durumu'] == 1 ? 'selected' : ''; ?>>Basılmış</option>
                                                                        <option value="0" <?php echo $row['barkod_basilma_durumu'] == 0 ? 'selected' : ''; ?>>Basılmamış</option>
                                                                    </select>
                                                                </div>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                                <button type="submit" class="btn btn-primary">Kaydet</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Sil Modal -->
                                            <div class="modal fade" id="silModal<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="silModalLabel<?= htmlspecialchars($row['id']) ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="siparis_sil.php" method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="silModalLabel<?= htmlspecialchars($row['id']) ?>">Siparişi Sil</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Bu siparişi silmek istediğinizden emin misiniz?</p>
                                                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                                <button type="submit" class="btn btn-danger">Sil</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                  
        </div>

            <?php include 'tema/footer.php'; ?>
        </div>
    </div>
                                        <!-- Resmileştir Modal -->
                                        <div class="modal fade" id="resmilestirModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="resmilestirModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="faturaresmilestir.php" method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="resmilestirModalLabel<?php echo $row['id']; ?>">Siparişi Resmileştir</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                            <p>Bu siparişi resmileştirmek istediğinizden emin misiniz?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                            <button type="submit" class="btn btn-warning">Evet, Resmileştir</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sipariş Gir Modal -->
<div class="modal fade" id="siparisGirModal" tabindex="-1" aria-labelledby="siparisGirModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="siparisGirModalLabel">Sipariş Girişi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: 0.85rem; padding: 15px;">
                <!-- Sipariş Gir Formu -->
              <form action="siparis_gir_sonuc.php" method="post">
                    

                    <!-- Gizli Inputlar -->
                    <input type="hidden" name="user_name" value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    <input type="hidden" name="faturalandirma_durumu" value="Faturalandırılmadı">
                    <input type="hidden" name="barkod_basilma_durumu" value="Basılmamış">

                    <div class="row g-3 align-items-center">
                        <!-- Otomatik Doldur -->
                        <div class="col-3">
                            <label for="otodoldur" class="form-label">Otomatik Doldur</label>
                        </div>
                       <div class="col-9">
    <textarea id="otodoldur" name="otodoldur" class="form-control form-control-sm" rows="7"></textarea>
</div>



                        <!-- Müşteri İsmi -->
                        <div class="col-3">
                            <label for="musteri_ismi" class="form-label">Müşteri İsmi</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="musteri_ismi" name="musteri_ismi" class="form-control form-control-sm" placeholder="Örn: Ahmet Yılmaz" required>
                        </div>

                        <!-- Müşteri Telefonu -->
                        <div class="col-3">
                            <label for="musteri_telefonu" class="form-label">Müşteri Telefonu</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="musteri_telefonu" name="musteri_telefonu" class="form-control form-control-sm" placeholder="Örn: 05xx xxx xx xx" required>
                        </div>

                        <!-- Müşteri Adresi -->
                        <div class="col-3">
                            <label for="musteri_adresi" class="form-label">Müşteri Adresi</label>
                        </div>
                        <div class="col-9">
                            <textarea id="musteri_adresi" name="musteri_adresi" class="form-control form-control-sm" rows="2" required></textarea>
                        </div>

                        <!-- İl ve İlçe -->
                        <div class="col-3">
                            <label for="Iller" class="form-label">Müşteri İl</label>
                        </div>
                        <div class="col-9">
                            <select id="Iller" name="Iller" class="form-select form-select-sm" required>
                                <option value="">İl Seçin</option>
                            </select>
                        </div>

                        <div class="col-3">
                            <label for="Ilceler" class="form-label">Müşteri İlçe</label>
                        </div>
                        <div class="col-9">
                            <select id="Ilceler" name="Ilceler" class="form-select form-select-sm" required>
                                <option value="">Önce İl Seçin</option>
                            </select>
                        </div>

                        <!-- Ödeme Şartı -->
                        <div class="col-3">
                            <label for="odeme_sarti" class="form-label">Ödeme Şartı</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="odeme_sarti" name="odeme_sarti" class="form-control form-control-sm" placeholder="Örn: 150" required>
                        </div>

                        <!-- Ürünler -->
                        <div class="col-3">
                            <label for="urunler" class="form-label">Ürünler</label>
                        </div>
                        <div class="col-9">
                            <textarea id="urunler" name="urunler" class="form-control form-control-sm" rows="5" required></textarea>
                        </div>

                        <!-- Desi ve Ağırlık -->
                        <div class="col-3">
                            <label for="desi" class="form-label">Desi</label>
                        </div>
                        <div class="col-3">
                            <input type="number" id="desi" name="desi" class="form-control form-control-sm" value="1" readonly>
                        </div>
                        <div class="col-3">
                            <label for="agirlik" class="form-label">Ağırlık</label>
                        </div>
                        <div class="col-3">
                            <input type="number" id="agirlik" name="agirlik" class="form-control form-control-sm" value="430" readonly>
                        </div>

                        <!-- Kargo -->
                        <div class="col-3">
                            <label for="kargo" class="form-label">Kargo</label>
                        </div>
                        <div class="col-9">
                            <select id="kargo" name="kargo" class="form-select form-select-sm" required>
                                <option value="Ödeme Şartlı">Ödeme Şartlı</option>
                                <option value="Bedelsiz">Bedelsiz</option>
                                <option value="Ücreti Alıcıdan">Ücreti Alıcıdan</option>
                            </select>
                        </div>

                        <!-- Yönetici Notu -->
                        <div class="col-3">
                            <label for="yoneticinotu" class="form-label">Yönetici Notu</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="yoneticinotu" name="yoneticinotu" class="form-control form-control-sm">
                        </div>

                        <!-- Sipariş Sayfası -->
                        <div class="col-3">
                            <label for="siparissayfasi" class="form-label">Sipariş Sayfası</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="siparissayfasi" name="siparissayfasi" class="form-control form-control-sm" placeholder="Örnek: Semrebutik DM">
                        </div>

                        <!-- Sipariş Tarihi -->
                        <div class="col-3">
                            <label for="siparis_tarihi" class="form-label">Sipariş Tarihi</label>
                        </div>
<?php
$date = new DateTime('now', new DateTimeZone('UTC')); // Şu anki UTC zamanı alır
$date->modify('+3 hours'); // UTC+3 zamanını ekler
$formattedDate = $date->format('Y-m-d\TH:i'); // HTML için uygun formata çevirir
?>
<div class="col-9">
    <input type="datetime-local" id="siparis_tarihi" name="siparis_tarihi" class="form-control form-control-sm" value="<?= $formattedDate; ?>" readonly>
</div>

						
						<div class="col-md-12" style="text-align: right;">
    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">İptal</button>
    <button type="submit" class="btn btn-info">Sipariş Ekle</button>
</div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>





                                        <style>
                                        .form-container {
                                            max-width: 400px; /* Genişliği sınırlayın */
                                            margin: 0 auto; /* Ortalamak için */
                                        }
                                        </style>





                                    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>







   <script> 


var data = [
    {
        "il": "Adana",
        "plaka": 1,
        "ilceleri": [
            "Aladağ",
            "Ceyhan",
            "Çukurova",
            "Feke",
            "İmamoğlu",
            "Karaisalı",
            "Karataş",
            "Kozan",
            "Pozantı",
            "Saimbeyli",
            "Sarıçam",
            "Seyhan",
            "Tufanbeyli",
            "Yumurtalık",
            "Yüreğirr"
        ]
    },
    {
        "il": "Adıyaman",
        "plaka": 2,
        "ilceleri": [
            "Besni",
            "Çelikhan",
            "Gerger",
            "Gölbaşı",
            "Kahta",
            "Merkez",
            "Samsat",
            "Sincik",
            "Tut"
        ]
    },
    {
        "il": "Afyonkarahisar",
        "plaka": 3,
        "ilceleri": [
            "Başmakçı",
            "Bayat",
            "Bolvadin",
            "Çay",
            "Çobanlar",
            "Dazkırı",
            "Dinar",
            "Emirdağ",
            "Evciler",
            "Hocalar",
            "İhsaniye",
            "İscehisar",
            "Kızılören",
            "Merkez",
            "Sandıklı",
            "Sinanpaşa",
            "Sultandağı",
            "Şuhut"
        ]
    },
    {
        "il": "Ağrı",
        "plaka": 4,
        "ilceleri": [
            "Diyadin",
            "Doğubayazıt",
            "Eleşkirt",
            "Hamur",
            "Merkez",
            "Patnos",
            "Taşlıçay",
            "Tutak"
        ]
    },
    {
        "il": "Amasya",
        "plaka": 5,
        "ilceleri": [
            "Göynücek",
            "Gümüşhacıköy",
            "Hamamözü",
            "Merkez",
            "Merzifon",
            "Suluova",
            "Taşova"
        ]
    },
    {
        "il": "Ankara",
        "plaka": 6,
        "ilceleri": [
            "Altındağ",
            "Ayaş",
            "Bala",
            "Beypazarı",
            "Çamlıdere",
            "Çankaya",
            "Çubuk",
            "Elmadağ",
            "Güdül",
            "Haymana",
            "Kalecik",
            "Kızılcahamam",
            "Nallıhan",
            "Polatlı",
            "Şereflikoçhisar",
            "Yenimahalle",
            "Gölbaşı",
            "Keçiören",
            "Mamak",
            "Sincan",
            "Kazan",
            "Akyurt",
            "Etimesgut",
            "Evren",
            "Pursaklar"
        ]
    },
    {
        "il": "Antalya",
        "plaka": 7,
        "ilceleri": [
            "Akseki",
            "Alanya",
            "Elmalı",
            "Finike",
            "Gazipaşa",
            "Gündoğmuş",
            "Kaş",
            "Korkuteli",
            "Kumluca",
            "Manavgat",
            "Serik",
            "Demre",
            "İbradı",
            "Kemer",
            "Aksu",
            "Döşemealtı",
            "Kepez",
            "Konyaaltı",
            "Muratpaşa"
        ]
    },
    {
        "il": "Artvin",
        "plaka": 8,
        "ilceleri": [
            "Ardanuç",
            "Arhavi",
            "Merkez",
            "Borçka",
            "Hopa",
            "Şavşat",
            "Yusufeli",
            "Murgul"
        ]
    },
    {
        "il": "Aydın",
        "plaka": 9,
        "ilceleri": [
            "Merkez",
            "Bozdoğan",
            "Efeler",
            "Çine",
            "Germencik",
            "Karacasu",
            "Koçarlı",
            "Kuşadası",
            "Kuyucak",
            "Nazilli",
            "Söke",
            "Sultanhisar",
            "Yenipazar",
            "Buharkent",
            "İncirliova",
            "Karpuzlu",
            "Köşk",
            "Didim"
        ]
    },
    {
        "il": "Balıkesir",
        "plaka": 10,
        "ilceleri": [
            "Altıeylül",
            "Ayvalık",
            "Merkez",
            "Balya",
            "Bandırma",
            "Bigadiç",
            "Burhaniye",
            "Dursunbey",
            "Edremit",
            "Erdek",
            "Gönen",
            "Havran",
            "İvrindi",
            "Karesi",
            "Kepsut",
            "Manyas",
            "Savaştepe",
            "Sındırgı",
            "Gömeç",
            "Susurluk",
            "Marmara"
        ]
    },
    {
        "il": "Bilecik",
        "plaka": 11,
        "ilceleri": [
            "Merkez",
            "Bozüyük",
            "Gölpazarı",
            "Osmaneli",
            "Pazaryeri",
            "Söğüt",
            "Yenipazar",
            "İnhisar"
        ]
    },
    {
        "il": "Bingöl",
        "plaka": 12,
        "ilceleri": [
            "Merkez",
            "Genç",
            "Karlıova",
            "Kiğı",
            "Solhan",
            "Adaklı",
            "Yayladere",
            "Yedisu"
        ]
    },
    {
        "il": "Bitlis",
        "plaka": 13,
        "ilceleri": [
            "Adilcevaz",
            "Ahlat",
            "Merkez",
            "Hizan",
            "Mutki",
            "Tatvan",
            "Güroymak"
        ]
    },
    {
        "il": "Bolu",
        "plaka": 14,
        "ilceleri": [
            "Merkez",
            "Gerede",
            "Göynük",
            "Kıbrıscık",
            "Mengen",
            "Mudurnu",
            "Seben",
            "Dörtdivan",
            "Yeniçağa"
        ]
    },
    {
        "il": "Burdur",
        "plaka": 15,
        "ilceleri": [
            "Ağlasun",
            "Bucak",
            "Merkez",
            "Gölhisar",
            "Tefenni",
            "Yeşilova",
            "Karamanlı",
            "Kemer",
            "Altınyayla",
            "Çavdır",
            "Çeltikçi"
        ]
    },
    {
        "il": "Bursa",
        "plaka": 16,
        "ilceleri": [
            "Gemlik",
            "İnegöl",
            "İznik",
            "Karacabey",
            "Keles",
            "Mudanya",
            "Mustafakemalpaşa",
            "Orhaneli",
            "Orhangazi",
            "Yenişehir",
            "Büyükorhan",
            "Harmancık",
            "Nilüfer",
            "Osmangazi",
            "Yıldırım",
            "Gürsu",
            "Kestel"
        ]
    },
    {
        "il": "Çanakkale",
        "plaka": 17,
        "ilceleri": [
            "Ayvacık",
            "Bayramiç",
            "Biga",
            "Bozcaada",
            "Çan",
            "Merkez",
            "Eceabat",
            "Ezine",
            "Gelibolu",
            "Gökçeada",
            "Lapseki",
            "Yenice"
        ]
    },
    {
        "il": "Çankırı",
        "plaka": 18,
        "ilceleri": [
            "Merkez",
            "Çerkeş",
            "Eldivan",
            "Ilgaz",
            "Kurşunlu",
            "Orta",
            "Şabanözü",
            "Yapraklı",
            "Atkaracalar",
            "Kızılırmak",
            "Bayramören",
            "Korgun"
        ]
    },
    {
        "il": "Çorum",
        "plaka": 19,
        "ilceleri": [
            "Alaca",
            "Bayat",
            "Merkez",
            "İskilip",
            "Kargı",
            "Mecitözü",
            "Ortaköy",
            "Osmancık",
            "Sungurlu",
            "Boğazkale",
            "Uğurludağ",
            "Dodurga",
            "Laçin",
            "Oğuzlar"
        ]
    },
    {
        "il": "Denizli",
        "plaka": 20,
        "ilceleri": [
            "Acıpayam",
            "Buldan",
            "Çal",
            "Çameli",
            "Çardak",
            "Çivril",
            "Merkez",
            "Merkezefendi",
            "Pamukkale",
            "Güney",
            "Kale",
            "Sarayköy",
            "Tavas",
            "Babadağ",
            "Bekilli",
            "Honaz",
            "Serinhisar",
            "Baklan",
            "Beyağaç",
            "Bozkurt"
        ]
    },
    {
        "il": "Diyarbakır",
        "plaka": 21,
        "ilceleri": [
            "Kocaköy",
            "Çermik",
            "Çınar",
            "Çüngüş",
            "Dicle",
            "Ergani",
            "Hani",
            "Hazro",
            "Kulp",
            "Lice",
            "Silvan",
            "Eğil",
            "Bağlar",
            "Kayapınar",
            "Sur",
            "Yenişehir",
            "Bismil"
        ]
    },
    {
        "il": "Edirne",
        "plaka": 22,
        "ilceleri": [
            "Merkez",
            "Enez",
            "Havsa",
            "İpsala",
            "Keşan",
            "Lalapaşa",
            "Meriç",
            "Uzunköprü",
            "Süloğlu"
        ]
    },
    {
        "il": "Elazığ",
        "plaka": 23,
        "ilceleri": [
            "Ağın",
            "Baskil",
            "Merkez",
            "Karakoçan",
            "Keban",
            "Maden",
            "Palu",
            "Sivrice",
            "Arıcak",
            "Kovancılar",
            "Alacakaya"
        ]
    },
    {
        "il": "Erzincan",
        "plaka": 24,
        "ilceleri": [
            "Çayırlı",
            "Merkez",
            "İliç",
            "Kemah",
            "Kemaliye",
            "Refahiye",
            "Tercan",
            "Üzümlü",
            "Otlukbeli"
        ]
    },
    {
        "il": "Erzurum",
        "plaka": 25,
        "ilceleri": [
            "Aşkale",
            "Çat",
            "Hınıs",
            "Horasan",
            "İspir",
            "Karayazı",
            "Narman",
            "Oltu",
            "Olur",
            "Pasinler",
            "Şenkaya",
            "Tekman",
            "Tortum",
            "Karaçoban",
            "Uzundere",
            "Pazaryolu",
            "Köprüköy",
            "Palandöken",
            "Yakutiye",
            "Aziziye"
        ]
    },
    {
        "il": "Eskişehir",
        "plaka": 26,
        "ilceleri": [
            "Çifteler",
            "Mahmudiye",
            "Mihalıççık",
            "Sarıcakaya",
            "Seyitgazi",
            "Sivrihisar",
            "Alpu",
            "Beylikova",
            "İnönü",
            "Günyüzü",
            "Han",
            "Mihalgazi",
            "Odunpazarı",
            "Tepebaşı"
        ]
    },
    {
        "il": "Gaziantep",
        "plaka": 27,
        "ilceleri": [
            "Araban",
            "İslahiye",
            "Nizip",
            "Oğuzeli",
            "Yavuzeli",
            "Şahinbey",
            "Şehitkamil",
            "Karkamış",
            "Nurdağı"
        ]
    },
    {
        "il": "Giresun",
        "plaka": 28,
        "ilceleri": [
            "Alucra",
            "Bulancak",
            "Dereli",
            "Espiye",
            "Eynesil",
            "Merkez",
            "Görele",
            "Keşap",
            "Şebinkarahisar",
            "Tirebolu",
            "Piraziz",
            "Yağlıdere",
            "Çamoluk",
            "Çanakçı",
            "Doğankent",
            "Güce"
        ]
    },
    {
        "il": "Gümüşhane",
        "plaka": 29,
        "ilceleri": [
            "Merkez",
            "Kelkit",
            "Şiran",
            "Torul",
            "Köse",
            "Kürtün"
        ]
    },
    {
        "il": "Hakkari",
        "plaka": 30,
        "ilceleri": [
            "Çukurca",
            "Merkez",
            "Şemdinli",
            "Yüksekova"
        ]
    },
    {
        "il": "Hatay",
        "plaka": 31,
        "ilceleri": [
            "Altınözü",
            "Arsuz",
            "Defne",
            "Dörtyol",
            "Hassa",
            "Antakya",
            "İskenderun",
            "Kırıkhan",
            "Payas",
            "Reyhanlı",
            "Samandağ",
            "Yayladağı",
            "Erzin",
            "Belen",
            "Kumlu"
        ]
    },
    {
        "il": "Isparta",
        "plaka": 32,
        "ilceleri": [
            "Atabey",
            "Eğirdir",
            "Gelendost",
            "Merkez",
            "Keçiborlu",
            "Senirkent",
            "Sütçüler",
            "Şarkikaraağaç",
            "Uluborlu",
            "Yalvaç",
            "Aksu",
            "Gönen",
            "Yenişarbademli"
        ]
    },
    {
        "il": "Mersin",
        "plaka": 33,
        "ilceleri": [
            "Anamur",
            "Erdemli",
            "Gülnar",
            "Mut",
            "Silifke",
            "Tarsus",
            "Aydıncık",
            "Bozyazı",
            "Çamlıyayla",
            "Akdeniz",
            "Mezitli",
            "Toroslar",
            "Yenişehir"
        ]
    },
    {
        "il": "İstanbul",
        "plaka": 34,
        "ilceleri": [
            "Adalar",
            "Bakırköy",
            "Beşiktaş",
            "Beykoz",
            "Beyoğlu",
            "Çatalca",
            "Eyüp",
            "Fatih",
            "Gaziosmanpaşa",
            "Kadıköy",
            "Kartal",
            "Sarıyer",
            "Silivri",
            "Şile",
            "Şişli",
            "Üsküdar",
            "Zeytinburnu",
            "Büyükçekmece",
            "Kağıthane",
            "Küçükçekmece",
            "Pendik",
            "Ümraniye",
            "Bayrampaşa",
            "Avcılar",
            "Bağcılar",
            "Bahçelievler",
            "Güngören",
            "Maltepe",
            "Sultanbeyli",
            "Tuzla",
            "Esenler",
            "Arnavutköy",
            "Ataşehir",
            "Başakşehir",
            "Beylikdüzü",
            "Çekmeköy",
            "Esenyurt",
            "Sancaktepe",
            "Sultangazi"
        ]
    },
    {
        "il": "İzmir",
        "plaka": 35,
        "ilceleri": [
            "Aliağa",
            "Bayındır",
            "Bergama",
            "Bornova",
            "Çeşme",
            "Dikili",
            "Foça",
            "Karaburun",
            "Karşıyaka",
            "Kemalpaşa",
            "Kınık",
            "Kiraz",
            "Menemen",
            "Ödemiş",
            "Seferihisar",
            "Selçuk",
            "Tire",
            "Torbalı",
            "Urla",
            "Beydağ",
            "Buca",
            "Konak",
            "Menderes",
            "Balçova",
            "Çiğli",
            "Gaziemir",
            "Narlıdere",
            "Güzelbahçe",
            "Bayraklı",
            "Karabağlar"
        ]
    },
    {
        "il": "Kars",
        "plaka": 36,
        "ilceleri": [
            "Arpaçay",
            "Digor",
            "Kağızman",
            "Merkez",
            "Sarıkamış",
            "Selim",
            "Susuz",
            "Akyaka"
        ]
    },
    {
        "il": "Kastamonu",
        "plaka": 37,
        "ilceleri": [
            "Abana",
            "Araç",
            "Azdavay",
            "Bozkurt",
            "Cide",
            "Çatalzeytin",
            "Daday",
            "Devrekani",
            "İnebolu",
            "Merkez",
            "Küre",
            "Taşköprü",
            "Tosya",
            "İhsangazi",
            "Pınarbaşı",
            "Şenpazar",
            "Ağlı",
            "Doğanyurt",
            "Hanönü",
            "Seydiler"
        ]
    },
    {
        "il": "Kayseri",
        "plaka": 38,
        "ilceleri": [
            "Bünyan",
            "Develi",
            "Felahiye",
            "İncesu",
            "Pınarbaşı",
            "Sarıoğlan",
            "Sarız",
            "Tomarza",
            "Yahyalı",
            "Yeşilhisar",
            "Akkışla",
            "Talas",
            "Kocasinan",
            "Melikgazi",
            "Hacılar",
            "Özvatan"
        ]
    },
    {
        "il": "Kırklareli",
        "plaka": 39,
        "ilceleri": [
            "Babaeski",
            "Demirköy",
            "Merkez",
            "Kofçaz",
            "Lüleburgaz",
            "Pehlivanköy",
            "Pınarhisar",
            "Vize"
        ]
    },
    {
        "il": "Kırşehir",
        "plaka": 40,
        "ilceleri": [
            "Çiçekdağı",
            "Kaman",
            "Merkez",
            "Mucur",
            "Akpınar",
            "Akçakent",
            "Boztepe"
        ]
    },
    {
        "il": "Kocaeli",
        "plaka": 41,
        "ilceleri": [
            "Gebze",
            "Gölcük",
            "Kandıra",
            "Karamürsel",
            "Körfez",
            "Derince",
            "Başiskele",
            "Çayırova",
            "Darıca",
            "Dilovası",
            "İzmit",
            "Kartepe"
        ]
    },
    {
        "il": "Konya",
        "plaka": 42,
        "ilceleri": [
            "Akşehir",
            "Beyşehir",
            "Bozkır",
            "Cihanbeyli",
            "Çumra",
            "Doğanhisar",
            "Ereğli",
            "Hadim",
            "Ilgın",
            "Kadınhanı",
            "Karapınar",
            "Kulu",
            "Sarayönü",
            "Seydişehir",
            "Yunak",
            "Akören",
            "Altınekin",
            "Derebucak",
            "Hüyük",
            "Karatay",
            "Meram",
            "Selçuklu",
            "Taşkent",
            "Ahırlı",
            "Çeltik",
            "Derbent",
            "Emirgazi",
            "Güneysınır",
            "Halkapınar",
            "Tuzlukçu",
            "Yalıhüyük"
        ]
    },
    {
        "il": "Kütahya",
        "plaka": 43,
        "ilceleri": [
            "Altıntaş",
            "Domaniç",
            "Emet",
            "Gediz",
            "Merkez",
            "Simav",
            "Tavşanlı",
            "Aslanapa",
            "Dumlupınar",
            "Hisarcık",
            "Şaphane",
            "Çavdarhisar",
            "Pazarlar"
        ]
    },
    {
        "il": "Malatya",
        "plaka": 44,
        "ilceleri": [
            "Akçadağ",
            "Arapgir",
            "Arguvan",
            "Darende",
            "Doğanşehir",
            "Hekimhan",
            "Merkez",
            "Pütürge",
            "Yeşilyurt",
            "Battalgazi",
            "Doğanyol",
            "Kale",
            "Kuluncak",
            "Yazıhan"
        ]
    },
    {
        "il": "Manisa",
        "plaka": 45,
        "ilceleri": [
            "Akhisar",
            "Alaşehir",
            "Demirci",
            "Gördes",
            "Kırkağaç",
            "Kula",
            "Merkez",
            "Salihli",
            "Sarıgöl",
            "Saruhanlı",
            "Selendi",
            "Soma",
            "Şehzadeler",
            "Yunusemre",
            "Turgutlu",
            "Ahmetli",
            "Gölmarmara",
            "Köprübaşı"
        ]
    },
    {
        "il": "Kahramanmaraş",
        "plaka": 46,
        "ilceleri": [
            "Afşin",
            "Andırın",
            "Dulkadiroğlu",
            "Onikişubat",
            "Elbistan",
            "Göksun",
            "Merkez",
            "Pazarcık",
            "Türkoğlu",
            "Çağlayancerit",
            "Ekinözü",
            "Nurhak"
        ]
    },
    {
        "il": "Mardin",
        "plaka": 47,
        "ilceleri": [
            "Derik",
            "Kızıltepe",
            "Artuklu",
            "Merkez",
            "Mazıdağı",
            "Midyat",
            "Nusaybin",
            "Ömerli",
            "Savur",
            "Dargeçit",
            "Yeşilli"
        ]
    },
    {
        "il": "Muğla",
        "plaka": 48,
        "ilceleri": [
            "Bodrum",
            "Datça",
            "Fethiye",
            "Köyceğiz",
            "Marmaris",
            "Menteşe",
            "Milas",
            "Ula",
            "Yatağan",
            "Dalaman",
            "Seydikemer",
            "Ortaca",
            "Kavaklıdere"
        ]
    },
    {
        "il": "Muş",
        "plaka": 49,
        "ilceleri": [
            "Bulanık",
            "Malazgirt",
            "Merkez",
            "Varto",
            "Hasköy",
            "Korkut"
        ]
    },
    {
        "il": "Nevşehir",
        "plaka": 50,
        "ilceleri": [
            "Avanos",
            "Derinkuyu",
            "Gülşehir",
            "Hacıbektaş",
            "Kozaklı",
            "Merkez",
            "Ürgüp",
            "Acıgöl"
        ]
    },
    {
        "il": "Niğde",
        "plaka": 51,
        "ilceleri": [
            "Bor",
            "Çamardı",
            "Merkez",
            "Ulukışla",
            "Altunhisar",
            "Çiftlik"
        ]
    },
    {
        "il": "Ordu",
        "plaka": 52,
        "ilceleri": [
            "Akkuş",
            "Altınordu",
            "Aybastı",
            "Fatsa",
            "Gölköy",
            "Korgan",
            "Kumru",
            "Mesudiye",
            "Perşembe",
            "Ulubey",
            "Ünye",
            "Gülyalı",
            "Gürgentepe",
            "Çamaş",
            "Çatalpınar",
            "Çaybaşı",
            "İkizce",
            "Kabadüz",
            "Kabataş"
        ]
    },
    {
        "il": "Rize",
        "plaka": 53,
        "ilceleri": [
            "Ardeşen",
            "Çamlıhemşin",
            "Çayeli",
            "Fındıklı",
            "İkizdere",
            "Kalkandere",
            "Pazar",
            "Merkez",
            "Güneysu",
            "Derepazarı",
            "Hemşin",
            "İyidere"
        ]
    },
    {
        "il": "Sakarya",
        "plaka": 54,
        "ilceleri": [
            "Akyazı",
            "Geyve",
            "Hendek",
            "Karasu",
            "Kaynarca",
            "Sapanca",
            "Kocaali",
            "Pamukova",
            "Taraklı",
            "Ferizli",
            "Karapürçek",
            "Söğütlü",
            "Adapazarı",
            "Arifiye",
            "Erenler",
            "Serdivan"
        ]
    },
    {
        "il": "Samsun",
        "plaka": 55,
        "ilceleri": [
            "Alaçam",
            "Bafra",
            "Çarşamba",
            "Havza",
            "Kavak",
            "Ladik",
            "Terme",
            "Vezirköprü",
            "Asarcık",
            "Ondokuzmayıs",
            "Salıpazarı",
            "Tekkeköy",
            "Ayvacık",
            "Yakakent",
            "Atakum",
            "Canik",
            "İlkadım"
        ]
    },
    {
        "il": "Siirt",
        "plaka": 56,
        "ilceleri": [
            "Baykan",
            "Eruh",
            "Kurtalan",
            "Pervari",
            "Merkez",
            "Şirvan",
            "Tillo"
        ]
    },
    {
        "il": "Sinop",
        "plaka": 57,
        "ilceleri": [
            "Ayancık",
            "Boyabat",
            "Durağan",
            "Erfelek",
            "Gerze",
            "Merkez",
            "Türkeli",
            "Dikmen",
            "Saraydüzü"
        ]
    },
    {
        "il": "Sivas",
        "plaka": 58,
        "ilceleri": [
            "Divriği",
            "Gemerek",
            "Gürün",
            "Hafik",
            "İmranlı",
            "Kangal",
            "Koyulhisar",
            "Merkez",
            "Suşehri",
            "Şarkışla",
            "Yıldızeli",
            "Zara",
            "Akıncılar",
            "Altınyayla",
            "Doğanşar",
            "Gölova",
            "Ulaş"
        ]
    },
    {
        "il": "Tekirdağ",
        "plaka": 59,
        "ilceleri": [
            "Çerkezköy",
            "Çorlu",
            "Ergene",
            "Hayrabolu",
            "Malkara",
            "Muratlı",
            "Saray",
            "Süleymanpaşa",
            "Kapaklı",
            "Şarköy",
            "Marmaraereğlisi"
        ]
    },
    {
        "il": "Tokat",
        "plaka": 60,
        "ilceleri": [
            "Almus",
            "Artova",
            "Erbaa",
            "Niksar",
            "Reşadiye",
            "Merkez",
            "Turhal",
            "Zile",
            "Pazar",
            "Yeşilyurt",
            "Başçiftlik",
            "Sulusaray"
        ]
    },
    {
        "il": "Trabzon",
        "plaka": 61,
        "ilceleri": [
            "Akçaabat",
            "Araklı",
            "Arsin",
            "Çaykara",
            "Maçka",
            "Of",
            "Ortahisar",
            "Sürmene",
            "Tonya",
            "Vakfıkebir",
            "Yomra",
            "Beşikdüzü",
            "Şalpazarı",
            "Çarşıbaşı",
            "Dernekpazarı",
            "Düzköy",
            "Hayrat",
            "Köprübaşı"
        ]
    },
    {
        "il": "Tunceli",
        "plaka": 62,
        "ilceleri": [
            "Çemişgezek",
            "Hozat",
            "Mazgirt",
            "Nazımiye",
            "Ovacık",
            "Pertek",
            "Pülümür",
            "Merkez"
        ]
    },
    {
        "il": "Şanlıurfa",
        "plaka": 63,
        "ilceleri": [
            "Akçakale",
            "Birecik",
            "Bozova",
            "Ceylanpınar",
            "Eyyübiye",
            "Halfeti",
            "Haliliye",
            "Hilvan",
            "Karaköprü",
            "Siverek",
            "Suruç",
            "Viranşehir",
            "Harran"
        ]
    },
    {
        "il": "Uşak",
        "plaka": 64,
        "ilceleri": [
            "Banaz",
            "Eşme",
            "Karahallı",
            "Sivaslı",
            "Ulubey",
            "Merkez"
        ]
    },
    {
        "il": "Van",
        "plaka": 65,
        "ilceleri": [
            "Başkale",
            "Çatak",
            "Erciş",
            "Gevaş",
            "Gürpınar",
            "İpekyolu",
            "Muradiye",
            "Özalp",
            "Tuşba",
            "Bahçesaray",
            "Çaldıran",
            "Edremit",
            "Saray"
        ]
    },
    {
        "il": "Yozgat",
        "plaka": 66,
        "ilceleri": [
            "Akdağmadeni",
            "Boğazlıyan",
            "Çayıralan",
            "Çekerek",
            "Sarıkaya",
            "Sorgun",
            "Şefaatli",
            "Yerköy",
            "Merkez",
            "Aydıncık",
            "Çandır",
            "Kadışehri",
            "Saraykent",
            "Yenifakılı"
        ]
    },
    {
        "il": "Zonguldak",
        "plaka": 67,
        "ilceleri": [
            "Çaycuma",
            "Devrek",
            "Ereğli",
            "Merkez",
            "Alaplı",
            "Gökçebey"
        ]
    },
    {
        "il": "Aksaray1",
        "plaka": 68,
        "ilceleri": [
            "Ağaçören",
            "Eskil",
            "Gülağaç",
            "Güzelyurt",
            "Merkez",
            "Ortaköy",
            "Sarıyahşi",
			"Sultanhanı"
        ]
    },
    {
        "il": "Bayburt",
        "plaka": 69,
        "ilceleri": [
            "Merkez",
            "Aydıntepe",
            "Demirözü"
        ]
    },
    {
        "il": "Karaman",
        "plaka": 70,
        "ilceleri": [
            "Ermenek",
            "Merkez",
            "Ayrancı",
            "Kazımkarabekir",
            "Başyayla",
            "Sarıveliler"
        ]
    },
    {
        "il": "Kırıkkale",
        "plaka": 71,
        "ilceleri": [
            "Delice",
            "Keskin",
            "Merkez",
            "Sulakyurt",
            "Bahşili",
            "Balışeyh",
            "Çelebi",
            "Karakeçili",
            "Yahşihan"
        ]
    },
    {
        "il": "Batman",
        "plaka": 72,
        "ilceleri": [
            "Merkez",
            "Beşiri",
            "Gercüş",
            "Kozluk",
            "Sason",
            "Hasankeyf"
        ]
    },
    {
        "il": "Şırnak",
        "plaka": 73,
        "ilceleri": [
            "Beytüşşebap",
            "Cizre",
            "İdil",
            "Silopi",
            "Merkez",
            "Uludere",
            "Güçlükonak"
        ]
    },
    {
        "il": "Bartın",
        "plaka": 74,
        "ilceleri": [
            "Merkez",
            "Kurucaşile",
            "Ulus",
            "Amasra"
        ]
    },
    {
        "il": "Ardahan",
        "plaka": 75,
        "ilceleri": [
            "Merkez",
            "Çıldır",
            "Göle",
            "Hanak",
            "Posof",
            "Damal"
        ]
    },
    {
        "il": "Iğdır",
        "plaka": 76,
        "ilceleri": [
            "Aralık",
            "Merkez",
            "Tuzluca",
            "Karakoyunlu"
        ]
    },
    {
        "il": "Yalova",
        "plaka": 77,
        "ilceleri": [
            "Merkez",
            "Altınova",
            "Armutlu",
            "Çınarcık",
            "Çiftlikköy",
            "Termal"
        ]
    },
    {
        "il": "Karabük",
        "plaka": 78,
        "ilceleri": [
            "Eflani",
            "Eskipazar",
            "Merkez",
            "Ovacık",
            "Safranbolu",
            "Yenice"
        ]
    },
    {
        "il": "Kilis",
        "plaka": 79,
        "ilceleri": [
            "Merkez",
            "Elbeyli",
            "Musabeyli",
            "Polateli"
        ]
    },
    {
        "il": "Osmaniye",
        "plaka": 80,
        "ilceleri": [
            "Bahçe",
            "Kadirli",
            "Merkez",
            "Düziçi",
            "Hasanbeyli",
            "Sumbas",
            "Toprakkale"
        ]
    },
    {
        "il": "Düzce",
        "plaka": 81,
        "ilceleri": [
            "Akçakoca",
            "Merkez",
            "Yığılca",
            "Cumayeri",
            "Gölyaka",
            "Çilimli",
            "Gümüşova",
            "Kaynaşlı"
        ]
    }
];

// Türkçe karakterleri normalize eden fonksiyon
function normalizeText(text) {
    return text.toLocaleLowerCase("tr")
        .replace(/ı/g, "i")
        .replace(/ü/g, "u")
        .replace(/ö/g, "o")
        .replace(/ç/g, "c")
        .replace(/ş/g, "s")
        .replace(/ğ/g, "g");
}

// İl ve ilçe select kutularını doldur
function populateSelects() {
    var ilSelect = document.getElementById("Iller");
    var ilceSelect = document.getElementById("Ilceler");

    // İl seçeneklerini ekle
    data.forEach(item => {
        var option = document.createElement("option");
        option.value = item.il;
        option.textContent = item.il;
        ilSelect.appendChild(option);
    });

    // İl değiştiğinde ilçeleri güncelle
    ilSelect.addEventListener("change", function () {
        ilceSelect.innerHTML = '<option value="">Önce İl Seçin</option>';
        var selectedIl = this.value;
        var ilData = data.find(item => item.il === selectedIl);
        if (ilData) {
            ilData.ilceleri.forEach(ilce => {
                var option = document.createElement("option");
                option.value = ilce;
                option.textContent = ilce;
                ilceSelect.appendChild(option);
            });
        }
    });
}

// Textarea'daki 3. ve 4. satırları kontrol edip ili ve ilçeyi seçen fonksiyon
function detectAddressFromTextarea() {
    var textarea = document.getElementById("otodoldur");
    var ilSelect = document.getElementById("Iller");
    var ilceSelect = document.getElementById("Ilceler");

    if (!textarea.value) return;

    // Satırları al
    var lines = textarea.value.split("\n");

    // En az 4 satır varsa, 3. ve 4. satırları al
    var relevantLines = lines.length >= 4 ? lines.slice(2, 4).join(" ") : "";

    var normalizedText = normalizeText(relevantLines);

    // İl ve ilçe kontrolü
    var foundIl = data.find(item => new RegExp("\\b" + normalizeText(item.il) + "\\b", "i").test(normalizedText));

    if (foundIl) {
        ilSelect.value = foundIl.il;
        ilSelect.dispatchEvent(new Event("change")); // İl değişimini tetikle

        setTimeout(() => {
            var foundIlce = foundIl.ilceleri.find(ilce => new RegExp("\\b" + normalizeText(ilce) + "\\b", "i").test(normalizedText));
            if (foundIlce) {
                ilceSelect.value = foundIlce;
            }
        }, 100); // İlçe listesi yüklendikten sonra seçim yapmak için kısa bir gecikme ekledik
    }
}

// Sayfa yüklendiğinde select kutularını hazırla
document.addEventListener("DOMContentLoaded", function () {
    populateSelects();

    // "otodoldur" textarea'sı değiştiğinde ili ve ilçeyi belirle
    document.getElementById("otodoldur").addEventListener("input", detectAddressFromTextarea);
});

   </script> 






























                                    <!-- adı kopyalıyor -->
                                    <script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); document.getElementById('musteri_ismi').value = lines[0].trim(); }); </script>

                                    <!-- telefonu oto dolruyor -->
                                    <script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); if (lines.length > 1) { document.getElementById('musteri_telefonu').value = lines[1].trim(); } }); </script>

                                    <!-- adresi oto işliyor -->
                                    <script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); if (lines.length > 2) { document.getElementById('musteri_adresi').value = lines[2].trim(); } }); </script>

                                    <!-- hangi sayfadan geldiğini doldursunnn -->
                                    <script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); document.getElementById('siparissayfasi').value = lines[lines.length - 1].trim(); }); </script>

                                    <!-- ödeme şartı varsa rakamı doldurcak içerye -->

                                    <script>
                                            $(document).ready(function () {
                                                $('#otodoldur').on('input', function () {
                                                    var metin = $(this).val().trim();

                                                    if (metin === "") {
                                                        // Metin boşsa Ödeme Şartı alanını sıfırla
                                                        $('#odeme_sarti').val('0');
                                                        return;
                                                    }

                                                    // Metni satırlara ayır
                                                    var satirlar = metin.split('\n').map(function(satir) {
                                                        return satir.trim();
                                                    }).filter(function(satir) {
                                                        return satir.length > 0;
                                                    });

                                                    // En alt satırdan bir önceki satırı bul
                                                    if (satirlar.length < 2) {
                                                        // Eğer metinde birden az iki satır varsa, Ödeme Şartı alanını sıfırla
                                                        $('#odeme_sarti').val('0');
                                                        return;
                                                    }

                                                    var hedefSatir = satirlar[satirlar.length - 2]; // En alt satırdan bir önceki satır

                                                    // Hedef satırdaki sayıyı bulmak için regex kullan
                                                    var sayiMatch = hedefSatir.match(/\d+/); // İlk sayıyı bulur

                                                    if (sayiMatch) {
                                                        // Eğer sayı bulunduysa, Ödeme Şartı alanına yaz
                                                        $('#odeme_sarti').val(sayiMatch[0]);
                                                    } else {
                                                        // Eğer sayı bulunmazsa, Ödeme Şartı alanını sıfırla
                                                        $('#odeme_sarti').val('0');
                                                    }
                                                });
                                            });
                                        </script>
	
 <!--  ürünleri dağıtıyor -->
<script>
document.getElementById('otodoldur').addEventListener('input', function() {
    let lines = this.value.split('\n');
    let urunler = '';
    for (let i = 3; i < lines.length - 2; i++) { // İlk 3 ve son 2 satırı dahil etmemek için 3'ten başlayıp length - 2'ye kadar döner
        urunler += lines[i] + '\n';
    }
    document.getElementById('urunler').value = urunler.trim();
});
</script>


                                        <!-- il ilçe ve ürünleri dağıtıyor -->
                                        <script>

document.getElementById('otodoldur').addEventListener('input', function() {
    var lines = this.value.split('\n');
    var il, ilce;


        // Ürünler alanını doldur
 // Ürünler alanını doldur
        var urunler = '';
        for (var i = 3; i < lines.length - 2; i++) { // En alt iki satırı dahil etmemek için -2 ekledik
            urunler += lines[i] + '\n';
        }
        document.getElementById('urunler').value = urunler.trim();
    }
});


                                        </script>
    

    <script>
        $(document).ready(function () {
            $('#datatable').DataTable({
                scrollX: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Turkish.json'
                },
                responsive: true,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                dom: 'Bfrtip', // Export buttons
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });

        });


    </script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

    </script>

    <script>
        document.getElementById('pageSearch').addEventListener('input', function () {
            let filter = this.value.toUpperCase();
            let rows = document.querySelectorAll('#datatable tbody tr');

            rows.forEach(row => {
                let text = row.textContent || row.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    row.style.display = ''; // Eşleşme varsa satırı göster
                } else {
                    row.style.display = 'none'; // Eşleşme yoksa satırı gizle
                }
            });
        });
    </script>

    
<!-- adı kopyalıyor -->
<script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); document.getElementById('musteri_ismi').value = lines[0].trim(); }); </script>

<!-- telefonu oto dolruyor -->
<script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); if (lines.length > 1) { document.getElementById('musteri_telefonu').value = lines[1].trim(); } }); </script>

<!-- adresi oto işliyor -->
<script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); if (lines.length > 2) { document.getElementById('musteri_adresi').value = lines[2].trim(); } }); </script>

<!-- hangi sayfadan geldiğini doldursunnn -->
<script> document.getElementById('otodoldur').addEventListener('input', function() { let lines = this.value.split('\n'); document.getElementById('siparissayfasi').value = lines[lines.length - 1].trim(); }); </script>

<!-- ödeme şartı varsa rakamı doldurcak içerye -->

<script>
       $(document).ready(function () {
           $('#otodoldur').on('input', function () {
               var metin = $(this).val().trim();

               if (metin === "") {
                   // Metin boşsa Ödeme Şartı alanını sıfırla
                   $('#odeme_sarti').val('0');
                   return;
               }

               // Metni satırlara ayır
               var satirlar = metin.split('\n').map(function(satir) {
                   return satir.trim();
               }).filter(function(satir) {
                   return satir.length > 0;
               });

               // En alt satırdan bir önceki satırı bul
               if (satirlar.length < 2) {
                   // Eğer metinde birden az iki satır varsa, Ödeme Şartı alanını sıfırla
                   $('#odeme_sarti').val('0');
                   return;
               }

               var hedefSatir = satirlar[satirlar.length - 2]; // En alt satırdan bir önceki satır

               // Hedef satırdaki sayıyı bulmak için regex kullan
               var sayiMatch = hedefSatir.match(/\d+/); // İlk sayıyı bulur

               if (sayiMatch) {
                   // Eğer sayı bulunduysa, Ödeme Şartı alanına yaz
                   $('#odeme_sarti').val(sayiMatch[0]);
               } else {
                   // Eğer sayı bulunmazsa, Ödeme Şartı alanını sıfırla
                   $('#odeme_sarti').val('0');
               }
           });
       });
   </script>
   



   <!-- il ilçe ve ürünleri dağıtıyor
<script>
 document.getElementById('otodoldur').addEventListener('input', function() {
   var lines = this.value.split('\n');
   var il, ilce;

   // 3. veya 4. satırda il ve ilçe kontrolü
   for (var i = 2; i <= 3; i++) {
     data.forEach(function(item) {
       if (lines[i] && lines[i].toLowerCase().includes(item.il.toLowerCase())) {
         il = item.il;
         item.ilceleri.forEach(function(subItem) {
           if (lines[i].toLowerCase().includes(subItem.toLowerCase())) {
             ilce = subItem;
           }
         });
       }
     });
     if (il && ilce) break;
   }

   // İl ve ilçe select'lerini doldur
   if (il && ilce) {
     document.getElementById('Iller').value = il;
     document.getElementById('Ilceler').innerHTML = ''; // Temizle
     data.find(item => item.il === il).ilceleri.forEach(function(subItem) {
       var option = document.createElement('option');
       option.value = subItem;
       option.text = subItem;
       document.getElementById('Ilceler').appendChild(option);
     });
     document.getElementById('Ilceler').value = ilce;

     // Ürünler textarea'sını doldur
     var urunler = '';
     for (var j = i + 1; j < lines.length - 2; j++) {
       urunler += lines[j] + '\n';
     }
     document.getElementById('urunler').value = urunler.trim();
   }
 });
</script>

 -->
	 <script>
        // Excel İndir
        document.getElementById("downloadExcel").addEventListener("click", function () {
            var table = document.getElementById("datatable");
            var workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            XLSX.writeFile(workbook, "TabloVerileri.xlsx");
        });

       
    </script>
	
 <script>
        document.getElementById("downloadPDF").addEventListener("click", function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: "landscape",
                unit: "pt",
                format: "a4",
            });

            // Roboto fontunu PDF'ye ekleme
            doc.addFileToVFS("Roboto-Bold.ttf", RobotoRegular); // Dönüştürülmüş fontu ekle
            doc.addFont("Roboto-Bold.ttf", "Roboto", "normal");
            doc.setFont("Roboto");

            const table = document.getElementById("datatable");
            const headers = [...table.querySelectorAll("thead tr th")].map(th => th.innerText);
            const rows = [...table.querySelectorAll("tbody tr")].map(tr =>
                [...tr.querySelectorAll("td")].map(td => td.innerText)
            );

            doc.autoTable({
                head: [headers],
                body: rows,
                theme: "grid",
                styles: {
                    font: "Roboto",
                    fontSize: 10,
                },
                headStyles: {
                    fillColor: [41, 128, 185],
                    textColor: [255, 255, 255],
                    halign: "center",
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240],
                },
                margin: { top: 40 },
                didDrawPage: function (data) {
                    doc.setFontSize(18);
                    doc.text("Tablo Verileri", doc.internal.pageSize.getWidth() / 2, 30, { align: "center" });
                },
            });

            doc.save("TabloVerileri.pdf");
        });
    </script>
	
	

    <style>
    
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 255, 0, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 255, 0, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 255, 0, 0);
            }
        }

        .animate-pulse {
            animation: pulse 1.5s infinite;
            display: inline-block;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            color: #fff;
            background-color: #28a745;
        }
    </style>
	
	<!-- Vendor -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="assets/libs/jquery-ui/jquery-ui.min.js"></script>
        
        <!-- App js-->
        <script src="assets/js/app.js"></script>
        <script  src="./script.js"></script>


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

<script>
function resmilestir(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlem geri alınamaz!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Resmileştir!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('resmilestir_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire(
                        'Başarılı!',
                        'Resmileştirme işlemi tamamlandı.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}
</script>
  
  
  
  
  
  
  
  
  
  <script>
        $(document).ready(function() {
            $("#guncelleBtn").click(function() {
                $.ajax({
                    type: "POST",
                    url: "siparisresmilestirmeislemi.php",
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            let mesaj = `<h3>${response.message}</h3><p>Paraşüt otomatik olarak kısa bir süre sonra işleme alacaktır.</p><ul>`;
                            response.musteriler.forEach(function(musteri) {
                                mesaj += `<li>${musteri}</li>`;
                            });
                            mesaj += "</ul>";
                            $("#sonuc").html(mesaj);
                        } else {
                            $("#sonuc").html(`<p style="color:red;">${response.message}</p>`);
                        }
                    },
                    error: function() {
                        $("#sonuc").html("<p style='color:red;'>Bir hata oluştu. Lütfen tekrar deneyin.</p>");
                    }
                });
            });
        });
    </script>

  
  
  
  
  
  
  
  
  
  
  
  
<!-- JavaScript ekle -->
<script>
function topluResmilestir() {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Uygun olan tüm siparişler resmileştirilecek!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Resmileştir!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('toplu_resmilestir.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire(
                        'Başarılı!',
                        data.message,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}
</script>

</body>

<style>
    .table-responsive {
        overflow-x: auto; /* Yatay kaydırmayı etkinleştir */
    }

    .table-hover tbody tr:hover {
        background-color: #f9f9f9; /* Satır üzerine gelindiğinde arka plan rengi değişir */
    }

    .table thead th {
        text-align: center; /* Başlıklar ortalanır */
        white-space: nowrap; /* Başlık taşmasında yan yana kalır */
    }

    .btn-primary, .btn-danger {
        margin: 2px; /* Butonlara daha düzenli görünüm için margin */
    }

    #pageSearch {
        margin-bottom: 15px;
        max-width: 400px;
    }

</style>

</html>