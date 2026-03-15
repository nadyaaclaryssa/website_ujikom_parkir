<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/kelola_user.php
// -> Tujuan Spesifik: Halaman Form & Tabel CRUD terintegrasi fungsi insert & delete dalam satu file untuk Data Hak Akses Staff (Petugas, Admin, Owner).
// ======================================

// [SINTAKS PHP]: session_start() | Melanjutkan State Sesi Autentikasi untuk menjaga sesi Admin terjaga
session_start();

// [SINTAKS PHP]: Validasi Keamanan Lapis Peran | Mencegah penyusup peran bawah masuk laman Admin.
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: header location | Tendang balik ke form login
    header("location:../../auth/index.php"); 
    // [SINTAKS PHP]: exit | Terminasi script kebawahnya
    exit; 
}

// [SINTAKS PHP]: include | Tarikan file konektor MySQL
include '../../config/koneksi.php';

// [SINTAKS PHP]: Query Aglomerasi Visual Sidebar | Menghitung kumulatif kendaraan terpakir (Dipakai untuk Bar loading UI meteran kapasitas di kiri)
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;

// [SINTAKS PHP]: Variabel Peringatan Kosong | Disiapkan untuk melempar teks Alert Notifikasi kalau pendaftaran gagal
$error_msg = "";

// ==== BLOK LOGIKA: TAMBAH (CREATE) USER BARU ====
// [SINTAKS PHP]: if (isset($_POST[])) | Listener Event yang Menunggu Tembakan Data Formulir POST dengan nama tombol submit "simpan"
if(isset($_POST['simpan'])){
    
    // [SINTAKS PHP]: mysqli_real_escape_string & trim() | Gabungan Fungsi Sanitasi data (Membersihkan inputan spasi nakal di ujung teks dan karakter bahaya kutip SQL Injection).
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $user = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $pass = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    
    // Enkripsi opsional: Harusnya md5($pass), namun sementara plain aja dulu biar gampang pas ujikom.
    
    $role = mysqli_real_escape_string($koneksi, $_POST['role']); 
    
    // [SINTAKS PHP]: Query Cek Duplikat | Mencocokan nama akun yg mau didaftarkan dengan isi tabel user aktual The Database 
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$user'");
    
    // [SINTAKS PHP]: Percabangan Validasi Logical | Evaluasi Hasil Num Rows (Banyaknya baris ditemukan) 
    if(mysqli_num_rows($cek) > 0) {
        // [SINTAKS PHP]: Alert text Assignment | Kondisi Akun sudah eksis di Database
        $error_msg = "Username '$user' terdeteksi sudah ada di sistem!"; 
    } else {
        // [SINTAKS PHP]: SQL Insert Command | Formula Menyuntikan Baris Data Baru ke Kolom yg sejajar di tb_user 
        $q = "INSERT INTO tb_user (nama_lengkap, username, password, role) VALUES ('$nama', '$user', '$pass', '$role')";
        
        // [SINTAKS PHP]: mysqli_query Execution | Tarik pelatuk SQL ke Engine MySQL melalui Pipa $koneksi
        $simpan = mysqli_query($koneksi, $q);
        
        // [SINTAKS PHP]: Evaluasi Penentu Kesuksesan (True/False) Action Input 
        if($simpan) {
            // [SINTAKS PHP]: Redirect | Refresh mandiri halaman pasca tersimpan tanpa nyimpan cache browser 
            header("location:kelola_user.php");
            exit;
        } else {
            // [SINTAKS PHP]: Error Tracking Mysqli | Mencetak string error asli terjemahan engline mysql murni di layar UI Admin
            $error_msg = "Gagal simpan! Error: " . mysqli_error($koneksi);
        }
    }
}

// ==== BLOK LOGIKA: HAPUS (DELETE) USER EKSISTING ====
// [SINTAKS PHP]: if (isset($_GET[])) | Listener Action via Rute URL / URL Parameter. Apabila URL ujungnya ketambahan ?hapus=99
if(isset($_GET['hapus'])){
    // [SINTAKS PHP]: Mengamankan Nilai ID yg diselundupkan di URL
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // [SINTAKS PHP]: SQL Komando Hapus Baris Permanen (DELETE FROM)
    mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user='$id'");
    
    // [SINTAKS PHP]: Redirect Pembersih Jejak URL biar bersih kembali normal '....kelola_user.php'
    header("location:kelola_user.php");
    exit;
}
?>

