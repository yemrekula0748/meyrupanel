<?php
session_start();
session_unset(); // Oturum verilerini temizler
session_destroy(); // Oturumu sonlandırır

header("Location: login.php"); // Login sayfasına yönlendirir
exit;
?>
