<?php
include 'DB.php';
$db = new DB();

if (isset($_GET['id'])) {
    $id = $db->escape($_GET['id']); // Güvenlik için veri kaçar

    $query = "DELETE FROM siparisler WHERE id = '$id'";
    if ($db->query($query)) {
        echo "<script>
            
            window.location.href = 'girilen_siparisler.php';
        </script>";
    } else {
        echo "<script>
            alert('Sipariş silinirken bir hata oluştu.');
            window.location.href = 'girilen_siparisler.php';
        </script>";
    }
}
?>
