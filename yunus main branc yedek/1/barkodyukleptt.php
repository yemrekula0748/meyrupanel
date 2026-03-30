<!DOCTYPE html>
<html lang="tr">
    <head>

        <meta charset="utf-8" />
        <title>Starter | Silva - Responsive Admin Dashboard Template</title>
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
                                <h4 class="fs-18 fw-semibold m-0">PTT Barkod Yükle</h4>
                            </div>
            
                          
                        </div>
                        


                         
                        
                    </div> <!-- container-fluid -->
					
					
					
	<form action="ptt_barkod_yukle_sonuc.php" method="POST" enctype="multipart/form-data">
        <label for="excel_file">Excel Dosyasını Seçin:</label>
        <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx, .xls" required>
        <button class="btn btn-primary" type="submit">Yükle</button>
    </form>
					
					

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
        
    </body>
</html>