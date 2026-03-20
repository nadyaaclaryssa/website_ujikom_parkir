<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/tarif_edit.php
// -> Tujuan Spesifik: Modul/komponen Form Tunggal Meng-Update Satu per satu Record Tarif Parkir (Metode Edit/Update) Dari Tombol tabel "Ubah Tarif".
// ======================================

// [SINTAKS PHP]: session_start() | Memakai memori session PHP buat memvalidasi apakah ini user beneran atau hantu (penyusup)
session_start();

// [SINTAKS PHP]: Filter Privilege Lapis Baja | Hanya admin murni yang boleh nembus ini! 
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: Lempar buang ke form login
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Panggil Koneksi Bridging Ke Master Node Server MySQL
include '../../config/koneksi.php';

// ==== BLOK LOGIKA: PERSIAPAN MENAMPILKAN DATA LAMA ALIAS NILAI EKSISTING SAAT INI (PRE-FILL FORM) ====

// [SINTAKS PHP]: GET Param Hook | Menangkap Bungkusan ID Unik yang dititipkan lewat Batang Address Bar URL (?id=29) dari klikan tombol Edit di File Terdahulu 
$id = $_GET['id'];

// [SINTAKS PHP]: SELECT 1 Row SQL | Memerintahkan Robot Database Nyomot 1 Data Ekslusif spesifik cuma punya si Nomor ID itu doang. 
$data = mysqli_query($koneksi, "SELECT * FROM tb_tarif WHERE id_tarif='$id'");

// [SINTAKS PHP]: Mem-parsing/Membongkar Lembaran Kertas hasil Kueri DB di atas menjadi Data Array 1 Lapis (Karena Asumsinya 1 ID Pasti Cuma Dapet 1 Baris)
$t = mysqli_fetch_array($data);

// ==== BLOK LOGIKA: PENYIMPANAN DATA BARU YANG DI UBAH YBS (Proses Update Submit) ====

// [SINTAKS PHP]: Trigger Cek Pelatuk | Pengecekan ada event tombol Submit Form Update ditekan dari browser atau enggak.  
if(isset($_POST['update'])){
    
    // [SINTAKS PHP]: Narik nilai Angka Harga Baru yang barusan diketik Ulang pake keyboard sama Si Admin.
    $tarif = $_POST['tarif'];
    
    // [SINTAKS PHP]: Command Eksekusi SQL UPDATE! | Intstruksi mengubah (SET) Isi Kantong kolom Kolom tarif_per_jam lama ditimban Value Tarif Harga Baru Murni! SYARAT MUTLAK: Tujukan PADA BARIS ('WHERE id') YG TEPAT! Jgn Lpa Where nya, kalo engga semua tarif 1 tabel kesimpen harga yg sama rata!
    mysqli_query($koneksi, "UPDATE tb_tarif SET tarif_per_jam='$tarif' WHERE id_tarif='$id'");
    
    // [SINTAKS PHP]: echo Javascript Tag | Membaurkan kode tag Script bawaan Front End JS dari belakang mesin PHP Ke Muka Browser untuk menyugestikan Kotak Jendela Pop Up Sukses Notifikasi (Alert JS) lalu JS mengarahkan pindah jendela lokasi ke menu sebelumnya. 
    echo "<script>alert('Tarif Berhasil Diupdate!'); window.location='tarif_index.php';</script>";
}
?>

