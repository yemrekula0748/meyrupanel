<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$kullanici_adi = $_SESSION['kullanici_adi'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mesaj_metni = $_POST['mesaj_metni'] ?? '';
    $dosya_tipi = null;
    $dosya_yolu = null;

    if (!empty($_FILES['dosya']['name'])) {
        $dosya_tipi = pathinfo($_FILES['dosya']['name'], PATHINFO_EXTENSION);
        $dosya_tipi = strtolower($dosya_tipi);
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'mp3', 'mp4'];
        
        if (in_array($dosya_tipi, $allowed_types)) {
            $dosya_tipi = (strpos($dosya_tipi, 'mp3') !== false) ? 'ses' : 
                          ((strpos($dosya_tipi, 'mp4') !== false) ? 'video' : 'resim');
            $dosya_yolu = 'uploads/' . time() . '_' . $_FILES['dosya']['name'];
            move_uploaded_file($_FILES['dosya']['tmp_name'], $dosya_yolu);
        }
    }

    $sql = "INSERT INTO mesajlar (kullanici_id, mesaj_metni, dosya_yolu, dosya_tipi) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $_SESSION['user_id'], $mesaj_metni, $dosya_yolu, $dosya_tipi);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sohbet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Merhaba, <?= htmlspecialchars($kullanici_adi) ?></h2>
            <a href="logout.php">Çıkış Yap</a>
        </div>
        <div class="chat-box" id="chat-box">
            <!-- Mesajlar buraya eklenecek -->
        </div>
        <div class="chat-input">
            <form method="POST" action="" enctype="multipart/form-data" id="message-form">
                <input type="text" name="mesaj_metni" placeholder="Mesajınızı yazın...">
                <label for="file-upload" class="custom-file-upload">
                    Dosya Seç
                </label>
                <input id="file-upload" type="file" name="dosya">
                <button type="submit">Gönder</button>
            </form>
        </div>
    </div>

    <!-- Emoticons script -->
    <script src="https://cdn.jsdelivr.net/npm/@twemoji/twemoji@14.0.0/dist/twemoji.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        twemoji.parse(document.body);
        const chatBox = document.getElementById("chat-box");
        chatBox.scrollTop = chatBox.scrollHeight;

        // Otomatik mesaj güncelleme
        setInterval(function() {
            fetch('fetch_messages.php')
                .then(response => response.text())
                .then(data => {
                    chatBox.innerHTML = data;
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        }, 