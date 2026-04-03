<?php
require_once 'DB.php';
require_once 'vendor/autoload.php'; // Composer autoload
use Picqer\Barcode\BarcodeGeneratorPNG;

// Veritabanı Bağlantısı
$db = new DB();

// SQL Sorgusu
$sql = "SELECT * FROM siparisler WHERE barkod_basilma_durumu = ? AND kargo = ? AND kargo_barkodu IS NOT NULL LIMIT 100";

$params = ['Basılmamış', 'Ödeme Şartlı'];
$types = "ss";

// Verileri Sorgula
$result = $db->query($sql, $params, $types);

$count = 0; // Kayıt sayacı
$total_records = $db->numRows($result); // Toplam kayıt sayısı

// Barkod Generator


$generator = new BarcodeGeneratorPNG();
$scale = 8;
$height = 350;


function sayiYaziyla($sayi) {
    $birler = ["", "BİR", "İKİ", "ÜÇ", "DÖRT", "BEŞ", "ALTI", "YEDİ", "SEKİZ", "DOKUZ"];
    $onlar = ["", "ON", "YİRMİ", "OTUZ", "KIRK", "ELLİ", "ALTMIŞ", "YETMİŞ", "SEKSEN", "DOKSAN"];
    
    if($sayi == 0) {
        return "SIFIR";
    }
    
    $yazi = "";
    
    // Binler
    $binler = intval($sayi / 1000);
    if($binler > 0) {
        if($binler == 1) {
            $yazi .= "BİN";
        } else {
            $yazi .= $birler[$binler] . "BİN";
        }
    }
    
    // Yüzler
    $yuzler_basamak = intval(($sayi % 1000) / 100);
    if($yuzler_basamak > 0) {
        if($yuzler_basamak == 1) {
            $yazi .= "YÜZ";
        } else {
            $yazi .= $birler[$yuzler_basamak] . "YÜZ";
        }
    }
    
    // Onlar
    $onlar_basamak = intval(($sayi % 100) / 10);
    if($onlar_basamak > 0) {
        $yazi .= $onlar[$onlar_basamak];
    }
    
    // Birler
    $birler_basamak = $sayi % 10;
    if($birler_basamak > 0) {
        $yazi .= $birler[$birler_basamak];
    }
    
    return $yazi;
}

// HTML Başlat
echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<title>Fatura Yazdırma</title>';
echo '<style>
    @media print {
        .coklu_fatura {
            page-break-after: always;
        }
        .coklu_fatura:last-child {
            page-break-after: auto;
        }
    }
</style>';
echo '</head>';
echo '<body>';

