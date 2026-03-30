<?php
require_once 'DB.php';
require_once 'ikaskargoislem.php';

$db = new DB();

// "kargo_barkodu" boş olan, "hangisayfa" iKas olan siparişleri çek
$result = $db->query("
    SELECT * 
    FROM siparisler
    WHERE hangisayfa = 'iKas'
      AND (kargo_barkodu IS NULL OR kargo_barkodu = '')
");

while ($siparis = $result->fetch_assoc()) {
    $username = $siparis['hangikargo'] ?? 'Bilinmiyor';
    $kargo = $siparis['kargo'] ?? 'Bilinmiyor';

    // Kargo işlemini tetikle
    ikaskargoIslem($username, $kargo);

    // İstersek burada "kargo_barkodu" kaydı da yapabiliriz
    // $barkod = '...'; // dış sistemden gelen cevap
    // $update = $db->getConn()->prepare("UPDATE siparisler SET kargo_barkodu = ? WHERE id = ?");
    // $update->bind_param("si", $barkod, $siparis['id']);
    // $update->execute();
    // $update->close();

    echo "Tetikleme işlemi tamamlandı. Sipariş ID: ".$siparis['id']."\n";
}
?>