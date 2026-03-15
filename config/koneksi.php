<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: koneksi.php
// -> Tujuan Spesifik: Jembatan penghubung antara source code PHP dengan Database MySQL 'ukk_parkir'
// ======================================

// [SINTAKS PHP]: Deklarasi Variabel | Menyimpan konfigurasi IP Host lokal (XAMPP/MAMP)
$host = "127.0.0.1"; 

// [SINTAKS PHP]: Deklarasi Variabel | Menyimpan kredensial username default database lokal
$user = "root";

// [SINTAKS PHP]: Deklarasi Variabel | Menyimpan kredensial password default (biasanya kosong untuk XAMPP)
$pass = "";         

// [SINTAKS PHP]: Deklarasi Variabel | Mendefinisikan nama database aplikasi yang aktif digunakan
$db   = "ukk_parkir"; 

// [SINTAKS PHP]: mysqli_connect() | Fungsi inti pembentuk jembatan koneksi ke server database MySQL menggunakan 4 parameter wajib
$koneksi = mysqli_connect($host, $user, $pass, $db);

// [SINTAKS PHP]: Percabangan if (!...) | Mengecek/memvalidasi apakah fungsi mysqli_connect gagal mengeksekusi koneksi
if (!$koneksi) {
    // [SINTAKS PHP]: die() | Menghentikan paksa seluruh proses render file PHP jika gagal, memunculkan pesan error sistem
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>