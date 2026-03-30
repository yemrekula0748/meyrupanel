<?php
session_start();
require 'DB.php'; // Veritabanı bağlantısı

// Giriş yapmamış kullanıcıları engelle
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new DB();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Kullanıcıyı ekle
    try {
        $sql = "INSERT INTO users (name, email, password) VALUES ('" . 
            $db->escape($name) . "', '" . 
            $db->escape($email) . "', '" . 
            $db->escape($password) . "')";
        $db->query($sql);

        header("Location: register.php?success=1");
        exit;
    } catch (Exception $e) {
        $error = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Ekle</title>
    <link href="assets/css/app.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <style>
        .auth-card {
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .auth-title {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
        }
        .btn-primary {
            border-radius: 5px;
            padding: 10px 15px;
        }
    </style>
</head>
<body class="bg-primary-subtle">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="auth-card">
                <h2 class="auth-title">Yeni Kullanıcı Ekle</h2>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Kullanıcı başarıyla oluşturuldu!</div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="form-group mb-3">
                        <label for="name">Ad Soyad</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ad ve Soyad" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="E-posta adresi" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Şifre</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Şifre" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Kullanıcı Ekle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Vendor Scripts -->
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
