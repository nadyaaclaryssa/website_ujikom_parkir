<?php 
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: logout.php
// -> Tujuan Spesifik: Melakukan Terminasi log keluar aplikasi, membersihkan buffer autentikasi dan melempar pengguna paksa keluar dari session yang aktif
// ======================================

// [SINTAKS PHP]: session_start() | Memulai / Menarik ID sesi (session) browser saat ini sebelum melakukan interupsi pemberhentian session  sebagai penanda memori aktif
session_start();

// [SINTAKS PHP]: session_destroy() | Menghancurkan, menghapus paksa memori lokal, dan membersihkan semua data kredensial $_SESSION dari pelbagai Cookie Storage server sehingga Log out utuh menyeluruh tanpa tinggalkan celah Backdoor
session_destroy();

// [SINTAKS PHP]: header("location:...") | Fungsi penutup Redirector, melakukan pengalihan paksa jendela tab pengguna ke file index beranda Login page otentikasi
header("location:index.php");
?>
