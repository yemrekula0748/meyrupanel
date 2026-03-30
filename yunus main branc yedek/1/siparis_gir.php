<!DOCTYPE html>
<html lang="tr">
    <head>

        <meta charset="utf-8" />
        <title>Satış Panel | Sipariş Oluşturma Ekranı</title>
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

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Sipariş Girişi</h4>
                            </div>
            
                         
                        </div>
                        









									 <form action="siparis_gir_sonuc.php" method="post">
        <div class="form-group">
            <label for="siparis_tarihi">Sipariş Tarihi:</label>
            <input type="date" id="siparis_tarihi" name="siparis_tarihi" class="form-control" required>
        </div>
		<script> document.addEventListener('DOMContentLoaded', (event) => { const today = new Date(); const year = today.getFullYear(); const month = String(today.getMonth() + 1).padStart(2, '0'); const day = String(today.getDate()).padStart(2, '0'); const formattedDate = `${year}-${month}-${day}`; document.getElementById('siparis_tarihi').value = formattedDate; }); </script>
		
<br>
        <div class="form-group">
            <label for="musteri_ismi">Müşteri İsmi:</label>
            <input type="text" id="musteri_ismi" name="musteri_ismi" class="form-control" required>
        </div>
<br>
        <div class="form-group">
            <label for="musteri_telefonu">Müşteri Telefonu:</label>
            <input type="text" id="musteri_telefonu" name="musteri_telefonu" class="form-control" required>
        </div>
<br>
        <div class="form-group">
            <label for="musteri_adresi">Müşteri Adresi:</label>
            <textarea id="musteri_adresi" name="musteri_adresi" class="form-control" rows="3" required></textarea>
        </div>
<br>
        <div class="form-group">
            <label for="il">Müşteri İl:</label>
            <select class="form-control" name ="Iller" id="Iller" required>
			 <option value="">İl Seçin</option>
      
  </select>
        </div>
<br>
        <div class="form-group">
            <label for="ilce">Müşteri İlçe:</label>
            <select id="Ilceler" name="Ilceler" class="form-control" required>
                <option value="">Önce İl Seçin</option>
            </select>
        </div>
<br>
        <div class="form-group">
            <label for="odeme_sarti">Ödeme Şartı:</label>
            <input type="text" id="odeme_sarti" name="odeme_sarti" class="form-control" required>
        </div>
<br>
        <div class="form-group">
            <label for="urunler">Ürünler:</label>
            <textarea id="urunler" name="urunler" class="form-control" rows="5" required></textarea>
        </div>
<br>
      <div class="form-group">
    <label for="desi">Desi:</label>
    <input type="number" id="desi" name="desi" class="form-control"  value="1" readonly>
</div>

<br>
        <div class="form-group">
            <label for="agirlik">Ağırlık:</label>
            <input type="number" id="agirlik" name="agirlik" value="430" class="form-control" readonly>
        </div>
<br>





       <div class="form-group">
    <label for="kargo">Kargo:</label>
    <select id="kargo" name="kargo" class="form-control" required>
        <option value="Ödeme Şartlı">Ödeme Şartlı</option>
        <option value="Bedelsiz">Bedelsiz</option>
        <option value="Ücreti Alıcıdan">Ücreti Alıcıdan</option>
    </select>
</div>

<br>
<div class="form-group">
            <label yoneticinotu="kargo">Yönetici Notu:</label>
            <input type="text" id="yoneticinotu" name="yoneticinotu" class="form-control">
        </div>




        <!-- Gizli Alanlar -->
        <input type="hidden" name="faturalandirma_durumu" value="Faturalandırılmadı">
        <input type="hidden" name="barkod_basilma_durumu" value="Basılmamış">
<br>
        <button type="submit" class="btn btn-primary">Sipariş Ekle</button>
    </form>
									
									






                         
                        
                    </div> <!-- container-fluid -->

                </div> <!-- content -->

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

        <!-- App js-->
        <script src="assets/js/app.js"></script>
		<script  src="./script.js"></script>
        
    </body>
</html>