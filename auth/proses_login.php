<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: proses_login.php
// -> Tujuan Spesifik: Memproses input kredensial, melakukan validasi query ke tb_user, menyimpan sesi role, mencatat log audit aktivitas, serta melakukan routing redirector yang ditunjuk per user role
// ======================================

// [SINTAKS PHP]: session_start() | Memulai dan menyalakan Session (sesi) browser untuk pendaftaran memori kredensial ID pengguna secara kontiniu / menetap di server
session_start();

// [SINTAKS PHP]: include | Memanggil file konfigurasi koneksi agar mesin bisa mendengarkan/menjalankan Query syntax-sintaks SQL (Variabel $koneksi hadir)
include '../config/koneksi.php'; 

// [SINTAKS PHP]: date_default_timezone_set | Mengatur zona waktu bawaan (Default behavior) fungsi Timestamp jam mutlak PHP ke wilayah waktu spesifik (WIB Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// [SINTAKS PHP]: Percabangan if (isset()) | Logika keamanan. Mencegah user menginjek URL ini langsung secara Direct, mengecek dan memastikan ada aksi Submit array $_POST 'login' terkirim valid
if (isset($_POST['login'])) {
    
    // [SINTAKS PHP]: mysqli_real_escape_string() | Fungsionalitas keamanan ganda (Anti SQL-Injection), membersihkan paksa (Filtering out) setiap kutip/karakter iseng hacker di kolom Username form
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    
    // [SINTAKS PHP]: mysqli_real_escape_string() | Melindungi string inputan password yang ditangkap Form (Key: 'password') dari suntikan meta HTML/SQL berbahaya
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // [SINTAKS PHP]: md5() | Enkripsi hashing Algoritme satu jalur Message-Digest (MD5) agar teks mentah password teracak acak, memprotek integritas (Match checking against Database)
    $pass_md5 = md5($password);

    // [SINTAKS PHP]: mysqli_query() eksekusi SQL | Mengotentikasi & menyeleksi 1 set record spesifik didalam entitas `tb_user`, di mana value kombinasi "Username AND Password(MD5)" bertepatan persis di tabelnya
    $query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username' AND password='$pass_md5'");
    
    // [SINTAKS PHP]: mysqli_num_rows() | Mesin penghitung jumlah deret. Bila ada row hasil match > 0 (Berarti Valid Identitas Cocok), bila = 0 (Berarti Gagal/Tidak terdaftar)
    $cek = mysqli_num_rows($query);

    // [SINTAKS PHP]: Percabangan if() otentikasi keberhasilan log on form
    if ($cek > 0) {
        
        // [SINTAKS PHP]: mysqli_fetch_assoc() | Pem-Parsing hasil query database menjadikannya bentuk susunan Object Associative array (key=nama_kolom, val=nilai_field) 
        $data = mysqli_fetch_assoc($query);
        
        // [SINTAKS PHP]: Deklarator SESSION Array Superglobal ($_SESSION) | Menyematkan/menyimpan jejak ID user, kredensial, nama asli (Data Profile Statis) ke memory buffer mesin (Cookie Auth Session Payload)
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        
        // Fix: Duplikasi field array SESSION khusus agar Dashboard Petugas yang membaca variabel $_SESSION['nama'] turut ter-"Panggil" tanpa error Index OOB
        $_SESSION['nama'] = $data['nama_lengkap']; 
        
        // [SINTAKS PHP]: Session Role Bind | Kunci Utama ACL (Access Control List) untuk parameter penyaring visibilitas modul (Admin/Petugas/Owner)
        $_SESSION['role'] = $data['role'];

        // --- SISTEM CATAT AUDIT TRAIL LOG SYSTEM AKTIVITAS ---
        // [SINTAKS PHP]: Variabel Penampung Parameter | Menyusun pilar string statis aktivitas apa yang baru saja diselesaikan 
        $id_user = $data['id_user'];
        // [SINTAKS PHP]: date() format datetime (Y-m-d H:i:s) | Mentranslasi waktu server ke timestamp yang mudah dibaca MySQL Native
        $waktu = date("Y-m-d H:i:s");
        $aktivitas = "Berhasil Login ke Sistem";
        
        // [SINTAKS PHP]: mysqli_query() Eksekusi Insert | Merecord history Log In ke dalam histori tb_log_aktivitas
        mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ('$id_user', '$aktivitas', '$waktu')");

        // [SINTAKS PHP]: Percabangan if else multi kondisional (Routing otomatis per Role/Privilege Level Profile User terkait)
        if($data['role'] == 'admin'){
            // [SINTAKS PHP]: header("location:") | Melemparkan/memindah jendela browser otomatis ke destinasi path url "/view/admin/dashboard.php" layaknya Switchgate Gateway
            header("location:../view/admin/dashboard.php");
        } else if($data['role'] == 'petugas'){
            // [SINTAKS PHP]: header("location:") | Melemparkan URL spesifik UI gerbang Dashboard Oprational Petugas Parkir
            header("location:../view/petugas/dashboard.php");
        } else {
            // [SINTAKS PHP]: header("location:") | Melemparkan URL spesifik Dashboard Owner Eksekutif UI Statistik
            header("location:../view/owner/dashboard.php");
        }
        // [SINTAKS PHP]: exit() | Menutup jalur eksekusi beranda halaman ini. (Protektif anti bypass logika bypasser)
        exit;
    } else {
        // [SINTAKS PHP]: echo JS Pop-Up Alert | Bila num_rows = 0 (Akses login gagal salah ketik akun) memantulkan popup Window JS (Alert Message) lalu lempar balik form 
        echo "<script>alert('Gagal! Username atau Password Salah.'); window.location='index.php';</script>";
    }
}
?>