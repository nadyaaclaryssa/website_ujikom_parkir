<?php
// Sesuaikan nama database menjadi ukk_parkir
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ukk_parkir"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>