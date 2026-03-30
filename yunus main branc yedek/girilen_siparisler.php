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

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
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
                    include('DB.php');
                    $db = new DB();
                    $sql = "SELECT * FROM siparisler ORDER BY id DESC";
                    $result = $db->query($sql);
                    ?>

                    <div class="mb-3">
                        <input type="text" id="pageSearch" class="form-control" placeholder="Arama yapın...">
                    </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Siparişler Tablosu</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>NO</th>
                                        <th>Barkod</th>
                                        <th>Sipariş Tarihi</th>
                                        <th>Müşteri Adı</th>
                                        <th>Telefon</th>
                                        <th>Adres</th>
                                        <th>Ödeme Şartı</th>
                                        <th>Ürünler</th>
                                        <th>NOT</th>
                                        <th>Kargo</th>
                                        <th>Fatura</th>
                                        <th>Barkod</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
<td>
    <?php if ($row['kargo_barkodu'] !== null): ?>
        <a href="<?php echo htmlspecialchars($row['kargolink']); ?>" target="_blank">
            <?php echo htmlspecialchars($row['kargo_barkodu']); ?>
        </a>
    <?php else: ?>
        <!-- Eğer barkod boş ise boş bir değer göster -->
        <?php echo ''; ?>
    <?php endif; ?>
</td>


                                            <td><?php echo !empty($row['siparis_tarihi']) ? htmlspecialchars(date('d-m-Y', strtotime($row['siparis_tarihi']))) : 'Tarih Yok'; ?></td>
                                            <td><?php echo htmlspecialchars($row['musteri_ismi']); ?></td>
                                            <td><?php echo htmlspecialchars($row['musteri_telefonu']); ?></td>
                                            <td><?php echo htmlspecialchars($row['musteri_adresi']); ?></td>
                                            <td><?php echo htmlspecialchars($row['odeme_sarti']); ?></td>
                                            <td><?php echo htmlspecialchars($row['urunler']); ?></td>
                                            <td><?php echo htmlspecialchars($row['yonetici_notu']); ?></td>
                                            <td><?php echo htmlspecialchars($row['kargo']); ?></td>
                                            <td><?php echo htmlspecialchars($row['faturalandirma_durumu']); ?></td>
                                            <td><?php echo htmlspecialchars($row['barkod_basilma_durumu']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#gorModal<?php echo $row['id']; ?>">
                                                    Gör
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#silModal<?php echo $row['id']; ?>">
                                                    Sil
                                                </button>
												<button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#faturaModal<?php echo $row['id']; ?>">
                                                    Fatura Kes
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Görüntüle Modal -->
                                        <div class="modal fade" id="gorModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="gorModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="gorModalLabel<?php echo $row['id']; ?>">Sipariş Detayları</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Müşteri Adı:</strong> <?php echo htmlspecialchars($row['musteri_ismi']); ?></p>
                                                        <p><strong>Telefon:</strong> <?php echo htmlspecialchars($row['musteri_telefonu']); ?></p>
                                                        <p><strong>Adres:</strong> <?php echo htmlspecialchars($row['musteri_adresi']); ?></p>
                                                        <p><strong>İl:</strong> <?php echo htmlspecialchars(string: $row['musteri_il']); ?></p>
                                                        <p><strong>İlçe:</strong> <?php echo htmlspecialchars(string: $row['musteri_ilce']); ?></p>
                                                        <p><strong>Sipariş Tarihi:</strong> <?php echo htmlspecialchars(date('d-m-Y', strtotime($row['siparis_tarihi']))); ?></p>
                                                        <p><strong>Ödeme Şartı:</strong> <?php echo htmlspecialchars($row['odeme_sarti']); ?></p>
                                                        <p><strong>Ürünler:</strong> <?php echo htmlspecialchars($row['urunler']); ?></p>
                                                        <p><strong>Yönetici Notu:</strong> <?php echo htmlspecialchars($row['yonetici_notu']); ?></p>
                                                        <p><strong>Kargo:</strong> <?php echo htmlspecialchars($row['kargo']); ?></p>
                                                        <p><strong>Fatura Durumu:</strong> <?php echo $row['faturalandirma_durumu'] == 1 ? 'Faturalandı' : 'Faturalanmadı'; ?></p>
                                                        <p><strong>Barkod Durumu:</strong> <?php echo $row['barkod_basilma_durumu'] == 1 ? 'Basılmış' : 'Basılmamış'; ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Düzenle Modal -->
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
                                                                <input type="text" class="form-control" name="odeme_sarti" value="<?php echo htmlspecialchars($row['odeme_sarti']); ?>">
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
                                                                <label for="kargo" class="form-label">Kargo</label>
                                                                <input type="text" class="form-control" name="kargo" value="<?php echo htmlspecialchars($row['kargo']); ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="faturalandirma_durumu" class="form-label">Fatura Durumu</label>
                                                                <select class="form-select" name="faturalandirma_durumu">
                                                                    <option value="1" <?php echo $row['faturalandirma_durumu'] == 1 ? 'selected' : ''; ?>>Faturalandı</option>
                                                                    <option value="0" <?php echo $row['faturalandirma_durumu'] == 0 ? 'selected' : ''; ?>>Faturalanmadı</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="barkod_basilma_durumu" class="form-label">Barkod Durumu</label>
                                                                <select class="form-select" name="barkod_basilma_durumu">
                                                                    <option value="1" <?php echo $row['barkod_basilma_durumu'] == 1 ? 'selected' : ''; ?>>Basilmiş</option>
                                                                    <option value="0" <?php echo $row['barkod_basilma_durumu'] == 0 ? 'selected' : ''; ?>>Basilmamış</option>
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
                                        <div class="modal fade" id="silModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="silModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="siparis_sil.php" method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="silModalLabel<?php echo $row['id']; ?>">Siparişi Sil</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                            <p>Bu siparişi silmek istediğinizden emin misiniz?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                            <button type="submit" class="btn btn-danger">Sil</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
										
										
										  <!-- Fatura  -->
										  <div class="modal fade" id="faturaModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="faturaModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="faturakes.php" method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="faturaModalLabel<?php echo $row['id']; ?>">Fatura Kes</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                            <p>Bu Siparişe Fatura Kesmek İstediğinize Eminmisiniz ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                            <button type="submit" class="btn btn-danger">Faturalandır</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'tema/footer.php'; ?>
    </div>
    

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