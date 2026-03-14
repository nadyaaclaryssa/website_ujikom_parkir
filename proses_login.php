<?php
session_start();
include 'config/koneksi.php'; 
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $pass_md5 = md5($password);

    // Query cek user
    $query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username' AND password='$pass_md5'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        
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
        
        mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu) VALUES ('$id_user', '$aktivitas', '$waktu')");

        // Arahkan sesuai role
        if($data['role'] == 'admin'){
            header("location:admin/dashboard.php");
        } else if($data['role'] == 'petugas'){
            header("location:petugas/dashboard.php");
        } else {
            header("location:owner/dashboard.php");
        }
        exit;
    } else {
        echo "<script>alert('Gagal! Username atau Password Salah.'); window.location='index.php';</script>";
    }
}
?>