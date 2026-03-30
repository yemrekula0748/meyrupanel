<?php
// Gerekli dosyaları dahil et
include('DB.php');
require 'vendor/autoload.php'; // Composer autoload dosyasını dahil edin
use Picqer\Barcode\BarcodeGeneratorPNG;

// Veritabanı bağlantısı
$db = new DB();







function sayiyiYaziyaCevir($sayi) { $birler = ["", "BİR", "İKİ", "ÜÇ", "DÖRT", "BEŞ", "ALTI", "YEDİ", "SEKİZ", "DOKUZ"]; $onlar = ["", "ON", "YİRMİ", "OTUZ", "KIRK", "ELLİ", "ALTMIŞ", "YETMİŞ", "SEKSEN", "DOKSAN"]; $yuzler = ["", "YÜZ", "İKİYÜZ", "ÜÇYÜZ", "DÖRTYÜZ", "BEŞYÜZ", "ALTİYÜZ", "YEDİYÜZ", "SEKİZYÜZ", "DOKUZYÜZ"]; $binler = ["", "BİN"]; if ($sayi == 0) { return "SIFIR"; } $sayiStr = strval($sayi); $uzunluk = strlen($sayiStr); $okunus = ""; if ($uzunluk == 4) { $okunus .= $binler[1]; $okunus .= $yuzler[intval($sayiStr[1])]; $okunus .= $onlar[intval($sayiStr[2])]; $okunus .= $birler[intval($sayiStr[3])]; } elseif ($uzunluk == 3) { $okunus .= $yuzler[intval($sayiStr[0])]; $okunus .= $onlar[intval($sayiStr[1])]; $okunus .= $birler[intval($sayiStr[2])]; } elseif ($uzunluk == 2) { $okunus .= $onlar[intval($sayiStr[0])]; $okunus .= $birler[intval($sayiStr[1])]; } else { $okunus .= $birler[intval($sayiStr[0])]; } return $okunus; }


// Siparişleri alın
$sql = "SELECT * FROM siparisler WHERE TRIM(kargo) = 'Ödeme Şartlı' LIMIT 100";
$orders = $db->query($sql)->fetch_all(MYSQLI_ASSOC);




// HTML çıktısını başlat
ob_start();

?>



