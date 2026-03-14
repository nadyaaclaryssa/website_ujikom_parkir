
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: proses_login.php
// -> Tujuan Spesifik: Memproses input kredensial, validasi query tabel tb_user, menyimpan sesi, catat log aktivitas, routing otomatis.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
// [SINTAKS PHP]: include | Memanggil file koneksi yg berisi variabel host, user, pw, dan info database
include '../config/koneksi.php'; 
// [SINTAKS PHP]: Set default timezone | Mengatur zona waktu bawaan sistem fungsi jam PHP ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

if (isset(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['username']);
    $password = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['password']);
    $pass_md5 = // [SINTAKS PHP]: md5() | Enkripsi hashing string satu arah menggunakan metode standar MD5 Message-Digest Algorithm
md5($password);

    // Query cek user
    $query = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username' AND password='$pass_md5'");
    $cek = // [SINTAKS PHP]: mysqli_num_rows() | Menghitung dan mendapatkan jumlah total baris/records dari hasil eksekusi query SELECT
mysqli_num_rows($query);

    if ($cek > 0) {
        $data = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / Mengambil satu baris data array dari hasil query berdasarkan NAma Kolom / Index String
mysqli_fetch_assoc($query);
        
        // Simpan session
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        
        // FIX: Tambahkan baris ini supaya error di Dashboard Petugas hilang!
        $_SESSION['nama'] = $data['nama_lengkap']; 
        
        $_SESSION['role'] = $data['role'];

        // --- CATAT LOG ---
        $id_user = $data['id_user'];
        $waktu = date("Y-m-d H:i:s");
        $aktivitas = "Berhasil Login ke Sistem";
        
        // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ('$id_user', '$aktivitas', '$waktu')");

        // Arahkan sesuai role
        if($data['role'] == 'admin'){
            // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../view/admin/dashboard.php");
        } else if($data['role'] == 'petugas'){
            // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../view/petugas/dashboard.php");
        } else {
            // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../view/owner/dashboard.php");
        }
        exit;
    } else {
        // [SINTAKS PHP]: echo JS | Men-cetak sintaks javascript HTML untuk memunculkan pesan (Alert Pop-up) interaktif pada browser
echo "<script>alert('Gagal! Username atau Password Salah.'); window.location='index.php';</script>";
    }
}
?>

