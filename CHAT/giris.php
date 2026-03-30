<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını ekleyin
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici_adi = $_POST['kullanici_adi'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if ($kullanici_adi && $sifre) {
        $db = new DB();
        $kullanici_adi = $db->escape($kullanici_adi);
        $sifre = md5($sifre); // Örnek, hash kullanın

        $query = "SELECT * FROM kullanici WHERE kullanici_adi = '$kullanici_adi' AND sifre = '$sifre'";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $_SESSION['logged_in'] = true;
            header('Location: chat.php'); // Başarılı giriş sonrası yönlendirme
            exit;
        } else {
            echo "Geçersiz kullanıcı adı veya şifre.";
        }
    } else {
        echo "Kullanıcı adı ve şifreyi doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş</title>
</head>
<body>
    <form method="post" action="giris.php">
        <label for="kullanici_adi">Kullanıcı Adı:</label>
        <input type="text" id="kullanici_adi" name="kullanici_adi" required>
        <br>
        <label for="sifre">Şifre:</label>
        <input type="password" id="sifre" name="sifre" required>
        <br>
        <button type="submit">Giriş Yap</button>
    </form>
</body>
</html>