<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Fatura</title>
    <style>
        /* CSS kodları */
        .coklu_fatura { min-height: 100vh; page-break-after: always; }
        <style type="text/css">
        .coklu_fatura{min-height:100vh}
        @media print {
            html, body {
                height: 100%;
            }
        }
        body {
            background-color: #FFFFFF;
            font-family: 'Tahoma', "Times New Roman", Times, serif;
            font-size: 11px;
            color: black;
        }

        h1,
        h2 {
            padding-bottom: 3px;
            padding-top: 3px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        h1 {
            font-size: 1.4em;
            text-transform: none;
        }

        h2 {
            font-size: 1em;
            color: brown;
        }

        h3 {
            font-size: 1em;
            color: #333333;
            text-align: justify;
            margin: 0;
            padding: 0;
        }

        h4 {
            font-size: 1.1em;
            font-style: bold;
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        hr {
            height: 1px;
            color: #000000;border:1px;
            background-color: #000000;
            border-bottom: 1px solid #000000;
        }

        p,
        ul,
        ol {
            margin-top: 1.5em;
        }

        ul,
        ol {
            margin-left: 3em;
        }

        blockquote {
            margin-left: 3em;
            margin-right: 3em;
            font-style: italic;
        }

        a {
            text-decoration: none;
            color: #70A300;
        }

        a:hover {
            border: none;
            color: #70A300;
        }

        #despatchTable {
            border-collapse: collapse;
            font-size: 11px;
            float: right;
            border-color: gray;
        }

        #ettnTable {
            border-collapse: collapse;
            font-size: 11px;
            border-color: gray;
        }

        #customerPartyTable {
            border-width: 0px;
            border-spacing: ;
            border-style: inset;
            border-color: gray;
            border-collapse: collapse;
            background-color:
        }

        #customerIDTable {
            border-width: 2px;
            border-spacing: ;
            border-style: inset;
            border-color: gray;
            border-collapse: collapse;
            background-color:
        }

        #customerIDTableTd {
            border-width: 2px;
            border-spacing: ;
            border-style: inset;
            border-color: gray;
            border-collapse: collapse;
            background-color:
        }

        #lineTable {
            border-width: 2px;
            border-spacing: ;
            border-style: inset;
            border-color: black;
            border-collapse: collapse;
            background-color: ;
        }

        td.lineTableTd {
            border-width: 1px;
            padding: 1px;
            border-style: inset;
            border-color: black;
            background-color: white;
        }

        tr.lineTableTr {
            border-width: 1px;
            padding: 0px;
            border-style: inset;
            border-color: black;
            background-color: white;
            -moz-border-radius: ;
        }

        #lineTableDummyTd {
            border-width: 1px;
            border-color: white;
            padding: 1px;
            border-style: inset;
            border-color: black;
            background-color: white;
        }

        td.lineTableBudgetTd {
            border-width: 2px;
            border-spacing: 0px;
            padding: 1px;
            border-style: inset;
            border-color: black;
            background-color: white;
            -moz-border-radius: ;
        }

        #notesTable {
            border-width: 2px;
            border-spacing: ;
            border-style: inset;
            border-color: black;
            border-collapse: collapse;
            background-color:
        }

        #notesTableTd {
            border-width: 0px;
            border-spacing: ;
            border-style: inset;
            border-color: black;
            border-collapse: collapse;
            background-color:
        }

        table {
            border-spacing: 0px;
        }

        #budgetContainerTable {
            border-width: 0px;
            border-spacing: 0px;
            border-style: inset;
            border-color: black;
            border-collapse: collapse;
            background-color: ;
        }

        td {
            border-color: gray;
        }
.Estilo2 {
    font-size: 13px;
    font-weight: bold;
}
    .Estilo3 {font-size: 13px}
    .style6 {
    font-size: 14px;
    font-weight: bold;
}
    .style7 {font-size: 14px}
    .style9 {font-size: 17px; font-weight: bold; }
.style11 {font-size: 17px}
    .style14 {font-size: 16px}
