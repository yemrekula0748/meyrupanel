<?php
session_start();
require 'DB.php'; // Veritabanı bağlantısı

$db = new DB();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['user_name']; // Dropdown'dan seçilen e-posta
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM users WHERE email = '" . $db->escape($email) . "'";
        $result = $db->query($sql);

        if ($db->numRows($result) > 0) {
            $user = $db->fetchAssoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: index.php"); // Başarılı giriş yapıldıysa yönlendirme
                exit;
            } else {
                $error = "Geçersiz şifre.";
            }
        } else {
            $error = "Kullanıcı bulunamadı.";
        }
    } catch (Exception $e) {
        $error = "Bir hata oluştu: " . $e->getMessage();
    }
}

?>

<?php
// Kullanıcı adlarını almak için veritabanından çek
$userSql = "SELECT name, email FROM users";
$userResult = $db->query($userSql);
$users = [];
while ($row = $db->fetchAssoc($userResult)) {
    $users[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Giriş Yap | Sipariş Paneli</title>
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
                                    <div class="mb-0 border-0 p-md-5 p-lg-0 p-4">
                                        <div class="mb-4 p-0 text-center">
                                            <a href="index.php" class="auth-logo">
                                                <img src="assets/images/logo-dark.png" alt="logo-dark" class="mx-auto" height="28" />
                                            </a>
                                        </div>

                                        <div class="auth-title-section mb-3 text-center"> 
                                            <h3 class="text-dark fs-20 fw-medium mb-2">Tekrar Hoş Geldiniz</h3>
                                            <p class="text-dark text-capitalize fs-14 mb-0">Devam etmek için oturum açın.</p>
                                        </div>

                                        <?php if ($error): ?>
                                            <div class="alert alert-danger"><?= $error ?></div>
                                        <?php endif; ?>

                                        <form action="login.php" method="POST" class="my-4">
    <div class="form-group mb-3">
        <label for="user_name" class="form-label">Kullanıcı Seçin</label>
        <select class="form-control" id="user_name" name="user_name" required>
            <option value="">Kullanıcı Seçin</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label">Şifre</label>
        <input class="form-control" type="password" id="password" name="password" required placeholder="Lütfen şifrenizi girin">
    </div>

    <div class="form-group mb-0 row">
        <div class="col-12">
            <div class="d-grid">
                <button class="btn btn-primary" type="submit"> Giriş Yap </button>
            </div>
        </div>
    </div>
</form>


                                        <!--<div class="text-center text-muted mb-4">
                                            <p class="mb-0">Don't have an account? <a class='text-primary ms-2 fw-medium' href='register.php'>Sign up</a></p>
                                        </div>-->
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-7">
                    <div class="account-page-bg p-md-5 p-4">
                        <div class="text-center">
                            <div class="auth-image">
                                <img src="assets/images/auth-images.svg" class="mx-auto img-fluid" alt="images">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
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
