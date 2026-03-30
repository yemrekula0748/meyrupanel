<?php
// Birinci adım: Yeni siparişleri çekip ekle
ob_start();
include 'sync_orders.php';
ob_end_clean();

// İkinci adım: Kargoyu tetikle
ob_start();
include 'tetikle_kargo.php';
ob_end_clean();

// İsterseniz burada işlemin bittiği mesajını gösterebilirsiniz
echo "İşlem tamamlandı!";
?>