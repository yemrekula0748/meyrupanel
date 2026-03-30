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
        sendCurlRequest("https://semre.hpanel.com.tr/sevimaydinpttsartliodeme.php");
        sendCurlRequest("https://semre.hpanel.com.tr/sevimaydin_musteriden_faturaya.php");
    } elseif ($username === "Sevim Aydın - PTT" && $kargo === "Bedelsiz") {
        sendCurlRequest("https://semre.hpanel.com.tr/sevimaydinpttbedelsizodeme.php");
    } elseif ($username === "Yunus Emre - PTT" && $kargo === "Ödeme Şartlı") {
        sendCurlRequest("https://semre.hpanel.com.tr/yunusemrepttsartliodeme.php");
        sendCurlRequest("https://semre.hpanel.com.tr/yunusemre_musteriden_faturaya.php");
    } elseif ($username === "Yunus Emre - PTT" && $kargo === "Bedelsiz") {
        sendCurlRequest("https://semre.hpanel.com.tr/yunusemrepttbedelsizodeme.php");
    } else {
        echo "Eşleşen bir işlem bulunamadı. Kullanıcı: {$username} / Kargo: {$kargo}\n";
    }
}
?>