<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? htmlspecialchars($_POST['id']) : '';
    $musteri_telefonu = isset($_POST['musteri_telefonu']) ? htmlspecialchars($_POST['musteri_telefonu']) : '';
    $musteri_ismi = isset($_POST['musteri_ismi']) ? htmlspecialchars($_POST['musteri_ismi']) : '';
    $musteri_adresi = isset($_POST['musteri_adresi']) ? htmlspecialchars($_POST['musteri_adresi']) : '';
    $odeme_sarti = isset($_POST['odeme_sarti']) ? htmlspecialchars($_POST['odeme_sarti']) : '';
    $user_name = isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : '';

    // Verileri kontrol etmek için ekrana yazdırabilirsiniz
    echo "ID: $id <br>";
    echo "Müşteri Telefonu: $musteri_telefonu <br>";
    echo "Müşteri İsmi: $musteri_ismi <br>";
    echo "Müşteri Adresi: $musteri_adresi <br>";
    echo "Ödeme Şartı: $odeme_sarti <br>";
    echo "Kullanıcı Adı: $user_name <br>";

    // Fatura kesme işlemleri burada yapılabilir
    // ...
}
?>