// Sonuçları Kontrol Et ve HTML Şablonunu Doldur
if ($total_records > 0) {
    while ($row = $db->fetchAssoc($result)) {
        $count++;

        // Şablon Dosyasını Oku
        $html = file_get_contents('e-Fatura.html');
		
        $kdv_orani = 0.10; // %10 KDV oranı
        $kdv_dahil_fiyat = floatval($row['odeme_sarti']); // Gelen fiyat
        $kdv_haric_fiyat = $kdv_dahil_fiyat / (1 + $kdv_orani); // KDV Hariç Fiyat
		
        $kdv_tutari = $kdv_dahil_fiyat - $kdv_haric_fiyat; // KDV Tutarı
		$fiyat_sadece_tamsayi = intval($kdv_dahil_fiyat); // faturadaki küsurat kaldırma
        $odeme_sarti_yazi = sayiYaziyla($kdv_dahil_fiyat);
        $kargo_sorumlu = '';
        if (strpos($row['hangikargo'], 'MeyruKids') !== false) {
            $kargo_sorumlu = 'MEYRUKIDS - Merve Incir';
        } elseif (strpos($row['hangikargo'], 'Sevim Aydın - PTT') !== false) {
            $kargo_sorumlu = 'Sevim Aydın';
        }

        // Barkod PNG Oluşturma
        $kargo_barkodu = htmlspecialchars($row['kargo_barkodu']);
        $barcode_file = 'barkodlar/' . $kargo_barkodu . '.png';
         if (!file_exists($barcode_file)) {
            file_put_contents($barcode_file, $generator->getBarcode($kargo_barkodu, $generator::TYPE_CODE_128, $scale, $height));
        }

        // Dinamik Verilerle Şablonu Doldur
        $html = str_replace('{{kargo_barkodu}}', $kargo_barkodu, $html);
		$html = str_replace('{{barcode}}', '<img src="' . $barcode_file . '" alt="Barkod" style="width: 600px; height: 100px;" />', $html);
        $html = str_replace('{{musteri_ismi}}', htmlspecialchars($row['musteri_ismi']), $html);
        $html = str_replace('{{musteri_adresi}}', htmlspecialchars($row['musteri_adresi']), $html);
        $html = str_replace('{{siparis_tarihi}}', htmlspecialchars($row['siparis_tarihi']), $html);
        $urunlerRaw = preg_split('/[\n,]+/', $row['urunler']);
        $urunlerRaw = array_filter(array_map('trim', $urunlerRaw), function($v) { return $v !== ''; });
        $urunSayilari = array_count_values(array_values($urunlerRaw));
        $urunlerLines = [];
        foreach ($urunSayilari as $urun => $adet) {
            $urunlerLines[] = $adet > 1 ? htmlspecialchars($urun) . ' - ' . $adet . ' ADET' : htmlspecialchars($urun);
        }
        $satirSayisi = count($urunlerLines);
        if ($satirSayisi <= 3) $urunFontSize = '27px';
        elseif ($satirSayisi <= 5) $urunFontSize = '21px';
        elseif ($satirSayisi <= 8) $urunFontSize = '16px';
        else $urunFontSize = '13px';
        $urunlerHtml = '<span style="font-size:' . $urunFontSize . ';font-weight:bold;">' . implode('<br>', $urunlerLines) . '</span>';
        $html = str_replace('{{urunler}}', $urunlerHtml, $html);
        $html = str_replace('{{hangisayfa}}', htmlspecialchars($row['hangisayfa']), $html);
        $html = str_replace('{{musteri_il}}', htmlspecialchars($row['musteri_il']), $html);
        $html = str_replace('{{musteri_ilce}}', htmlspecialchars($row['musteri_ilce']), $html);
        $html = str_replace('{{parasut_fatura_numarasi}}', htmlspecialchars($row['parasut_fatura_numarasi']), $html);
        $html = str_replace('{{musteri_telefonu}}', htmlspecialchars($row['musteri_telefonu']), $html);
       // $html = str_replace('{{odeme_sarti}}', number_format($kdv_dahil_fiyat, 2, ',', '.'), $html);
		$html = str_replace('{{odeme_sarti}}', $fiyat_sadece_tamsayi, $html);
        $html = str_replace('{{kdv_haric_fiyat}}', number_format($kdv_haric_fiyat, 2, ',', '.'), $html);
        $html = str_replace('{{kdv_tutari}}', number_format($kdv_tutari, 2, ',', '.'), $html);
        $html = str_replace('{{odeme_sarti_yazi}}', $odeme_sarti_yazi, $html);
        $html = str_replace('{{kargo_sorumlu}}', $kargo_sorumlu, $html);
        $html = str_replace('{{birim_fiyat}}', number_format($row['birim_fiyat'] ?? 0, 2, ".", "") . ' ₺', $html);
		
		// Mevcut hangikargo değerini al
$hangikargo = $row['hangikargo'];

// Gelen texti kontrol et ve uygun metni belirle
if (stripos($hangikargo, 'PTT') !== false) {
    $hangikargoMetni = 'PTT';
} elseif (stripos($hangikargo, 'Hepsijet') !== false) {
    $hangikargoMetni = 'HEPSİJET';
} else {
    $hangikargoMetni = 'Diğer'; // Eğer textte PTT veya Hepsijet yoksa varsayılan metin
}

// Değeri HTML'de yerleştir
$html = str_replace('{{hangikargo}}', htmlspecialchars($hangikargoMetni), $html);

$siparisNo = 'MEY' . htmlspecialchars($row['id']);
$html = str_replace('{{siparisno}}', $siparisNo, $html);
		
		
		
		

        // Dinamik HTML Çıktısını Görüntüle
        echo '<div class="coklu_fatura">' . $html . '</div>';

        // Yazdırılan kaydın durumunu "Basılmış" olarak güncelle
        $update_sql = "UPDATE siparisler SET barkod_basilma_durumu = ? WHERE id = ?";
        $db->query($update_sql, ['Basılmış', $row['id']], "si");
    }
} else {
    echo "Kriterlere uyan bir kayıt bulunamadı.";
}

// HTML Kapat
echo '</body>';
echo '</html>';
