<?php
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>PTT Barkod Yükle | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Barkod yükleme işlemlerini gerçekleştirin." />
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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .content-page {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
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
                            <h4 class="fs-18 fw-semibold m-0">PTT Barkod Yükle</h4>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="mt-4">
                        <form action="ptt_barkod_yukle_sonuc.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Excel Dosyasını Seçin:</label>
                                <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx, .xls" required>
                            </div>
                            <button class="btn btn-outline-primary rounded-pill" type="submit">Yükle</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'tema/footer.php'; ?>
    </div>

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