<!-- [SINTAKS HTML]: Templat DOM HTML Web 5 Halaman Laporan Web Aplikasi -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tarif - Hogwarts Admin</title>
    <!-- [SINTAKS HTML]: Font External Injector Library Google Typeface API -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* [SINTAKS CSS]: Mengamankan Skala box size Hitungan Border Padding  */
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body { margin: 0; display: flex; background: #f1f5f9; color: #0f172a; }
        
        /* [SINTAKS CSS]: Sidebar Hogwarts Gryfindor Theme CSS Fixed Posture Tinggi layang */
        .sidebar { width: 260px; height: 100vh; background: #8f3434; color: white; position: fixed; padding: 20px 0; }
        .sidebar-header { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        
        /* [SINTAKS CSS]: Merubah Logo Jpeg yg tadinya kotak Kaku tajam menjadi bundar bulet sempurna kayak koin pakai tipuan Border Radius 50% Lingkaran */
        .sidebar-header img { width: 60px; border-radius: 50%; margin-bottom: 10px; }
        .sidebar a { display: block; color: rgba(255,255,255,0.8); padding: 14px 25px; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); }
        .sidebar a.active { background: #782626; border-left: 4px solid white; font-weight: 600; }

        /* [SINTAKS CSS]: Celah sisa Lapang Kosong 100% dari lebar layar viewport Dikurangin Ukuran Sidebar Absolute nya  */
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        
        /* [SINTAKS CSS]: Gaya Form Kotak Box Putih Di Tengah dengan bayangan Soft Halus */
        .content-card { 
            background: white; padding: 30px; border-radius: 20px; border: 1px solid #e2e8f0; 
            max-width: 500px; /* Batasan Supaya boxnya ngga ngelebar gepeng parah menuhin monitor gede*/
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
        }
        
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
        .form-group label { font-size: 13px; font-weight: 700; color: #475569; }
        .form-group input { padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f1f5f9; outline: none; font-size: 14px; }
        .form-group input:focus { border-color: #2563eb; background: white; }

        .btn-update { background: #2563eb; color: white; padding: 12px 20px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-update:hover { background: #1d4ed8; }
        .btn-batal { display: block; text-align: center; margin-top: 15px; color: #475569; text-decoration: none; font-size: 14px; }
        .btn-batal:hover { color: #0f172a; }
    </style>
</head>
<body>
    <!-- [SINTAKS HTML]: Navigasi Laci Vertical -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../../../hogwarts2.jpg" alt="Logo">
            <h2>Hogwarts Admin</h2>
        </div>
        <a href="dashboard.php">Dashboard</a>
        <a href="kelola_user.php">Data User</a>
        <!-- [SINTAKS HTML]: Menjelaskan lokasi saat Ini Berada. -->
        <a href="tarif_parkir.php" class="active">Data Tarif</a>
        <a href="area_parkir.php">Data Area</a>
        <a href="log_aktivitas.php">Log Aktivitas</a>
        <a href="../../auth/logout.php" style="margin-top:auto; color:#ffb1b1;">Logout</a>
    </div>

    <!-- [SINTAKS HTML]: Lapangan Render Kanan -->
    <div class="main-content">
        <h1 style="margin: 0 0 30px 0; font-size: 28px;">Update Tarif</h1>
        
        <!-- [SINTAKS HTML]: Pembungkus Kardus Putih -->
        <div class="content-card">
            <!-- [SINTAKS PHP]: UcFIrst() Modifikator String memancarkan Output Text Judul Form Dinamis merujuk ke Value Jenis Kendaraan (Misal: Edit Tarif Truk, Edit Tarif Pesawat ) -->
            <h3 style="margin-top:0; color:#1e293b;">Edit Tarif: <?= ucfirst($t['jenis_kendaraan']); ?></h3>
            
            <!-- [SINTAKS HTML]: Form POST - Cara paling aman ngirim Paket data Perubahan (Biar Angka Duitnya kagak Bocor di Link URL Atas Browser Kyk Metod GET Biasa)  -->
            <form method="POST">
                
                <!-- [SINTAKS HTML]: Ruang Input Teks  -->
                <div class="form-group">
                    <label>Tarif per Jam (Rp)</label>
                    <!-- [SINTAKS HTML/PHP]: Type Number Mengharamkan Pengetikan Huruf Abjad/Karakter Simbol untuk mencegah Database Crash!! 
                         Value Pre-Fill : Mengisi kembali kolom kotak inpuut text kosong  dengan Nominal uang yang SEDANG EKSIS DI DATABASE SAAT INI (Echoing Data Fetch T array) Supaya Admin liat patokan harganya brp skrg. 
                    -->
                    <input type="number" name="tarif" value="<?= $t['tarif_per_jam']; ?>" required>
                </div>
                
                <!-- [SINTAKS HTML]: Triger Tombol Submit Menggedor Aksi PHP di atas -->
                <button type="submit" name="update" class="btn-update">Simpan Perubahan</button>
                
                <!-- [SINTAKS HTML]: Pintu Darurat Kembali Tanpa Menyentuh/Merubah Data sedikitpun -->
                <a href="tarif_parkir.php" class="btn-batal">Kembali ke Daftar Tarif</a>
            </form>

        </div>
    </div>
</body>
</html>
