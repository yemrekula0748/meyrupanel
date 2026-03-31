<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Giriş kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Kullanıcı giriş yapmadıysa login sayfasına yönlendir
    exit;
}

require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/autoload.php';
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/tecnickcom/tcpdf/tcpdf.php';
require_once 'DB.php';

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Satış Panel | BARKOD YAZDIRMA</title>
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
                                <h4 class="fs-18 fw-semibold m-0">Barkod Alınmayan Siparişler</h4>
                            </div>
                        </div>
                      

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-start align-items-center mb-3">
							<button class="btn btn-warning me-2" onclick="window.open('odeme_sartli_toplu.php', '_blank')">ÖDEME ŞARTLI Barkod Çıktısı Al - MH</button>
                            <button class="btn btn-primary" onclick="printBarcodes()">UA-B Barkod Çıktısı Al</button>
                        </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Siparişler Tablosu</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center align-middle">
                                <thead>
                                        <tr>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once 'DB.php';
                                        $db = new DB();
                                        $result = $db->query("SELECT * FROM siparisler 
                                        WHERE islem = 0 
                                        AND barkod_basilma_durumu = 'Basılmamış' 
                                        AND kargo_barkodu IS NOT NULL 
                                        AND (iptalmi = 0 OR iptalmi IS NULL) 
                                        ORDER BY id DESC");
                                    

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
                                                                'MeyruKids' => 'Yunus Emre',
																'Yunus Emre - Hepsijet' => 'YunusEmreHJ',
                                                                'Sevim Aydın - PTT' => 'Sevim Aydın',
                                                                '' => 'İkas',
                                                                default => $row['hangikargo']
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
                                                    <?php if (!empty($row['kargolink'])): ?>
                                                        <a href="<?= htmlspecialchars($row['kargolink']) ?>" target="_blank">
                                                            <?php if (!empty($row['kargo_barkodu'])): ?>
                                                                <?= htmlspecialchars($row['kargo_barkodu']) ?>
                                                            <?php else: ?>
                                                                <span class="text-red"></span>
                                                            <?php endif; ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <?php if (!empty($row['kargo_barkodu'])): ?>
                                                            <?= htmlspecialchars($row['kargo_barkodu']) ?>
                                                        <?php else: ?>
                                                            <span class="text-red"></span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>


                                                <td>
                                                    <?php if (!empty($row['parasut_fatura_numarasi'])): ?>
                                                        <?= htmlspecialchars($row['parasut_fatura_numarasi']) ?>
                                                    <?php elseif ($row['kargo'] === 'Bedelsiz'): ?>
                                                        <span class="badge rounded-pill text-bg-warning">Bedelsiz</span>
                                                    <?php else: ?>
                                                       <span class="badge rounded-pill text-bg-info">SEM<?= htmlspecialchars($row['id']) ?></span>

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
                                                <td><?= htmlspecialchars($resmilesmeDurumu) ?></td>
                                                <td><?= htmlspecialchars($barkodDurumu) ?></td>
                                                <td><?= htmlspecialchars($odemeSarti) ?></td>
                                               
                                            </tr>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

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
        // Excel İndir
        document.getElementById("downloadExcel").addEventListener("click", function () {
            var table = document.getElementById("datatable");
            var workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            XLSX.writeFile(workbook, "TabloVerileri.xlsx");
        });
    </script>

    <script>
        function printPaymentBarcodes() {
        fetch('odeme_sartli_toplu.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Barkod Çıktısı Alındı',
                        text: 'ÖDEME ŞARTLI siparişler için barkod çıktısı başarıyla oluşturuldu.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bazı siparişler iptal edilmiş veya işlem yapılamadı.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Barkod çıktısı alma işlemi sırasında bir sorun oluştu.'
                });
            });
    }
    </script>
    <script>
        function printBarcodes() {
        fetch('print_barcodes.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Barkod Çıktısı Alındı',
                        text: 'UA ve Bedelsiz siparişler için barkod çıktısı başarıyla oluşturuldu.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bazı siparişler iptal edilmiş veya işlem yapılamadı.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Barkod çıktısı alma işlemi sırasında bir sorun oluştu.'
                });
            });
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function printBarcodes() {
        fetch('print_barcodes.php')
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        Swal.fire({
                            icon: 'warning',
                            title: data.title,
                            text: data.message
                        });
                    });
                } else {
                    response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        window.open(url, '_blank');
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: 'Barkod yazdırma işlemi başarısız!'
                });
            });
    }
    </script>
	
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