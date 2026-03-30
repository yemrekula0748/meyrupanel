<?php
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı adı oturumdan alınır
$user_name = $_SESSION['user_name'] ?? 'Yabancı';

// Şifre doğrulama ve oturumu açma
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];

    require 'DB.php';
    $db = new DB();
    $sql = "SELECT password FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
    $result = $db->query($sql);

    if ($db->numRows($result) > 0) {
        $user = $db->fetchAssoc($result);
        if (password_verify($password, $user['password'])) {
            header("Location: index.php"); // Başarılı giriş yapıldıysa ana sayfaya yönlendir bacanağım :)
            exit;
        } else {
            $error = "Şifre hatalı.";
        }
    } else {
        $error = "Kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Kilit Ekranı | Admin Giriş Paneli</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc."/>
    <meta name="author" content="Zoyothemes"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="bg-primary-subtle">
    <!-- Begin page -->
    <div class="account-page">
        <div class="container-fluid p-0">
            <div class="row align-items-center g-0">
                <div class="col-xl-5">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card p-3 mb-0">
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <h3>Hoş Geldiniz, <?= htmlspecialchars($user_name) ?></h3>
                                        <p>Lütfen şifrenizi girin</p>
                                    </div>

                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">Şifre</label>
                                            <input type="password" class="form-control" id="password" name="password" required placeholder="Şifrenizi girin">
                                        </div>
                                        <div class="form-group mb-0">
                                            <button class="btn btn-primary w-100" type="submit">Kilit Aç</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="account-page-bg">
                        <div class="text-center">
                            <img src="assets/images/auth-images.svg" class="img-fluid" alt="Lock Screen">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END wrapper -->
</body>
</html>
