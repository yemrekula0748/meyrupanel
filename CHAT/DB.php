<?php
$servername = "localhost";
$username = "chatapp";
$password = "11111111";
$dbname = "chatapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
