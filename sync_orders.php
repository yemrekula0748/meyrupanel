<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Oturumu başlat
require_once 'DB.php';

$db = new DB();

// Kullanıcının oturumdaki `user_id` değerini alın
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("Hata: Kullanıcı oturum açmamış.");
}

// Kullanıcının adını `users` tablosundan çek
$userQuery = $db->getConn()->prepare("SELECT name FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userQuery->bind_result($username);
$userQuery->fetch();
$userQuery->close();

if (!$username) {
    die("Hata: Kullanıcı bilgisi bulunamadı.");
}

// calisanikasvericekme.php'yi tetikle (API'yi çağırır ve ikas_siparisler tablosuna veri yazar)
ob_start();
include 'calisanikasvericekme.php';
ob_end_clean();

// Sadece `islendimi = 0` veya `islendimi IS NULL` olan siparişleri çek
$newOrders = $db->query("SELECT * FROM ikas_siparisler WHERE islendimi = 0 OR islendimi IS NULL");

$addedOrders = 0;

while ($order = $newOrders->fetch_assoc()) {
    $siparisTarihi = $order['createdAt'] ? date('Y-m-d H:i:s', strtotime($order['createdAt'])) : null;
    $musteriIsmi = trim(($order['customer_firstname'] ?? '') . " " . ($order['customer_lastname'] ?? ''));
    $urunler = $order['productNames'] ?? 'Belirtilmemiş';
    
    // Ödeme şartını tam sayı olarak al
    $odemeSarti = (int)($order['totalFinalPrice'] ?? 0);
    
    // Telefon numarasından +9 prefix'ini kaldır
    $musteriTelefonu = $order['phone'] ?? 'Belirtilmemiş';
    if (substr($musteriTelefonu, 0, 2) === '+9') {
        $musteriTelefonu = substr($musteriTelefonu, 2);
    }
    
    $musteri_il = $order['city'] ?? 'Belirtilmemiş';
    $musteriIlce = $order['district'] ?? 'Belirtilmemiş';
    $desi = 1; // Varsayılan değer
    
    // Müşteri adresini JSON'dan çıkar
    $shippingAddress = $order['shippingAddress'] ?? '';
    $musteriAdresi = 'Adres belirtilmemiş';
    
    // Debug için paymentMethods verisini kontrol et
    echo "PaymentMethods: " . $order['paymentMethods'] . "\n";

    // Varsayılan kargo değeri
    $kargo = 'Bilinmiyor';

    // PaymentType kontrolü - direkt string kontrolü
    $paymentType = $order['paymentMethods'];

    switch($paymentType) {
        case 'CASH_ON_DELIVERY':
            $kargo = 'Ödeme Şartlı';
            break;
        case 'MONEY_ORDER':
        case 'CREDIT_CARD':
            $kargo = 'Bedelsiz';
            break;
        default:
            $kargo = 'Bilinmiyor';
    }

    echo "Seçilen Kargo Tipi: " . $kargo . "\n";

    // SQL sorgusunu güncelle
    $stmt = $db->getConn()->prepare("
        INSERT INTO siparisler (
            siparis_tarihi, 
            musteri_ismi, 
            urunler, 
            odeme_sarti, 
            musteri_telefonu,
            musteri_il, 
            musteri_ilce,
            musteri_adresi, 
            desi, 
            kargo, 
            hangikargo,
            hangisayfa
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $hangisayfa = 'iKas';

    $stmt->bind_param("ssssssssssss", 
        $siparisTarihi, 
        $musteriIsmi, 
        $urunler, 
        $odemeSarti, 
        $musteriTelefonu, 
        $musteri_il,  
        $musteriIlce, 
        $shippingAddress,
        $desi, 
        $kargo, 
        $username,
        $hangisayfa
    );

    try {
        $stmt->execute();
        $addedOrders++;

        // İşlenen siparişin `islendimi` alanını güncelle
        $updateQuery = $db->getConn()->prepare("UPDATE ikas_siparisler SET islendimi = 1 WHERE id = ?");
        $updateQuery->bind_param("i", $order['id']);
        $updateQuery->execute();
        $updateQuery->close();
		
		
		
		












			//$urls = [
				//	'https://semre.hpanel.com.tr/sadi_yunusemre_musteri_cron.php',
				//	'https://semre.hpanel.com.tr/yunusemre_fatura_olustur_cron.php',
				//	'https://semre.hpanel.com.tr/yunusemrehepsijet.php',
				//	'https://semre.hpanel.com.tr/yunusemrehepsijetbedelsiz.php',
				//	'https://semre.hpanel.com.tr/sevimaydinpttsartliodeme.php',
				//	'https://semre.hpanel.com.tr/yunusemrepttsartliodeme.php',
				//	'https://semre.hpanel.com.tr/yunusemrepttsartliodeme.php',
				//	'https://semre.hpanel.com.tr/sevimaydinpttbedelsizodeme.php',
				//	'https://semre.hpanel.com.tr/yunusemrehepsijetbedelsiz.php',
				//	'https://semre.hpanel.com.tr/yunusemrehepsijetbedelsiz.php'
				//];

				//foreach ($urls as $url) {
				//	$ch = curl_init($url);
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					//$response = curl_exec($ch);
					//curl_close($ch);
					//usleep(300000); 
				//}
		


if ($kargo == 'Ödeme Şartlı') {
        if ($username == 'Yunus Emre - Hepsijet') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/yunusemrehepsijet.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } elseif ($username == 'Sevim Aydın - PTT') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/sevimaydinpttsartliodeme.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } elseif ($username == 'Yunus Emre - PTT') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/yunusemrepttsartliodeme.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    } elseif ($kargo == 'Bedelsiz') {
        if ($username == 'Yunus Emre - PTT') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/yunusemrepttsartliodeme.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } elseif ($username == 'Yunus Emre - Hepsijet') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/yunusemrehepsijetbedelsiz.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } elseif ($username == 'Sevim Aydın - PTT') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/sevimaydinpttbedelsizodeme.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    } elseif ($kargo == 'Ücreti Alıcıdan') {
       if ($username == 'Yunus Emre - Hepsijet') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/yunusemrehepsijetbedelsiz.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } elseif ($username == 'Yunus Emre - PTT') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/yunusemrepttucretialicidan.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } elseif ($username == 'Sevim Aydın - PTT') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://semre.hpanel.com.tr/sevimaydinpttucretialicidan.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
	}






if ($username === 'Yunus Emre - Hepsijet') {
    $urls = [
        'https://semre.hpanel.com.tr/sadi_yunusemre_musteri_cron.php',
        'https://semre.hpanel.com.tr/yunusemre_fatura_olustur_cron.php'
    ];

    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        usleep(500000); // 
    }
}	



if ($username === 'Sevim Aydın - PTT') {
    $urls = [
        'https://semre.hpanel.com.tr/sevimaydin_musteriden_faturaya.php',
        
    ];

    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        usleep(500000); // 
    }
}



if ($username === 'Yunus Emre - PTT') {
    $urls = [
        'https://semre.hpanel.com.tr/sadi_yunusemre_musteri_cron.php',
        'https://semre.hpanel.com.tr/yunusemre_fatura_olustur_cron.php'
    ];

    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        usleep(500000); // 
    }
}


		
		
		
		
    } catch (mysqli_sql_exception $e) {
        echo "Veritabanı Hatası: " . $e->getMessage() . "\n";
    }

    $stmt->close();
}

echo "Yeni eklenen sipariş sayısı: $addedOrders\n";
?>
