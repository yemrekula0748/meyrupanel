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
        <title>Satış Panel | Sipariş Ekranı</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc."/>
        <meta name="author" content="Zoyothemes"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App css -->
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

        <!-- Icons -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    </head>

    <!-- body start -->
    <body data-menu-color="light" data-sidebar="default"

        <!-- Begin page -->
        <div id="app-layout">

<?php
include 'tema/menu.php';

?>
            

            <!-- ============================================================== -->
            <!-- BOŞ SAYFA BAŞLANGIÇ -->
            <!-- ============================================================== -->
         
            <div class="content-page">
    <div class="content">
        <!-- Start Content -->
        <div class="container-fluid">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Sipariş Girişi</h4>
                </div>
            </div>

            <form action="siparis_gir_sonuc.php" method="post">
                <div class="row g-3">
                    <!-- Sipariş Tarihi -->
                    <div class="col-lg-6">
					
					
					
					
					
					
					
					
                        <div class="form-group">
    <label for="siparis_tarihi">Sipariş Tarihi</label>
    <input type="date" id="siparis_tarihi" name="siparis_tarihi" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
</div>

                    </div>

<div class="col-lg-6">
    <div class="form-group">
        <label for="otodoldur">OTOMATİK DOLDUR</label>
        <textarea id="otodoldur" name="otodoldur" class="form-control" placeholder="Örn: Ahmet Yılmaz" ></textarea>
    </div>
</div>



                    <!-- Müşteri İsmi -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="musteri_ismi">Müşteri İsmi</label>
                            <input type="text" id="musteri_ismi" name="musteri_ismi" class="form-control" placeholder="Örn: Ahmet Yılmaz" required>
                        </div>
                    </div>

                    <!-- Müşteri Telefonu -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="musteri_telefonu">Müşteri Telefonu</label>
                            <input type="text" id="musteri_telefonu" name="musteri_telefonu" class="form-control" placeholder="Örn: 05xx xxx xx xx" required>
                        </div>
                    </div>

                    <!-- Müşteri Adresi -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="musteri_adresi">Müşteri Adresi</label>
                            <textarea id="musteri_adresi" name="musteri_adresi" class="form-control" rows="3" placeholder="Örn: Atatürk Cad. No:10, İstanbul" required></textarea>
                        </div>
                    </div>

                    <!-- İl -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="Iller">Müşteri İl</label>
                            <select id="Iller" name="Iller" class="form-control" required>
                                <option value="">İl Seçin</option>
                            </select>
                        </div>
                    </div>

                    <!-- İlçe -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="Ilceler">Müşteri İlçe</label>
                            <select id="Ilceler" name="Ilceler" class="form-control" required>
                                <option value="">Önce İl Seçin</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ödeme Şartı -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="odeme_sarti">Ödeme Şartı</label>
                            <input type="text" id="odeme_sarti" name="odeme_sarti"  value="0" class="form-control" placeholder="Örn: 150" required>
                        </div>
                    </div>

                    <!-- Ürünler -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="urunler">Ürünler</label>
                            <textarea id="urunler" name="urunler" class="form-control" rows="3" placeholder="Sipariş edilen ürünleri yazınız" required></textarea>
                        </div>
                    </div>

                    <!-- Desi -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="desi">Desi</label>
                            <input type="number" id="desi" name="desi" class="form-control" value="1" readonly>
                        </div>
                    </div>

                    <!-- Ağırlık -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="agirlik">Ağırlık</label>
                            <input type="number" id="agirlik" name="agirlik" class="form-control" value="430" readonly>
                        </div>
                    </div>

                    <!-- Kargo -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="kargo">Kargo</label>
                            <select id="kargo" name="kargo" class="form-control" required>
                                <option value="Ödeme Şartlı">Ödeme Şartlı</option>
                                <option value="Bedelsiz">Bedelsiz</option>
                                <option value="Ücreti Alıcıdan">Ücreti Alıcıdan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Yönetici Notu -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="yoneticinotu">Yönetici Notu</label>
                            <input type="text" id="yoneticinotu" name="yoneticinotu" class="form-control" placeholder="Yönetici notu ekleyebilirsiniz">
                        </div>
                    </div>
					
					<div class="col-lg-6">
                        <div class="form-group">
                            <label for="siparissayfasi">Sipariş Sayfası</label>
                            <input type="text" id="siparissayfasi" name="siparissayfasi" class="form-control" placeholder="Örnek Semrebutik DM">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-lg-12 text-center">
                        <input type="hidden" name="user_name" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                        <input type="hidden" name="faturalandirma_durumu" value="Faturalandırılmadı">
                        <input type="hidden" name="barkod_basilma_durumu" value="Basılmamış">
                        <button type="submit" class="btn btn-primary btn-lg">Sipariş Ekle</button>
                    </div>

                    <!-- Gizli Alanlar -->

					<input type="hidden" name="user_name" value="<?php echo $userName; ?>">
                </div>
            </form>
        </div>
    </div>
</div>



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


 <!-- eğer ödeme şartı 0 ise otomatik bedelsiz seçiyor, eğer 0dan büyük sayı varsa ödeme şartlı seçiyor. -->












<?php
include 'tema/footer.php';

?>

         </div>
        <!-- ============================================================== -->
        <!-- BOŞ SAYFA BİTİŞ -->
        <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->

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
        // Başarı ve hata mesajı kontrolü
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
                    }).then(() => {
                        if ('{$status}' === 'success') {
                            window.location.href = 'girilen_siparisler.php';
                        }
                    });
                });
            </script>";
        }
        ?>

    </body>
</html>
		