<!-- [SINTAKS HTML]: <!DOCTYPE html> | Menjamin render formasi HTML versi 5 Layout Standar -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parline - Kelola Pengguna</title>
    <!-- [SINTAKS HTML]: <link> Font API Relasi Google Webfont -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: Tumbuan Inline Style Page User Manager -->
    <style>
        /* [SINTAKS CSS]: Konstanta Hexa Code Warna Base Line */
        :root { --primary: #1d4ed8; --grad-1: #e0f2fe; --grad-2: #bae6fd; }
        
        /* [SINTAKS CSS]: HTML Body Lock | Pemaksaan ukuran viewport menekan overflow jejak scroll luar jendela penjelajah */
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%); display: flex; justify-content: center; align-items: center; }
        
        .app-container { width: 96%; height: 94vh; background: white; border-radius: 32px; display: flex; overflow: hidden; box-shadow: 0 40px 100px -20px rgba(0,0,0,0.1); }
        
        /* [SINTAKS CSS]: Sidebar Dashboard Style Menu Frame */
        .sidebar { width: 260px; background: #f1f5f9; padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid rgba(226, 232, 240, 0.8); }
        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .logo-section img { width: 30px; height: 30px; border-radius: 8px; }
        .logo-section h2 { font-size: 18px; margin: 0; color: #0f172a; font-weight: 800; }
        
        /* [SINTAKS CSS]: Konstruksi Nav Tab Links List */
        .nav-menu { flex-grow: 1; }
        .nav-menu a { display: flex; align-items: center; gap: 10px; padding: 10px 18px; text-decoration: none; color: #64748b; font-size: 14px; font-weight: 600; margin-bottom: 5px; border-radius: 12px; transition: 0.3s; }
        .nav-menu a.active { background: #1d4ed8; color: white; box-shadow: 0 8px 15px -5px rgba(37,99,235,0.3); }

        /* [SINTAKS CSS]: Modul Storage Kapasitas Pojok Kiri Bawah Sempit Info Lahan Parkir Ringkas */
        .storage-sidebar { margin-top: auto; padding: 15px; background: #f1f5f9; border-radius: 15px; margin-bottom: 15px; }
        .storage-sidebar p { margin: 0 0 5px 0; font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        .progress-mini { height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; }
        .progress-fill { height: 100%; background: #1d4ed8; }

        .btn-logout-sidebar { color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600; padding-left: 10px; }

        /* [SINTAKS CSS]: Lahan Konten Manajemen Formulir Utama Kanan */
        .main-content { flex: 1; background: white; padding: 30px 40px; overflow-y: auto; }
        .section-title { font-size: 22px; color: #0f172a; margin: 0 0 20px 0; font-weight: 800; }
        
        /* [SINTAKS CSS]: Papan Kotak Form Pengisan Data Staff Karyawan Baru / Akun Modifikasi Tampilan Modern Melengkung Sudut Radius 20px */
        .form-card { background: #f1f5f9; padding: 20px; border-radius: 20px; margin-bottom: 25px; border: 1px solid #e2e8f0; }
        
        /* [SINTAKS CSS]: Form Grid CSS Layouting | Menjajarkan Kolom isian menyamping horizontal di dalam frame flexbox dgn porsi Fractional pecahan kolom (1.2 bagian dsb)*/
        .grid-form { display: grid; grid-template-columns: 1.2fr 1fr 1fr 0.8fr 0.6fr; gap: 12px; align-items: end; }
        .input-group { display: flex; flex-direction: column; gap: 6px; }
        .input-group label { font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase; }
        
        /* [SINTAKS CSS]: Kolom Isian Input Input & Select Bar Modifikasi Outline Putih Tulang Shadow Tipis */
        .input-group input, .input-group select { padding: 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 13px; outline: none; }
        
        .btn-simpan { background: #1d4ed8; color: white; border: none; padding: 11px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-simpan:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37,99,235,0.2); }
        
        /* [SINTAKS CSS]: Wadah Frame Pembungkus Tabel List Akun DB */
        .table-container { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 18px; color: #cbd5e1; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #f1f5f9; }
        td { padding: 12px 18px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
        
        /* [SINTAKS CSS]: Lencana Atribut Role Karyawan Admin, Petugas, Eksekutif dibedakan Warnanya Secara Dinamis */
        .role-badge { padding: 3px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; }
        .role-ADMIN { background: #fee2e2; color: #b91c1c; } /* Merah Darah untuk Admin Utama Spesial */
        .role-PETUGAS { background: #dcfce7; color: #166534; } /* Lencana Hijau Rumput buat Staff Biasa Penjaga loket portal */
        .role-OWNER { background: #fef9c3; color: #854d0e; } /* Kuning Keemasan Premium untuk Bangsawan Level Direksi Tertinggi (Bos The Owner)*/
        
        .btn-hapus { color: #ef4444; text-decoration: none; font-weight: 700; font-size: 11px; }
        
        /* [SINTAKS CSS]: Tampilan Blok Merah Kotak Pengingat Notifikasi Tanda Error Kalo Pendaftaran Berabe/Akun Dobel Error Warning */
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 15px; margin-bottom: 20px; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="app-container">
        
        <!-- [SINTAKS HTML]: Kerangka Menu Samping Laci Kiri -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Impor Logo -->
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <!-- [SINTAKS HTML]: Highlighted Link Active State ke Biru laut -->
                <a href="kelola_user.php" class="active">👥 Data User</a>
                <a href="tarif_parkir.php">📂 Data Tarif</a>
                <a href="area_parkir.php">🕒 Data Area</a>
            </div>

            <!-- [SINTAKS HTML]: Meteran Kapasitas Terukur Miniatur -->
            <div class="storage-sidebar">
                <p>SLOT TERISI</p>
                <div class="progress-mini">
                    <div class="progress-fill" style="width:<?= ($kendaraan_masuk/1350)*100 ?>%;"></div>
                </div>
                <!-- [SINTAKS PHP]: Inline Echo Pengeprint Indikator Angka Rasio Maksimal Kapasitas Parkiran Gedung -->
                <p style="margin-top:5px; font-size:10px; color:#1e293b;"><?= $kendaraan_masuk ?> / 1350</p>
            </div>

            <a href="../../auth/logout.php" class="btn-logout-sidebar">🚪 Logout</a>
        </div>

        <div class="main-content">
            <h1 class="section-title">Kelola Pengguna</h1>

            <!-- [SINTAKS PHP]: Modul Penampil Log Peringatan Teks Error Merah jika Valuasi $error_msg != String Kosong "" -->
            <?php if($error_msg != ""): ?>
                <div class="alert-error">⚠️ <?= $error_msg ?></div>
            <?php endif; ?>

            <!-- [SINTAKS HTML]: Kotak Putih Formulir Pendaftaran Perekrutan Staff Pegawai Baru -->
            <div class="form-card">
                <!-- [SINTAKS HTML]: Form Method POST | Rute Pengirim Paket Data Terselubung dan Aman tanpa bocor di batang URL ke atas Logic PHP Blok Baris 22 td -->
                <form method="POST" class="grid-form">
                    
                    <!-- [SINTAKS HTML]: INPUT TEXT | Kolom input Teks Standar Dengan Proteksi WAJIB ISI Required HTML API -->
                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Nama..." required>
                    </div>
                    
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Username..." required>
                    </div>
                    
                    <!-- [SINTAKS HTML]: INPUT PASSWORD | Enkripsi tampilan input visual titiktitik hitam dot obfuscation untuk kolom input rahasia kata sandi baru akun -->
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="•••••" required>
                    </div>
                    
                    <!-- [SINTAKS HTML]: SELECT OPTION | Kolom Pilih Pilihan Tunggal Dropdown (Combo Box Tipe Peran Level Izin Pengguna Baru) Menuju Logic Parameter Inputan -->
                    <div class="input-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    
                    <!-- [SINTAKS HTML]: SUBMIT BUTTON | Trigger Penembakan Query ke Mesin Atas -->
                    <button type="submit" name="simpan" class="btn-simpan">Simpan</button>
                </form>
            </div>

            <div class="table-container">
                <!-- [SINTAKS HTML]: List Penampilan Laporan Kehadiran Staff di Sistem (Data Visualisasi Tabel Baris Kolom) -->
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Nama Lengkap</th><th>Username</th><th>Role</th><th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        // [SINTAKS PHP]: mysqli_query Fetcher | Memutar Roda Pemanggilan Urutan Descending Data Akun di Tabel dari yang terbaru turun ke bawah SQL ENGINE EXECUTION
                        $q = mysqli_query($koneksi, "SELECT * FROM tb_user ORDER BY id_user DESC");
                        
                        // [SINTAKS PHP]: Looping Render Komponen Baris Kolom Td HTML ke Layar
                        while($data = mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <!-- [SINTAKS PHP]: Echo Angka Counter yg bertambah terus (++ Increment Numerators Action) -->
                            <td>#<?= $no++ ?></td>
                            <td style="font-weight: 700;"><?= $data['nama_lengkap'] ?></td>
                            
                            <!-- [SINTAKS PHP]: Kombinasi Karakter String dan Parameter Echo Username (@UsernameKerenKu) -->
                            <td>@<?= $data['username'] ?></td>
                            
                            <!-- [SINTAKS PHP]: strtoupper() | Pengubah karakter tulisan kecil 'owner' menjadi BESAR 'OWNER' biar cocok dengan nama Class CSS Role Badge Lencana Warnawarni di atas tadi -->
                            <td><span class="role-badge role-<?= strtoupper($data['role']) ?>"><?= strtoupper($data['role']) ?></span></td>
                            
                            <td style="text-align: right;">
                                <!-- [SINTAKS HTML & JS]: Anchor URL Pemicu Delete Link "?hapus=ID" | Dipasangkan Rem Konfirmasi JS Confirm Browser Popup buat meminimalisir salah Klik Mouse Fatal Delete -->
                                <a href="?hapus=<?= $data['id_user'] ?>" class="btn-hapus" onclick="return confirm('Hapus Permanen Akun Ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
