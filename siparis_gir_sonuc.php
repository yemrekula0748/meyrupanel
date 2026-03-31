<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'DB.php';

session_start();
$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $siparis_tarihi = $db->escape($_POST['siparis_tarihi']);
        $musteri_ismi = $db->escape($_POST['musteri_ismi']);
        $musteri_telefonu = $db->escape($_POST['musteri_telefonu']);
        $musteri_adresi = $db->escape($_POST['musteri_adresi']);
        $musteri_il = $db->escape($_POST['Iller']);
        $musteri_ilce = $db->escape($_POST['Ilceler']);
        $odeme_sarti = $db->escape($_POST['odeme_sarti']);
        $urunler = $db->escape($_POST['urunler']);
        $desi = $db->escape($_POST['desi']);
        $agirlik = $db->escape($_POST['agirlik']);
        $kargo = $db->escape($_POST['kargo']);
        $yoneticinotu = $db->escape($_POST['yoneticinotu']);
        $faturalandirma_durumu = $db->escape($_POST['faturalandirma_durumu']);
        $barkod_basilma_durumu = $db->escape($_POST['barkod_basilma_durumu']);
		$siparissayfasi = $db->escape($_POST['siparissayfasi']);
        $kargo_cron = 0;

        // Kullanıcı Adını Oturumdan Al ve güvenli hale getir
        $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Bilinmiyor';
        $secureUserName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
		$secureUserName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');

// Eğer müşteri adresinde 'PTT' kelimesi varsa, secureUserName'i "MeyruKids" olarak değiştir.
if (strpos($musteri_adresi, 'PTT') !== false && $secureUserName !== "Sevim Aydın - PTT") {
    $secureUserName = "MeyruKids";
}


        $sql = "INSERT INTO siparisler (
                    siparis_tarihi, musteri_ismi, musteri_telefonu, musteri_adresi,
                    musteri_il, musteri_ilce, odeme_sarti, urunler, desi, agirlik,
                    kargo, yonetici_notu, faturalandirma_durumu, barkod_basilma_durumu, kargo_cron, hangikargo , hangisayfa
                ) VALUES (
                    '$siparis_tarihi', '$musteri_ismi', '$musteri_telefonu', '$musteri_adresi',
                    '$musteri_il', '$musteri_ilce', '$odeme_sarti', '$urunler', $desi, $agirlik,
                    '$kargo', '$yoneticinotu', '$faturalandirma_durumu', '$barkod_basilma_durumu', $kargo_cron, '$secureUserName', '$siparissayfasi'
                )";

  
  
if ($db->query($sql)) {
    // Sipariş verisini başarıyla ekledikten sonra, kargo türüne ve kullanıcı adına göre belirli sayfaları çalıştır
   function runAsyncCurl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Çıktıyı alma, bekleme
    curl_setopt($ch, CURLOPT_TIMEOUT, 1); // 1 saniyede zaman aşımı
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // Bağlantı zamanı aşımı
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); // Yeni bağlantı aç
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Yönlendirmeleri takip et
    curl_setopt($ch, CURLOPT_HEADER, false); // Header bilgisi alma
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // Timeout için sinyal kullanma
    curl_exec($ch);
    curl_close($ch);
}

// Örnek kullanım:
if ($kargo == 'Ödeme Şartlı') {
    if ($secureUserName == 'Yunus Emre - Hepsijet') {
        runAsyncCurl("https://meyrupanel.com.tr/yunusemrehepsijet.php");
    } elseif ($secureUserName == 'Sevim Aydın - PTT') {
        runAsyncCurl("https://meyrupanel.com.tr/sevimaydinpttsartliodeme.php");
    } elseif ($secureUserName == 'MeyruKids') {
        runAsyncCurl("https://meyrupanel.com.tr/yunusemrepttsartliodeme.php");
    }
} elseif ($kargo == 'Bedelsiz') {
    if ($secureUserName == 'MeyruKids') {
        runAsyncCurl("https://meyrupanel.com.tr/yunusemrepttsartliodeme.php");
    } elseif ($secureUserName == 'Yunus Emre - Hepsijet') {
        runAsyncCurl("https://meyrupanel.com.tr/yunusemrehepsijetbedelsiz.php");
    } elseif ($secureUserName == 'Sevim Aydın - PTT') {
        runAsyncCurl("https://meyrupanel.com.tr/sevimaydinpttbedelsizodeme.php");
    }
} elseif ($kargo == 'Ücreti Alıcıdan') {
    if ($secureUserName == 'Yunus Emre - Hepsijet') {
        runAsyncCurl("https://meyrupanel.com.tr/yunusemrehepsijetbedelsiz.php");
    } elseif ($secureUserName == 'MeyruKids') {
        runAsyncCurl("https://meyrupanel.com.tr/yunusemrepttucretialicidan.php");
    } elseif ($secureUserName == 'Sevim Aydın - PTT') {
        runAsyncCurl("https://meyrupanel.com.tr/sevimaydinpttucretialicidan.php");
    }
}

	
	
	if ($secureUserName == 'Sevim Aydın - PTT') {
    $url = 'https://meyrupanel.com.tr/sevimaydin_musteriden_faturaya.php';
} elseif ($secureUserName == 'MeyruKids') {
    $url = 'https://meyrupanel.com.tr/yunusemre_musteriden_faturaya.php';
}

if (isset($url)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'Curl Hatası: ' . curl_error($ch);
    } else {
       
		header("Location: girilen_siparisler.php");
    exit();
    }

    curl_close($ch);
}
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	
	// Başarılı işlemden sonra yönlendirme yap
	
	
	
    header("Location: girilen_siparisler.php");
    exit();
} else {
    throw new Exception("Sipariş eklenemedi");
}

} catch (Exception $e) {
    header("Location: siparis_gir.php?status=error&message=" . urlencode($e->getMessage()));
    exit();
}
}
?> 