.style20 {font-size: 21px; font-weight: bold; }
.style24 {font-size: 27px; font-weight: bold; }
</style>
</head>
<body>
    <?php foreach ($orders as $order): ?>


    <?php
    // Sipariş bilgileri
    $odemeSarti = floatval($order['odeme_sarti']); // KDV dahil fiyat
    
    // Barkod basıldı olarak işaretlenecek siparişlerin ID'sini topluyoruz
    $basilanSiparisler[] = intval($order['id']);

    // Hesaplamalar
    $birimFiyat = round($odemeSarti / 1.10, 2); // KDV hariç birim fiyat
    $kdvTutar = round($birimFiyat * 0.10, 2); // Hesaplanan KDV
    $toplamTutar = round($birimFiyat, 2); // Toplam Tutar (kontrol için)
    ?>



        

	
	
    <div class="coklu_fatura">
    <table id="customerPartyTable" align="left" border="0">
                <tbody>
                <tr style="height:71px; ">
                    <td>
                        <br><br>
                        <!-- <hr> -->
                        <table align="center" border="0">
                            <tbody>
                            <tr>
                                <td style="width:469px; " align="left"><span style="font-weight:bold; ">SAYIN</span></td>
                            </tr>
                            <tr>
                              <td style="width:469px; " align="left"><h1 class="style7">SEMREBUTİK - <?php
                                $order['hangikargo'] = htmlspecialchars($order['hangikargo']);

                                if ($order['hangikargo'] === "Yunus Emre - PTT") {
                                    echo "Yunus Emre AYDIN";
                                } elseif ($order['hangikargo'] === "Sevim Aydın - PTT") {
                                    echo "Sevim AYDIN";
                                } else {
                                    echo "Tanımlanmamış şirket ismi!";
                                }
                                ?>

                                </h1>
                                <span class="style7"><br>
                                </span></td>
                            </tr>
                            <tr>
                                <td style="width:469px; " align="left"><span class="style7"><strong>Baraj Mh. Kırçiceği Cd. 1A+2A Blk. No: 104/A</strong></span></td>
                            </tr>
                            <tr align="left">
                                <td><span class="style7"><strong>Web Sitesi: www.semrebutik.com</strong></span></td>
                            </tr>
                            <tr align="left">
                                <td><span class="style7"><strong>Vergi Dairesi: DÜDEN</strong></span></td>
                            </tr>
                            <tr align="left">
                                <td><span class="style7"><strong>VKN: </strong></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table><br>
            </td>

            <td width="50%" align="right" valign="middle" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
                <!-- Fatura logosu -->
                <img style="width:91px;" alt="Fatura Logo" src="faturalogo.png">
                <!-- GİB logosu -->
                <img style="width:91px;" alt="GİB Logo" src="giblogo.jpeg">
                <h1 align="right" style="margin-left: 10px; font-weight:bold;">e-FATURA</h1>
                <table align="right" style="text-align:center" border="0" width="100%">
                    <tbody>
                        <tr align="right">
                            <td align="right">
                                <div style="font-size:1.2em;"></div><br></td>
                        </tr>
                        <tr align="right">
                            <td align="right"><span class="style6"> - Semrebutik </span></td>
                        </tr>
                        <tr align="right">
                            <td align="right"><span class="style6">Baraj Mh. Kırçiceği Cd. 1A+2A Blk. No: 104/A  <br>
                            07320 KEPEZ / Antalya</span></td>
                        </tr>
                        <tr align="right">
                            <td align="right"><span class="style6">VD: DÜDEN</span></td>
                        </tr>
                        <tr align="right">
                            <td><span class="style6">VN: </span></td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="100%">

</td>
</tr>
</tbody>
</table>
<table width="800" style="margin-top:0px">
<tbody>

</tbody></table>
<table width="800" style="margin-top:5px">
<tbody>

</tbody></table>
<table style="border-color:blue; " border="0" cellspacing="0px" width="800" cellpadding="0px">
<tbody>
<tr align="left">
<td align="left" valign="top" width="50%" id="ettnTable"><span style="font-weight:bold;color:black;font-size:13px ">Müşteri Bilgileri</span></td>

<td align="right" valign="top" width="50%" id="ettnTable"><span style="font-weight:bold;color:black;font-size:14px"><span class="style14">Alıcı : <?= htmlspecialchars($order['musteri_ismi']) ?></span>&nbsp;</span></td>
</tr>  

</tbody>
</table>
<table style="border-color:black;border:none;margin-top:5px" border="1" cellspacing="0px" width="800" cellpadding="0px">
 <tbody><tr>
    <td width="50%" style="border-color:black;">
        <span style="font-weight:bold;color:black">Fatura Bilgileri</span>                </td>
    <td width="50%" style="border-color:black;">
        <span style="font-weight:bold;color:black">Teslimat Bilgileri</span>                </td>
</tr>
<tr>
    <td width="50%" style="border-color:black;">
        <span style="font-size: 17px"><strong><?= htmlspecialchars($order['musteri_ismi']) ?></strong></span><span class="style9"><br>
        <?= htmlspecialchars($order['musteri_adresi']) ?> <br>
         TÜRKİYE<br>
        Müşteri No : <?= htmlspecialchars($order['musteri_telefonu']) ?><br>
        VKNO : 11111111111<br>
        </span><span class="style11"><br>              
        </span></td>
    <td width="50%" style="border-color:black;">
        <span class="style11" style="font-weight:bold;color:black">Teslimat Bilgileri</span>
        <span class="style11" style="font-weight:bold;color:black"><?= htmlspecialchars($order['musteri_ismi']) ?></span><span class="style11"><strong><br>
        <span style="color:black"><?= htmlspecialchars($order['musteri_adresi']) ?></span><br>
        <span style="color:black">TÜRKİYE</span><br>
        <span style="color:black">Müşteri No : <?= htmlspecialchars($order['musteri_telefonu']) ?></span><br>
        <span style="color:black">VKNO : 11111111111</span></strong></span></td>
