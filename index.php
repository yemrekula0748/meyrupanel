


<!DOCTYPE html>
<html lang="tr">
    <head>

        <meta charset="utf-8" />
        <title>Anasayfa CRM | Satış Panel</title>
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

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    </head>

    <!-- body start -->
    <body data-menu-color="light" data-sidebar="default"

        <!-- Begin page -->
        <div id="app-layout">


<?php
include 'tema/menu.php';
include 'fonksiyon.php';
$unprocessedOrderCount = getUnprocessedOrderCount($db);

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
                                <h4 class="fs-18 fw-semibold m-0">İstatistik</h4>
                            </div>
                        </div>

                        <!-- start row -->
                        <div class="row">
                            <div class="col-md-12">
        

                            <div class="col-md-6">
                                <div class="row g-3">
                                    
                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <div class="widget-first">
        
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-pill me-2">
                                                            <div class="bg-primary rounded-circle widget-size text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#ffffff" d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4"/></svg>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-dark fs-15">Girilen Sipariş</p>
                                                    </div>
       
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="mb-0 fs-22 text-black me-3"><?php echo getAllUnprocessedOrderCount($db); ?></h3>
                                                        <div class="text-center">
                                                            
                                                            <p class="text-dark fs-13 mb-0">Paneldeki Boşaltılmayan Siparişler</p>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <div class="widget-first">
        
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="p-2 border border-secondary border-opacity-10 bg-secondary-subtle rounded-pill me-2">
                                                            <div class="bg-secondary rounded-circle widget-size text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 512"><path fill="#ffffff" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m32 32h-64c-17.6 0-33.5 7.1-45.1 18.6c40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64m-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32S208 82.1 208 144s50.1 112 112 112m76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2m-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4"/></svg>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-dark fs-15">Bu Ayki Sipariş</p>
                                                    </div>
        
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="mb-0 fs-22 text-black me-3"><?php echo getBuAykiSiparisCount(); ?></h3>
                                                        <div class="text-center">
                                                           
                                                            <p class="text-dark fs-13 mb-0">Aylık Siparişi Gösterir</p>
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <div class="widget-first">
        
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="p-2 border border-danger border-opacity-10 bg-danger-subtle rounded-pill me-2">
                                                            <div class="bg-danger rounded-circle widget-size text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#ffffff" d="M7 15h2c0 1.08 1.37 2 3 2s3-.92 3-2c0-1.1-1.04-1.5-3.24-2.03C9.64 12.44 7 11.78 7 9c0-1.79 1.47-3.31 3.5-3.82V3h3v2.18C15.53 5.69 17 7.21 17 9h-2c0-1.08-1.37-2-3-2s-3 .92-3 2c0 1.1 1.04 1.5 3.24 2.03C14.36 11.56 17 12.22 17 15c0 1.79-1.47 3.31-3.5 3.82V21h-3v-2.18C8.47 18.31 7 16.79 7 15"/></svg>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-dark fs-15">Resmileşen</p>   
                                                    </div>
        
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="mb-0 fs-22 text-black me-3"><?php echo getOfficialOrderCount($db); ?> Adet</h3>
                                                        <div class="text-center">
                                                           
                                                            <p class="text-dark fs-13 mb-0">Resmileşme Emri Verilen Sipariş Sayısı</p>
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <div class="widget-first">
        
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="p-2 border border-warning border-opacity-10 bg-warning-subtle rounded-pill me-2">
                                                            <div class="bg-warning rounded-circle widget-size text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#ffffff" d="M5.574 4.691c-.833.692-1.052 1.862-1.491 4.203l-.75 4c-.617 3.292-.926 4.938-.026 6.022C4.207 20 5.88 20 9.23 20h5.54c3.35 0 5.025 0 5.924-1.084c.9-1.084.591-2.73-.026-6.022l-.75-4c-.439-2.34-.658-3.511-1.491-4.203C17.593 4 16.403 4 14.02 4H9.98c-2.382 0-3.572 0-4.406.691" opacity="0.5"/><path fill="#988D4D" d="M12 9.25a2.251 2.251 0 0 1-2.122-1.5a.75.75 0 1 0-1.414.5a3.751 3.751 0 0 0 7.073 0a.75.75 0 1 0-1.414-.5A2.251 2.251 0 0 1 12 9.25"/></svg>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-dark fs-15">Resmileşmeyen</p>
                                                    </div>
                                                    
        
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="mb-0 fs-22 text-black me-3"><?php echo getUnofficialOrderCount($db); ?></h3>
        
                                                        <div class="text-muted">
                                                           
                                                            <p class="text-dark fs-13 mb-0">Resmileşmeyen Sipariş Sayısı</p>
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <div class="widget-first">
        
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="p-2 border border-success border-opacity-10 bg-success-subtle rounded-pill me-2">
                                                            <div class="bg-success rounded-circle widget-size text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M5 19L19 5"/><circle cx="7" cy="7" r="3"/><circle cx="17" cy="17" r="3"/></g></svg>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-dark fs-15">İkas Siparişleri</p>
                                                    </div>
                                                    
        
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="mb-0 fs-22 text-black me-3"><?php echo getUnprocessedOrderCount($db); ?> Adet</h3>
        
                                                        <div class="text-muted">
                                                           
                                                            <p class="text-dark fs-13 mb-0">iKas Sipariş Sayısı</p>
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="widget-first">
        
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="p-2 border border-dark border-opacity-10 bg-dark-subtle rounded-pill me-2">
                                                            <div class="bg-dark rounded-circle widget-size text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9H6.659c-1.006 0-1.51 0-1.634-.309c-.125-.308.23-.672.941-1.398L8.211 5M5 15h12.341c1.006 0 1.51 0 1.634.309c.125.308-.23.672-.941 1.398L15.789 19" color="#ffffff"/></svg>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-dark fs-15">Kalan Ptt Barkod Sayısı</p>
                                                    </div>
                                                    
        
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="mb-0 fs-22 text-black me-3"><?php echo bekleyenKargoSayisi(); ?> Adet</h3>
        
                                                        <div class="text-muted">
                                                            
                                                            <p class="text-dark fs-13 mb-0">Toplan Kalan KP Kodları</p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mb-4">
                                        <button id="clearOrders" class="btn btn-danger">Siparişleri Boşalt ve Yedekle</button>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- end start -->

                        
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

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
		 <!-- Apexcharts JS -->
        <!-- for basic area chart -->
  

        <!-- Widgets Init Js -->
        <script src="assets/js/pages/crm-dashboard.init.js"></script>
        <!-- Apexcharts JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="assets/js/pages/dashboard.init.js"></script>

        <script>
            document.getElementById('clearOrders').addEventListener('click', function () {
                Swal.fire({
                    title: 'Şifre Girin',
                    input: 'password',
                    inputLabel: 'Lütfen işlemi tamamlamak için şifrenizi girin',
                    inputPlaceholder: 'Şifre',
                    showCancelButton: true,
                    confirmButtonText: 'Devam Et',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value === "130519") {
                            // Şifre doğruysa işlemi başlat
                            $.post('bosalt_yedekle.php', function (response) {
                                Swal.fire({
                                    title: response.status === "success" ? "Başarılı" : "Hata",
                                    text: response.message,
                                    icon: response.status === "success" ? "success" : "error"
                                }).then(() => {
                                    if (response.status === "success") {
                                        location.reload(); // Sayfayı yeniden yükle
                                    }
                                });
                            }, "json");
                        } else {
                            Swal.fire({
                                title: 'Hatalı Şifre',
                                text: 'Girdiğiniz şifre yanlış. Lütfen tekrar deneyin.',
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        </script>

        
 


        
    </body>
</html>