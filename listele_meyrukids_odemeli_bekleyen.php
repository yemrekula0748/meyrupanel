<?php
require_once 'DB.php';

$db = new DB();

$sql = "SELECT id, musteri_ismi, parasut_fatura_numarasi, sales_invoice_id FROM siparisler WHERE parasut_resmilesme_durumu = 0 AND iptalmi = 0 AND hangikargo = 'MeyruKids' AND (parasut_fatura_numarasi IS NULL OR parasut_fatura_numarasi = '') AND (sales_invoice_id IS NULL OR sales_invoice_id = '') AND resmimi = 0 AND resmilestir = 0 AND kargo = 'Ödeme Şartlı'";

$result = $db->query($sql);

echo "<h2>MeyruKids - Ödeme Şartlı - Fatura Bekleyen Siparişler</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Müşteri İsmi</th><th>Parasut Fatura Numarası</th><th>Sales Invoice ID</th></tr>";

while ($row = $db->fetchAssoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['musteri_ismi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['parasut_fatura_numarasi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sales_invoice_id']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