</tr>
</tbody></table>
<table style="border-color:blue;margin-top:5px " border="0" cellspacing="0px" width="800" cellpadding="0px">
<tbody>

<tr align="left">
<td align="left" valign="top" width="50%" id="ettnTable"><span style="font-weight:bold;color:black ">Sipariş Ürünleri</span></td>

</tr>
</tbody>
</table>

<table border="1" id="lineTable" width="800" style="margin-top:5px">
<tbody>
<tr class="lineTableTr">
<td class="lineTableTd" style="width:3%" align="center"><span class="style11" style="font-weight:bold;">No</span></td>
<td class="lineTableTd" style="width:20%" align="center"><span class="style11" style="font-weight:bold;">Malzeme / Hizmet Açıklaması</span></td>
<td class="lineTableTd" style="width: 2.4%;" align="center"><span class="style11" style="font-weight:bold;">KDV</span></td>
<td class="lineTableTd" style="width: 4%;" align="center"><span class="style11" style="font-weight:bold;">Miktar</span></td>
<td class="lineTableTd" style="width: 7%;" align="center"><span class="style11" style="font-weight:bold;">Birim Fiyat</span></td>
<td class="lineTableTd" style="width: 7%;" align="center"><span class="style11" style="font-weight:bold;">Mal Hizmet Tutarı</span></td>
</tr>

<tr class="lineTableTr">
<td class="lineTableTd"><span class="style11"><strong>&nbsp;1</strong></span></td>
<td class="lineTableTd"><span class="style11"><strong>&nbsp;Bayan Giyim</strong></span></td>
<td align="right" class="lineTableTd"><span class="style11"><strong>&nbsp;%8</strong></span></td>
<td align="right" class="lineTableTd"><span class="style11"><strong>&nbsp;1 Adet</strong></span></td>
<td align="right" class="lineTableTd"><span class="style11"><strong><?php echo $birimFiyat; ?> ₺</strong></span></td>
<td align="right" class="lineTableTd"><span class="style11"><strong><?php echo $toplamTutar; ?> ₺</strong></span></td>
</tr>
</tbody>
</table>
<table id="budgetContainerTable" width="800px" style="margin-top:10px">
<tbody>

<tr align="left">
<td class="lineTableBudgetTd" align="left" width="130"><span class="style7" style="font-weight:bold; ">Senaryo : </span></td>
<td align="left" class="lineTableBudgetTd" style="width:140px; "><span class="style7"><strong>EARSIVFATURA</strong></span></td>
<td>
</td><td class="lineTableBudgetTd" width="240" align="left"><span class="style11" style="font-weight:bold; ">Mal Hizmet Toplam Tutarı</span></td>
<td align="left" class="lineTableBudgetTd" style="width:82px; "><span class="style11"><strong><?php echo $toplamTutar + $kdvTutar; ?> ₺</strong></span></td>

</tr>
<tr align="left">
<td class="lineTableBudgetTd" width="130" align="left"><span class="style7" style="font-weight:bold; ">Fatura Tipi : </span></td>
<td align="left" class="lineTableBudgetTd" style="width:140px; "><span class="style7"><strong> SATIŞ</strong></span></td>
<td>
</td><td class="lineTableBudgetTd" width="240" align="left"><span class="style11" style="font-weight:bold; ">Hesaplanan GERÇEK USULDE KDV(%10)</span></td>
<td align="left" class="lineTableBudgetTd" style="width:82px; "><span class="style11"><strong><?php echo $kdvTutar; ?> ₺</strong></span></td>


