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
							<button class="btn btn-warning me-2" onclick="window.location.href='export_excel_gider_hepsijet.php'">HJ YunusEmre</button>
							<button class="btn btn-warning me-2" onclick="window.location.href='export_excel_gider_yunusemre.php'">PTT YunusEmre</button>
							<button class="btn btn-warning me-2" onclick="window.location.href='export_excel_gider_sevim.php'">PTT SevimAydın</button>
                                <h4 class="fs-18 fw-semibold m-0">Girilen Siparişler</h4>
                            </div>
                        </div>
                        

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-start align-items-center mb-3">
                            <!-- Sol tarafta tüm butonlar -->
                           
						   
						   
						   
						   

                        </div>


                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">İptal Edilen Siparişler Tablosu</h5>
                        </div>



                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center align-middle">
                                <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>PTT</th>
                                            <th>BARKOD NO</th>
                                            <th>FATURA NO</th>
                                            <th>ÜRÜNLER</th>
                                            <th>MÜŞTERİ</th>
                                            <th>KARGO DAHIL</th>
                                            <th>EKLEME TARİHİ</th>
                                           
                                            <th>TUTAR</th>
                                      


                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once 'DB.php';
                                        $db = new DB();
                                        $result = $db->query("SELECT * FROM siparisler WHERE kargo = 'Ödeme Şartlı' AND iptalmi = 1  ORDER BY id DESC");

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
                                                'Yunus Emre - PTT' => 'Yunus Emre',
                                                'Sevim Aydın - PTT' => 'Sevim Aydın',
												'Yunus Emre - Hepsijet' => 'HepsiJET',
                                                '' => 'İkas',
                                                default => $row['null']
                                            };

                                            // Resmileşme durumu
                                            $resmilesmeDurumu = $row['faturalandirma_durumu'] === "Faturalandırılmadı" ? '✖︎' : '✓';

                                            // Barkod durumu
                                            $barkodDurumu = $row['barkod_basilma_durumu'] === "Basılmamış" ? '✖︎' : '✓';

                                            // Ödeme şartı
                                            $odemeSarti = $row['odeme_sarti'] . " TL";
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['id']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($odemeDurumu) ?>
                                                    <?php if($row['hangisayfa'] == 'iKas'): ?>
                                                        <br>
                                                        <span class="badge rounded-pill bg-danger">
                                                            <?= htmlspecialchars($row['hangisayfa']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
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



                                              <td>
                                                <?php if (!empty($row['parasut_fatura_numarasi'])): ?>
                                                    <?= htmlspecialchars($row['parasut_fatura_numarasi']) ?>
                                                <?php elseif ($row['kargo'] === 'Bedelsiz'): ?>
                                                    <span class="badge rounded-pill text-bg-warning">Bedelsiz</span>
                                                <?php else: ?>
                                                    <span class="badge rounded-pill text-bg-info">Oluşturuluyor</span>
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
                                                <td><?= date('d-m-Y', strtotime($row['siparis_tarihi'])) ?></td>
                                             
                                                <td><?= htmlspecialchars($odemeSarti) ?></td>
                                                
                                            

                                            </tr><!-- Düzenle Modal -->
                                            

                                            <!-- Sil Modal -->
                                         
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
                    <div class="col-md-6">
																<button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">İptal</button>
                                                                    <button type="submit" class="btn btn-info">Sipariş Ekle</button>
																</div>

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
                            <textarea id="otodoldur" name="otodoldur" class="form-control form-control-sm" rows="4"></textarea>
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
                            <textarea id="urunler" name="urunler" class="form-control form-control-sm" rows="2" required></textarea>
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
                        <div class="col-9">
                            <input type="date" id="siparis_tarihi" name="siparis_tarihi" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>





                                        <style>
                                        .form-container {
                                            max-width: 600px; /* Genişliği sınırlayın */
                                            margin: 0 auto; /* Ortalamak için */
                                        }
                                        </style>





                                    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>



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
	



                                        <!-- il ilçe ve ürünleri dağıtıyor -->
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
   



   <!-- il ilçe ve ürünleri dağıtıyor -->
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