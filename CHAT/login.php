<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = $_POST['kullanici_adi'];
    $parola = $_POST['parola'];

    $sql = "SELECT * FROM kullanici WHERE kullanici_adi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kullanici_adi);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($parola, $user['parola'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
        header('Location: chat.php');
        exit;
    } else {
        $error = 'Geçersiz kullanıcı adı veya parola';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <form method="POST" action="">
            <h2>Giriş Yap</h2>
            <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required>
            <input type="password" name="parola" placeholder="Parola" required>
            <button type="submit">Giriş Yap</button>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