</tr>
<tr align="left">
<td class="lineTableBudgetTd" width="130" align="left"><span class="style7" style="font-weight:bold; ">Fatura No :</span></td>
<td class="lineTableBudgetTd" style="width:140px " align="left"><h2 class="style7" style="color: black;"><?= htmlspecialchars($order['parasut_fatura_numarasi']) ?></h2> </td>
</tr>
<tr align="left">
<td class="lineTableBudgetTd" width="130" align="left"><span class="style7" style="font-weight:bold; ">Düzenleme Tarihi : </span></td>
<td class="lineTableBudgetTd" style="width:140px; " align="left"><span class="style7"><strong><?php $siparis_tarihi = htmlspecialchars($order['siparis_tarihi']); $formatted_date = date("d-m-Y", strtotime($siparis_tarihi)); echo $formatted_date; ?></strong></span></td>
<td>
</td><td class="lineTableBudgetTd" width="240" align="left"><span class="style11" style="font-weight:bold; ">Vergiler Dahil Toplam Tutar</span></td>
<td align="left" class="lineTableBudgetTd" style="width:82px; "><span class="style11"><strong>
        <?php
echo sayiyiYaziyaCevir(htmlspecialchars($order['odeme_sarti']));
?>
        TL'dir.    </strong></span></td>

</tr>
</tbody>
</table>
<br>
<table width="802" border="1">
<tbody><tr>
<td width="126"><span class="style20">Sayfa</span></td>
<td width="97"><span class="style20">Ödeme Türü </span></td>
<td width="90"><span class="style20">Rakam</span></td>
<td width="461"><span class="style20">İl / İlçe </span></td>
</tr>
<tr>
<td><span class="style20"><font color="black" face="tahoma"><?= htmlspecialchars($order['hangisayfa']) ?></font></span></td>
<td><span class="style20"><font face="tahoma" color="black">ŞARTLI ÖDEME</font></span></td>
<td><span class="style20"><font face="tahoma" color="black"><?= htmlspecialchars($order['odeme_sarti']) ?>₺</font></span></td>
<td><span class="style20"><font face="tahoma" color="black"><?= htmlspecialchars($order['musteri_il']) . ' / ' . htmlspecialchars($order['musteri_ilce']) ?></font></span></td>
</tr>
<tr>
<td><span class="style24">Ürünler : </span></td>
<td colspan="3"><span class="style24"><font color="black" face="tahoma">
<?= htmlspecialchars($order['urunler']) ?>	</font></span></td>
<center style="margin-top: 20px;">


</center>

</tr>
</tbody></table>

<br>
<br>
        <center>
            <?php
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($order['kargo_barkodu'], $generator::TYPE_CODE_128);
            $barcodeBase64 = base64_encode($barcode);
            ?>
            <img src="data:image/png;base64,<?= $barcodeBase64 ?>" alt="Barkod" style="width: 400px; height: 100px;">
            <p></p>
			
			
		
 
 <span class="style24"><font color="black" face="tahoma">
	<center><?= htmlspecialchars($order['kargo_barkodu']) ?>	</font></span>
			
			
        </center>
    </div>
    <?php endforeach; ?>
</body>
</html>

<?php
// HTML çıktısını yazdır
$output = ob_get_clean();
echo $output;

// Barkod basım durumunu güncelle
if (!empty($basilanSiparisler)) {
    $placeholders = implode(',', array_fill(0, count($basilanSiparisler), '?'));
    $updateSql = "UPDATE siparisler SET barkod_basilma_durumu = 'Basılmış' WHERE id IN ($placeholders)";
    $db->query($updateSql, $basilanSiparisler, str_repeat('i', count($basilanSiparisler)));
}
?>
