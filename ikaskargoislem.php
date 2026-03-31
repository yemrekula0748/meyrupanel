<?php
function ikaskargoIslem($username, $kargo) {
    // cURL fonksiyonu
    function sendCurlRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    if ($username === "Sevim Aydın - PTT" && $kargo === "Ödeme Şartlı") {
        sendCurlRequest("https://meyrupanel.com.tr/sevimaydinpttsartliodeme.php");
        sendCurlRequest("https://meyrupanel.com.tr/sevimaydin_musteriden_faturaya.php");
    } elseif ($username === "Sevim Aydın - PTT" && $kargo === "Bedelsiz") {
        sendCurlRequest("https://meyrupanel.com.tr/sevimaydinpttbedelsizodeme.php");
    } elseif ($username === "MeyruKids" && $kargo === "Ödeme Şartlı") {
        sendCurlRequest("https://meyrupanel.com.tr/yunusemrepttsartliodeme.php");
        sendCurlRequest("https://meyrupanel.com.tr/yunusemre_musteriden_faturaya.php");
    } elseif ($username === "MeyruKids" && $kargo === "Bedelsiz") {
        sendCurlRequest("https://meyrupanel.com.tr/yunusemrepttbedelsizodeme.php");
    } else {
        echo "Eşleşen bir işlem bulunamadı. Kullanıcı: {$username} / Kargo: {$kargo}\n";
    }
}
?>