<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = $_POST['kullanici_adi'];
    $parola = $_POST['parola'];

    $hashed_parola = password_hash($parola, PASSWORD_DEFAULT);

    $sql = "INSERT INTO kullanici (kullanici_adi, parola) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $kullanici_adi, $hashed_parola);

    if ($stmt->execute()) {
        echo "Kullanıcı başarıyla eklendi!";
    } else {
        echo "Hata: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
</head>
<body>
    <form method="POST" action="">
        <h2>Kayıt Ol</h2>
        <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required>
        <input type="password" name="parola" placeholder="Parola" required>
        <button type="submit">Kayıt Ol</button>
    </form>
</body>
</html>
