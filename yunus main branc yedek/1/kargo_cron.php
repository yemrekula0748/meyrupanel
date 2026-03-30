<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/ahmeti/ptt-kargo-api/src/PttVeriYukle2.php';
require_once 'DB.php'; // Veritabanı bağlantısı

use Ahmeti\PttKargoApi\PttVeriYukle2;

$db = new DB(); // Veritabanı bağlantı sınıfı

try {
    // İlk 20 siparişi al
    $query = "SELECT * FROM siparisler WHERE kargo_cron = 0 ORDER BY id ASC LIMIT 20";
    $siparisler = $db->query($query);

    if ($siparisler->num_rows > 0) {
        // PTT Kargo API nesnesini hazırla
        $ptt = (new PttVeriYukle2())
            ->kullanici('402955632') // Kullanıcı adı
            ->sifre('J3fbefKlzi5oBOfMsWQ') // Şifre
            ->musteriId('703083141') // Müşteri ID
            ->dosyaAdi(date('Ymd-His-') . uniqid()) // Dosya adı
            ->gonderiTur('KARGO') // Gönderi türü
            ->gonderiTip('NORMAL'); // Gönderi tipi

        while ($siparis = $siparisler->fetch_assoc()) {
            // Durum 0 olan ilk barkodu al
            $barkodQuery = "SELECT * FROM ptt_kargo_barkodlari WHERE durum = 0 ORDER BY id ASC LIMIT 1";
            $barkodResult = $db->query($barkodQuery);

            if ($barkodResult->num_rows > 0) {
                $barkod = $barkodResult->fetch_assoc();

                // Siparişin ödeme tipi kontrol ediliyor
                $odemeSartli = ($siparis['kargo'] === 'Ödeme Şartlı');
                $odemeSartUcreti = $odemeSartli ? $siparis['odeme_sarti'] : '0'; // Ödeme şartlıysa tablo verisi, değilse '0'
                $rezerve1 = $odemeSartli ? '15735142' : ''; // Ödeme şartlıysa sabit değer, değilse boş

                // Sipariş bilgilerini PTT API'ye ekle
                $ptt->aAdres($siparis['musteri_adresi'])
                    ->agirlik($siparis['agirlik'])
                    ->aliciAdi($siparis['musteri_ismi'])
                    ->aliciIlAdi($siparis['musteri_il'])
                    ->aliciIlceAdi($siparis['musteri_ilce'])
                    ->aliciSms($siparis['musteri_telefonu'])
                    ->barkodNo($barkod['kod']) // Barkodu kullan
                    ->boy('1')
                    
                    ->desi($siparis['desi'])
                    ->ekhizmet('MH')
                    ->en('1')
                    ->musteriReferansNo('REF-' . $siparis['id']) // Sipariş ID'si referans
                    ->odemesekli('UA')
                    ->odeme_sart_ucreti('100')
                    ->rezerve1('15735142')
					->deger_ucreti('100')
                    ->yukseklik('1')
                    ->ekle();

                // İşlenen barkodun durumunu güncelle
                $db->query("UPDATE ptt_kargo_barkodlari SET durum = 1 WHERE kod = '{$barkod['kod']}'");

                // Sipariş tablosundaki kargo_barkodu kolonunu ve kargo_cron kolonunu güncelle
                $db->query("UPDATE siparisler SET kargo_barkodu = '{$barkod['kod']}', kargo_cron = 1 WHERE id = {$siparis['id']}");
            } else {
                echo "Barkod bulunamadı. İşleme devam ediliyor...\n";
                continue; // Bir sonraki siparişe geç
            }
        }

        // Gönderim işlemini tamamla
        $result = $ptt->yukle();

        if (is_array($result) && $result['hataKodu'] == 1) {
            echo "Başarılı! Gönderilen barkodlar:\n";
            print_r($result['dongu']);
        } else {
            echo "Hata: " . $result['aciklama'];
        }
    } else {
        echo "Gönderilecek sipariş bulunamadı.";
    }
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
