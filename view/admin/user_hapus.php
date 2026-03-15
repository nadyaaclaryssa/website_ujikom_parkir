<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/user_hapus.php
// -> Tujuan Spesifik: Modul/komponen file tersembunyi non-visual murni logic Action eksekutor pemusnahan (Penghapusan/DELETE MySQL) Data Pengguna Eksisting.
// -> Penjelasan file terisolasi yg bertindak cuma sbg pengeksekusi (tanpa tampilan html design apasaja, cuma mikir dan langsung redirect mindahin halaman lagi secepat kilat hantu)
// ======================================

// [SINTAKS PHP]: session_start() | Mengaktifkan Sesor Penjejak Akses Jejak Token Keamanan User/Admin (Session)
session_start();

// [SINTAKS PHP]: include | Mengimport Skrip Penghubung Saluran Pipa Koneksi Ke Database Server (mutlak dibutuhkan krn mau nembak Hapus/Delete Data kan) 
include '../../config/koneksi.php';

// [SINTAKS PHP]: Penyeleksian Berlapis Privilese & Eksistensi Session | Memeriksa Ganda (Memastikan Status Sesi Ga Null !isset) DAN Memastikan Role-Nya Bukan Sembarangan Kacang tapi emang Administrator yg udh dilowecase huruf Cilik.
if(!isset($_SESSION['role']) || strtolower($_SESSION['role']) != "admin"){
    
    // [SINTAKS PHP]: echo JS | Kalau Terdeteksi Mnyusup/Session Hilang tiba2 saat eksekusi, Hukum pake Javascript Popup Tulisan "Akses Ditolak" dan lempar terbang jauh Out Dari Zona Admin Panel Area balik lagi ke Form awal Index  Login.
    echo "<script>alert('Akses Ditolak!'); window.location='../../index.php';</script>";
    
    // [SINTAKS PHP]: exit Konstruksi Breakpoint. Mengibarkan Bendera Merah Terminate Koding script dibawah ini JANGAN PERNAH DIJALANKAN MESIN PHP lagi. Stop sampe sini Aja biar ga Tembus (Cegah Eksekusi Tembus Latar Belakang).
    exit;
}

// ==== BLOK LOGIKA: PEMBUNUH(HAPUS/DELETE) AKUN (Action Only) ====

// [SINTAKS PHP]: GET Parameter Catcher | Menangkap Umpan Beban ID Nomor Spesifik Data Unik Sang Pegawai Yang Dititipkan Dalam Barisan Address Link URL (Misal url ujungnya '?id=69', ditarik angka 69 nya jadi Variabel Lokal siap eksekusi hapus)
$id = $_GET['id'];

// [SINTAKS PHP]: Perintah Utama Sql HAPUS Instan DELETE FROM | Memerintahkan Algo Database membumi-hanguskan se-blok Barisan Record utuh (Satu Orang dan Seluruh Identitas Namanya) yang memiliki Nomor Urut Primary Kunci ID_USER yang Sama dengan Catcher Tarikan URL diatas!
$query = mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user = '$id'");

// [SINTAKS PHP]: Logika Penilian (Evaluator Branching If/Else) Apakah Tembakan Sakti Pemusnah Massal diatas Berhasil Dieklesi/Menemukan sasaran nya di Kolom Basis Data ?
if($query){
    
    // [SINTAKS PHP]: echo JS | Kalau sukses dan tak mendapati eror query, Suguhkan Alert Javascript Tanda Kegembiraan Admin dan Refresh Ulang layar memantul balik mundur layar Ke tabel List Data Semula!
    echo "<script>alert('User berhasil dibasmi!'); window.location='user_index.php';</script>"; /* (Mengkoreksi Typo 'user_daftar' jadi 'user_index' secara sadar demi lancar fungsinya) */
} else {
    // [SINTAKS PHP]: Kalau ada malapetaka Eror Constraint Database/Salah Tulis, Munculin Barisan Pesan Kematian String Merah Erorr Asli Output dari Mesin Database (mysqli_error) Sbg Detektif Analisis Buat Teknisi Developer.
    echo "Gagal membasmi: " . mysqli_error($koneksi);
}
?>
