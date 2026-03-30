<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<?php



$client = new SoapClient("https://pttws.ptt.gov.tr/PttVeriYukleme/services/Sorgu?wsdl");



$params = array();
$kabulEkle2 = array();

$kabulEkle2["kullanici"] = 'PttWs';
$kabulEkle2["Sifre"] = 'J3fbefKlzi5oBOfMsWQ';
$kabulEkle2["musteriId"] = '703083141';
$kabulEkle2["dosyaAdi"] = date('Ymd-His-');
$kabulEkle2["gonderiTur"] = 'KARGO';
$kabulEkle2["gonderiTip"] = 'NORMAL';

/*

    $kabulEkle2->aAdres("deneme");
    $kabulEkle2->agirlik("1");
    $kabulEkle2->aliciAdi("şamil");
    $kabulEkle2->aliciIlAdi("antep");
    $kabulEkle2->aliciIlceAdi("araban");
    $kabulEkle2->aliciSms("0212");
    $kabulEkle2->ekle();

*/
$result = $client("kabulEkle2", $kabulEkle2)->kabulEkle2Response;

if( is_array($result) && $result['hataKodu'] == 1 ){

    print_r($result);

    foreach ($result['dongu'] as $barcode){
        // $barcode
    }

    return true;

}else{

    print_r($result['aciklama']);

    return false;
}

?>

<body>


</body>
</html>