<?php
require_once 'DB.php'; // Veritabanı bağlantı dosyasını dahil edin

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=GiderPusula_" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
echo '<body>';

// Tablo başlıkları
echo '<table border="1">';
echo '<thead>
        <tr>
            <th>Sipariş Tarihi</th>
            <th>Hangi Sayfa</th>
            <th>Müşteri İsmi</th>
            <th>Müşteri Telefonu</th>
            <th>Müşteri Adresi</th>
            <th>Müşteri İl</th>
            <th>Müşteri İlçe</th>
            <th>Ürünler</th>
            <th>Ödeme</th>
            <th>Hangi Kargo</th>
            <th>Kargo Barkodu</th>
			<th>FATURA NUMARASI</th>
			
        </tr>
      </thead>';
echo '<tbody>';

try {
    // Veritabanı bağlantısını başlat
    $db = new DB();

    // exceldurum = 0 olan kayıtları seç
    $query = "SELECT id, siparis_tarihi, hangisayfa, musteri_ismi, musteri_telefonu, musteri_adresi, musteri_il, musteri_ilce, urunler, odeme_sarti, hangikargo, kargo_barkodu , parasut_fatura_numarasi
              FROM siparisler 
              WHERE iptalmi = 1 AND kargo='Ödeme Şartlı' ";
    $result = $db->query($query);

    // İndirilmiş siparişlerin ID'lerini toplamak için bir dizi
    $downloadedIds = [];

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['siparis_tarihi']) . '</td>';
        echo '<td>' . htmlspecialchars($row['hangisayfa']) . '</td>';
        echo '<td>' . htmlspecialchars($row['musteri_ismi']) . '</td>';
        echo '<td>' . htmlspecialchars($row['musteri_telefonu']) . '</td>';
        echo '<td>' . htmlspecialchars($row['musteri_adresi']) . '</td>';
        echo '<td>' . htmlspecialchars($row['musteri_il']) . '</td>';
        echo '<td>' . htmlspecialchars($row['musteri_ilce']) . '</td>';
        echo '<td>' . htmlspecialchars($row['urunler']) . '</td>';
        echo '<td>' . htmlspecialchars($row['odeme_sarti']) . '</td>';
        echo '<td>' . htmlspecialchars($row['hangikargo']) . '</td>';
        echo '<td>' . htmlspecialchars($row['kargo_barkodu']) . '</td>';
		echo '<td>' . htmlspecialchars($row['parasut_fatura_numarasi']) . '</td>';
        echo '</tr>';

        // Güncellemek için ID'yi kaydet
        $downloadedIds[] = $row['id'];
    }

    echo '</tbody>';
    echo '</table>';
    echo '</body>';
    echo '</html>';

    // İndirilmiş siparişlerin exceldurum kolonunu 1 yap
   // if (!empty($downloadedIds)) {
      //  $idsToUpdate = implode(',', $downloadedIds); // ID'leri virgülle birleştir
      //  $updateQuery = "UPDATE siparisler SET exceldurum = 1 WHERE id IN ($idsToUpdate)";
      //  $db->query($updateQuery);
   // }
} catch (Exception $e) {
    echo '<tr><td colspan="11">Veri alınırken bir hata oluştu: ' . $e->getMessage() . '</td></tr>';
}
?>
