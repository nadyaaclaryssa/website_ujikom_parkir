
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/user_hapus.php
// -> Tujuan Spesifik: Modul/komponen fungsional spesifik aplikasi Smart Parking System.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
// Pastikan koneksi disertakan di awal agar variabel $koneksi terbaca
// [SINTAKS PHP]: include | Menyertakan file konfigurasi koneksi ke server MySQL Database agar tabel bisa dibaca/ditulis
include '../../config/koneksi.php';

// Proteksi: Cek apakah session role ada dan apakah dia admin
if(!isset($_SESSION['role']) || strtolower($_SESSION['role']) != "admin"){
    // [SINTAKS PHP]: echo JS | Men-cetak sintaks javascript HTML untuk memunculkan pesan (Alert Pop-up) interaktif pada browser
echo "<script>alert('Akses Ditolak!'); window.location='../../index.php';</script>";
    exit;
}

// Ambil ID dari URL
$id = // [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
$_GET['id'];

// Proses hapus ke database
$query = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user = '$id'");

if($query){
    // Pastikan tujuannya kembali ke halaman tabel user kamu
    // [SINTAKS PHP]: echo JS | Men-cetak sintaks javascript HTML untuk memunculkan pesan (Alert Pop-up) interaktif pada browser
echo "<script>alert('User berhasil dibasmi!'); window.location='user_daftar.php';</script>";
} else {
    echo "Gagal membasmi: " . mysqli_error($koneksi);
}
?>

