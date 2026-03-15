<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: index.php
// -> Tujuan Spesifik: Pintu gerbang utama root direktori yang menjadi pelempar otomatis (Redirector) ke modul Autentikasi Login (Login Page)
// ======================================

// [SINTAKS PHP]: header("location:...") | Fungsi untuk melakukan Redirect (pengalihan otomatis) pengguna yang membuka url web "/" ke "/auth/index.php" sebelum merender apapun
header("location:auth/index.php");

// [SINTAKS PHP]: exit | Menghentikan paksa proses script PHP setelah fungsi header terpenuhi agar script di bawahnya tidak dieksekusi secara rahasia
exit;
?>
