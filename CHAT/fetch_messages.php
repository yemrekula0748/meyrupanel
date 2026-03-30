<?php
session_start();
require 'db.php';

$mesajlar = $conn->query("SELECT * FROM mesajlar ORDER BY tarih ASC");

while ($mesaj = $mesajlar->fetch_assoc()): ?>
    <div class="message <?= $mesaj['kullanici_id'] == $_SESSION['user_id'] ? 'sent' : 'received' ?>">
        <?php if ($mesaj['dosya_yolu']): ?>
            <?php if ($mesaj['dosya_tipi'] == 'resim'): ?>
                <img src="<?= htmlspecialchars($mesaj['dosya_yolu']) ?>" alt="Resim">
            <?php elseif ($mesaj['dosya_tipi'] == 'ses'): ?>
                <audio controls>
                    <source src="<?= htmlspecialchars($mesaj['dosya_yolu']) ?>" type="audio/mpeg">
                    Tarayıcınız ses öğesini desteklemiyor.
                </audio>
            <?php elseif ($mesaj['dosya_tipi'] == 'video'): ?>
                <video controls>
                    <source src="<?= htmlspecialchars($mesaj['dosya_yolu']) ?>" type="video/mp4">
                    Tarayıcınız video öğesini desteklemiyor.
                </video>
            <?php endif; ?>
        <?php else: ?>
            <p><?= htmlspecialchars($mesaj['mesaj_metni']) ?></p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
