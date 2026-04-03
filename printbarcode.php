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

    <!-- Inter Font + Crimson Theme -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5 !important; background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c41a1a' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") !important; }
        body, .content-page, .content, .container-fluid { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important; }
        .card { border-radius: 1rem !important; border: none !important; box-shadow: 0 4px 6px -1px rgba(196,26,26,0.08), 0 10px 30px -5px rgba(196,26,26,0.10) !important; }
        .card-header { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; border-radius: 1rem 1rem 0 0 !important; padding: 14px 20px !important; border: none !important; }
        .card-header .card-title { color: #fff !important; font-family: 'Inter', sans-serif !important; font-weight: 700 !important; font-size: 1rem !important; margin: 0 !important; }
        .table thead th { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; color: #fff !important; font-family: 'Inter', sans-serif !important; font-weight: 600 !important; font-size: 0.75rem !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; white-space: nowrap !important; border: none !important; padding: 12px 10px !important; }
        .table tbody td { font-family: 'Inter', sans-serif !important; font-size: 0.84rem !important; vertical-align: middle !important; color: #374151 !important; border-color: #f3f4f6 !important; }
        .table tbody tr:hover td { background-color: #fff5f5 !important; }
        .cs-page-title { display: inline-flex; align-items: center; gap: 10px; font-family: 'Inter', sans-serif !important; font-weight: 700 !important; color: #c41a1a !important; }
        .cs-page-title-bar { width: 4px; height: 28px; background: linear-gradient(135deg, #c41a1a, #8b0f0f); border-radius: 4px; display: inline-block; }
        .btn-cs-crimson { background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%) !important; border: none !important; border-radius: 0.65rem !important; color: #fff !important; font-family: 'Inter', sans-serif !important; font-weight: 600 !important; font-size: 0.85rem !important; padding: 8px 18px !important; box-shadow: 0 2px 8px rgba(196,26,26,0.25) !important; transition: opacity 0.15s !important; }
        .btn-cs-crimson:hover { opacity: 0.88 !important; color: #fff !important; }
        .btn-cs-outline { background: #fff !important; border: 1.5px solid #c41a1a !important; border-radius: 0.65rem !important; color: #c41a1a !important; font-family: 'Inter', sans-serif !important; font-weight: 600 !important; font-size: 0.85rem !important; padding: 8px 18px !important; transition: all 0.15s !important; }
        .btn-cs-outline:hover { background: #c41a1a !important; color: #fff !important; }
        .badge { font-family: 'Inter', sans-serif !important; font-weight: 600 !important; border-radius: 9999px !important; }
        .form-control, .form-select { font-family: 'Inter', sans-serif !important; border-radius: 0.5rem !important; font-size: 0.85rem !important; }
        .form-control:focus, .form-select:focus { border-color: #c41a1a !important; box-shadow: 0 0 0 3px rgba(196,26,26,0.12) !important; }
    </style>
    </head>

    <body data-menu-color="light" data-sidebar="default">
        <div id="app-layout">
            <?php include 'tema/menu.php'; ?>

            <div class="content-page">
                <div class="content">
                    <div class="container-fluid">
                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 m-0 cs-page-title"><span class="cs-page-title-bar"></span>Barkod Alınmayan Siparişler</h4>
                            </div>
                        </div>
                      

                        <!-- Butonlar -->
<div class="d-flex justify-content-start align-items-center mb-3" style="gap:8px;flex-wrap:wrap;">
                            <button class="btn btn-cs-crimson" onclick="window.open('odeme_sartli_toplu.php', '_blank')">ÖDEME ŞARTLI Barkod Çıktısı Al - MH</button>
                            <button class="btn btn-cs-outline" onclick="printBarcodes()">UA-B Barkod Çıktısı Al</button>
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
                                                <td><?= date('d-m-Y', strtotime($row['siparis_tarihi'])) ?></td>
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