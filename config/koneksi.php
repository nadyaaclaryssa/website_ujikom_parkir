
<?php
$host = "127.0.0.1"; 
$user = "root";
$pass = "";         
$db   = "ukk_parkir"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>