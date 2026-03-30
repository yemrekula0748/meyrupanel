<!DOCTYPE html>
<html lang="tr">
    <head>

        <meta charset="utf-8" />
        <title>Satış Panel | Girilen Siparişler</title>
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
		
		<!-- Datatables css -->
        <link href="assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

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
                                <h4 class="fs-18 fw-semibold m-0">Girilen Siparişler</h4>
                            </div>
            
                           
                        </div>
                        


                         
                        
                    </div> <!-- container-fluid -->
					
					
					
					<?php
// Veritabanı bağlantısı dosyasını dahil ediyoruz.
include('DB.php');

// DB sınıfını kullanarak veritabanı bağlantısını başlatıyoruz.
$db = new DB();

// Siparişler tablosundan verileri çekiyoruz.
$sql = "SELECT * FROM siparisler ORDER BY id DESC";
$result = $db->query($sql);

// Eğer veri varsa, tabloyu dinamik olarak oluşturuyoruz.
if ($result->num_rows > 0) {
    echo '<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Siparişler Tablosu</h5>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>NO</th>
									<th>Barkod</th>
                                    <th>siparis_tarihi</th>
                                    <th>Müşteri Adı</th>
                                    <th>Telefon</th>
								
                                    <th>Adres</th>
                                    <th>Ödeme Şartı</th>
									 <th>Ürünler</th>
								
									   <th>NOT</th>
									    <th>Kargo</th>
										 <th>Fatura</th>
										  <th>Barkod</th>
                                </tr>
                            </thead>
                            <tbody>';
    // Verileri tabloya ekliyoruz.
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';  // Sipariş Adı
		echo '<td>' . $row['kargo_barkodu'] . '</td>';
       $siparis_tarihi = date('d-m-Y', strtotime($row['siparis_tarihi'])); echo '<td>' . $siparis_tarihi . '</td>';
        echo '<td>' . $row['musteri_ismi'] . '</td>';  // Ofis
        echo '<td>' . $row['musteri_telefonu'] . '</td>';
	
        echo '<td>' . $row['musteri_adresi'] . '</td>';  // Başlangıç Tarihi
        echo '<td>' . $row['odeme_sarti'] . '</td>';  // Maaş
		echo '<td>' . $row['urunler'] . '</td>';  // Maaş

		echo '<td>' . $row['yonetici_notu'] . '</td>';  // Maaş
		echo '<td>' . $row['kargo'] . '</td>';  // Maaş
		echo '<td>' . $row['faturalandirma_durumu'] . '</td>';  // Maaş
			echo '<td>' . $row['barkod_basilma_durumu'] . '</td>';  // Maaş
        echo '</tr>';
    }

    echo '</tbody>
        </table>
    </div>
  </div>
</div>';
} else {
    echo "Veri bulunamadı.";
}

?>

					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					

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
		
		
		 <!-- Datatables js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

        <!-- dataTables.bootstrap5 -->
        <script src="assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>

        <!-- buttons.colVis -->
        <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>

        <!-- buttons.bootstrap5 -->
        <script src="assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>

        <!-- dataTables.keyTable -->
        <script src="assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="assets/libs/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js"></script>

        <!-- dataTable.responsive -->
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

        <!-- dataTables.select -->
        <script src="assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
        <script src="assets/libs/datatables.net-select-bs5/js/select.bootstrap5.min.js"></script>

        <!-- Datatable Demo App Js -->
        <script src="assets/js/pages/datatable.init.js"></script>
		
		

        <!-- App js-->
        <script src="assets/js/app.js"></script>
        
    </body>
</html>