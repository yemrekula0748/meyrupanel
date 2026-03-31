<?php
require_once 'DB.php';

$db = new DB();

$sql = "SELECT id, sales_invoice_id FROM siparisler WHERE resmimi = 1 AND sales_invoice_id IS NOT NULL AND iptalmi = 0 AND parasut_resmilesme_durumu = 0 AND (hangikargo = 'MeyruKids' OR hangikargo = 'Yunus Emre - Hepsijet')";

$result = $db->query($sql);

echo "<h2>Resmileştirilecek Siparişler</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Sales Invoice ID</th></tr>";

while ($row = $db->fetchAssoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sales_invoice_id']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
