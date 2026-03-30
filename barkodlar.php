<?php
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'DB.php'; // Veritabanı bağlantısını dahil edin

// Veritabanından barkodları çek
try {
    $db = new DB();
    $result = $db->query("SELECT * FROM ptt_kargo_barkodlari ORDER BY id DESC");
    $barkodlar = [];
    while ($row = $db->fetchAssoc($result)) {
        $barkodlar[] = $row;
    }
} catch (Exception $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Barkod Listesi | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Yüklenen barkodları görüntüleyin ve yönetin." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />


    <style>
        .table-container {
            margin-top: 20px;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .badge {
            font-size: 14px;
        }
        .btn {
            font-size: 14px;
            padding: 5px 10px;
        }
    </style>
</head>

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">
        <?php include 'tema/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- Header -->
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Barkod Listesi</h4>
                            <p class="text-muted fs-14 mt-1">Yüklediğiniz barkodları burada görüntüleyebilir ve düzenleyebilirsiniz.</p>
                        </div>
                        <div>
                            <a href="barkodyukleptt.php" class="btn btn-outline-primary">Yeni Barkod Yükle</a>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="table-container">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Barkod</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($barkodlar)): ?>
                                    <?php foreach ($barkodlar as $barkod): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($barkod['id']); ?></td>
                                            <td><?php echo htmlspecialchars($barkod['kod']); ?></td>
                                            <td>
                                                <?php echo $barkod['durum'] == 0 
                                                    ? '<span class="badge bg-warning">Beklemede</span>' 
                                                    : '<span class="badge bg-success">Tamamlandı</span>'; ?>
                                            </td>
                                            <td>
                                                <!-- Düzenleme ve Silme -->
                                                <button 
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="<?php echo $barkod['id']; ?>"
                                                    data-kod="<?php echo $barkod['kod']; ?>">
                                                    Düzenle
                                                </button>
                                                <button 
                                                    class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?php echo $barkod['id']; ?>">
                                                    Sil
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Henüz barkod yüklenmedi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'tema/footer.php'; ?>
    </div>

    <!-- Düzenleme Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="barkod_duzenle.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Barkod Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label for="editKod" class="form-label">Barkod</label>
                            <input type="text" class="form-control" name="kod" id="editKod" required>
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

    <!-- Silme Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="barkod_sil.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Barkod Sil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bu barkodu silmek istediğinizden emin misiniz?</p>
                        <input type="hidden" name="id" id="deleteId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-danger">Sil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Düzenleme modalına verileri doldur
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const kod = button.getAttribute('data-kod');
            document.getElementById('editId').value = id;
            document.getElementById('editKod').value = kod;
        });

        // Silme modalına ID'yi doldur
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            document.getElementById('deleteId').value = id;
        });
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
		
		
        <script src="assets/js/app.js"></script>
		<script  src="./script.js"></script>


    </body>

</html>
