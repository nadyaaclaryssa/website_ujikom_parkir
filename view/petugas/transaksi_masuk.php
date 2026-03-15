<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/petugas/transaksi_masuk.php
// -> Tujuan Spesifik: Form HTML Operasional loket tempat petugas mengetik plat kendaran secara manual untuk melakukan pendataan Check-In (Awal) Parkir.
// ======================================

// [SINTAKS PHP]: session_start() | Memulai/Melanjutkan buffer Sesi user browser 
session_start();

// [SINTAKS PHP]: If Kondisional | Memblokade dan mengecek kelayakan otoritas pemanggil URL, menolak Admin/Owner melihat fungsional loket transaksi petugas
if ($_SESSION['role'] != "petugas") { 
    // [SINTAKS PHP]: header("location...") | Melempar/Redirect penyusup balik ke Beranda Log In form
    header("location:../../auth/index.php");
    // [SINTAKS PHP]: exit | Mematikan secara paksa pemrosesan halaman PHP yang di bawah
    exit;
}

// [SINTAKS PHP]: include | Tarik memori script konektor DB (Jembatan MySQL ke Script) config koneksi
include '../../config/koneksi.php';

// [SINTAKS PHP]: isset() & $_GET Tangkap URL Param | Metode Inisialisasi Dinamis form. 
// Jika petugas meng-Klik jalan pintas dari "Pilih Area Lantai 1", maka variable $area_terpilih digenggam ke form.
$area_terpilih = isset($_GET['area']) ? $_GET['area'] : '';

// [SINTAKS PHP]: mysqli_query Agregat & Coalesce (??) | Mengambil nilai kumulatif kalkulasi seberapa banyak mobil saat ini yang sedang berstatus nongkrong "Check-in in progress ('masuk')"
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;
?>

<!-- [SINTAKS HTML]: <!DOCTYPE html> | Menjamin render website mematuhi standar HTML versi 5 -->
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- [SINTAKS HTML]: <meta charset> | Proteksi Unicode (Standard String Render) -->
    <meta charset="UTF-8">
    <title>Hogwarts Petugas - Transaksi Masuk</title>
    <!-- [SINTAKS HTML]: <link> Google Fonts | Request URL ke Google CDN u/ mengambil font beraksen Premium tebal-tipis "Plus Jakarta Sans" -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: <style> Internal Sheet CSS Code -->
    <style>
        /* [SINTAKS CSS]: :root variables | Menanamkan token Global Variable CSS untuk standar warna/estetika project-wide uniformity */
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1e40af;
            --bg-gradient: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
            --text-main: #1e293b;
            --text-sub: #64748b;
            --white: #ffffff;
        }

        /* [SINTAKS CSS]: Universal Asterisk Selector (*) | Mengkalibrasi Ulang seluruh kerangka padding/margin tag HTML bawaan yang cacat menjadi teratur proporsinya */
        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* [SINTAKS CSS]: body Tag Rule | Konfigurasi Layout Induk latar Gradasi warna dan Flexbox Sentris. Layar sentral di tengah sumbu XY mutlak */
        body {
            margin: 0;
            background: var(--bg-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 15px;
        }

        /* [SINTAKS CSS]: .app-container Wrapper | Canvas Box Putih lebar UI aplikasi melintang 1200 pixels maksimum (Anti pecah di Monitor Lebar) dengan sudut bulat */
        .app-container {
            width: 100%;
            max-width: 1200px;
            height: 85vh;
            background: var(--white);
            border-radius: 32px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: .sidebar Nav | Papan kontrol Menu kiri dengan lebar statis (Freeze Width) dan Border Batas tipis di tepi kanannya */
        .sidebar {
            width: 260px;
            background: var(--white);
            padding: 40px 25px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .logo-section h2 {
            font-size: 20px;
            color: #0f172a;
            font-weight: 800;
            margin: 0;
        }

        /* [SINTAKS CSS]: a Menu Nav | Membersihkan ciri Link Hypertext baku HTML biru jelek menjadi tombol tombol bersih berbentuk block lengkung modern */
        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            border-radius: 16px;
            transition: 0.3s;
        }

        /* [SINTAKS CSS]: .active class | Modifikasi Spesifik Warna Aktif saat Tab page di posisi yang sedang dihinggapi saat ini (Mewarna biru dengan Shadow lembut menawan) */
        .nav-menu a.active {
            background: #2563eb;
            color: var(--white);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        /* [SINTAKS CSS]: Hover Interaction | Mengganti background pudar abu-abu ketika kursor mengapung di item Menu (Selain item yg sdg aktif) */
        .nav-menu a:hover:not(.active) {
            background: #f1f5f9;
            color: #2563eb;
        }

        /* [SINTAKS CSS]: .main-content Screen | Panggung Lebar Kanan sisa flex ber-Overflow (Mampu di-scroll Wheel panjang seukuran forms) */
        .main-content {
            flex: 1;
            background: #f1f5f9;
            padding: 40px 50px;
            overflow-y: auto;
        }

        .header-top h1 {
            font-size: 24px;
            color: #0f172a;
            font-weight: 800;
            margin: 0;
        }

        /* [SINTAKS CSS]: Form Wrapper Centering | Menaruh blok papan Inputan persis di sentral tengah secara elagan melayang (Card Elevation box shadow) */
        .form-wrapper {
            max-width: 550px;
            margin: 40px auto 0;
        }

        .form-card {
            background: var(--white);
            padding: 35px;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        }

        .form-group {
            margin-bottom: 22px;
        }

        /* [SINTAKS CSS]: Label Mod | Tipografi label tulisan kolom kecil kapital (Uppercase) rapat modern nan estetik tebalnya */
        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* [SINTAKS CSS]: Multiple Selector Mod (,) Input&Select | Menyeragamkan bentuk rupa visual kolom Textbox pengetikan & Tampilan Dropdown Select dengan tema seragam padu membulat */
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: 0.3s;
            font-weight: 500;
        }

        /* [SINTAKS CSS]: Focus Interaction Glow Ring | Menyala memunculkan pendar shadow saat input/select diklik mengetik, membantu aksesibilitas petugas yg awam komputer melihat titik posisi pengetikan aktif */
        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563eb;
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: .btn-submit Button | Menghancurkan Border pabrik Chrome, ganti dengan cat custom dan transisi naiknya bayangan melompat (Microanimation) */
        .btn-submit {
            background: #2563eb;
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.25);
        }

        /* [SINTAKS CSS]: Button Hover translateY(-2px) | Menarik Tombol 2 piksel ke udara tatkala kursor menyentuhnya */
        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .storage-box {
            margin-top: auto;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 20px;
        }
    </style>
</head>

<body>

    <div class="app-container">
        
        <!-- [SINTAKS HTML]: <nav> Sidebar Kiri (Navigasi Modul Kasir) -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Impor Logo Aplikasi -->
                <img src="../../public/hogwarts-removebg-preview.png" width="38">
                <h2>Parline</h2>
            </div>

            <!-- [SINTAKS HTML]: Indikator Menu | Tab saat ini disorot (Class: active) pada item Transaksi Masuk -->
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="transaksi_masuk.php" class="active">📥 Transaksi Masuk</a>
                <a href="transaksi_keluar.php">📤 Transaksi Keluar</a>
            </div>

            <!-- [SINTAKS HTML]: Storage Progress Widget | Metrik Persentasi Lahan -->
            <div class="storage-box">
                <p style="margin:0 0 10px 0; font-size:10px; font-weight:800; color:#475569; letter-spacing:0.5px;">
                    STORAGE DETAILS</p>
                <div style="height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:12px;">
                    <!-- [SINTAKS PHP]: Inline Variabel Print (=) | Injeksi kalkulasi Lebar meteran biru dari data persentase stok terisi ke slot inline style width CSS HTML element -->
                    <div style="width: <?=($kendaraan_masuk / 1350) * 100?>%; height:100%; background:#2563eb;">
                    </div>
                </div>
                <!-- [SINTAKS PHP]: Print Angka Stock terhuni saat ini -->
                <p style="margin:0; font-size:13px; font-weight:800; color:#0f172a;">Slot:
                    <?= $kendaraan_masuk?> <span style="color:#94a3b8; font-weight:600;">/ 1350</span>
                </p>
            </div>

            <!-- [SINTAKS HTML]: Link Pemutus Session (Logout) -->
            <a href="../../auth/logout.php"
                style="margin-top:25px; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;">🚪
                Logout</a>
        </div>

        <!-- [SINTAKS HTML]: Jendela Formulir Utama -->
        <div class="main-content">
            <!-- [SINTAKS HTML]: Header Inline Flex | Memisahkan blok Teks salam Header dengan Avatar Profil (Space Between rata pinggir-pinggir) -->
            <div class="header-top" style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Input Transaksi</h1>
                    <!-- [SINTAKS PHP]: date() format kalender | Menerjemahkan hari ("l" misal Monday), Tanggal ("d"), Bulan Penuh ("F"), Tahun 4 digit ("Y") Live Otomatis hari ini real-time -->
                    <p style="color:#475569; margin:5px 0 0 0; font-size:14px;">
                        <?= date('l, d F Y')?>
                    </p>
                </div>
                <!-- [SINTAKS HTML]: Avatar Container Widget & Level Profile Name -->
                <div style="display:flex; align-items:center; gap:15px;">
                    <div style="text-align:right">
                        <!-- [SINTAKS PHP]: Echoing $_SESSION | Meneruskan render variabel pendaftaran nama pelapor asli tanpa Query SELECT baru -->
                        <div style="font-size:14px; font-weight:800; color:#0f172a;">
                            <?= $_SESSION['nama']?>
                        </div>
                        <div style="font-size:11px; color:#475569; font-weight:600;">Petugas Aktif</div>
                    </div>
                    <!-- [SINTAKS PHP]: substr(nama, start 0, batas 1 huruf decut) | Mewujudkan Abjad Inisial dari nama panjang kasir -->
                    <div
                        style="width:45px; height:45px; background:#2563eb; border-radius:14px; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);">
                        <?= substr($_SESSION['nama'], 0, 1)?>
                    </div>
                </div>
            </div>

            <!-- [SINTAKS HTML]: <div> Wrapper Box Panggung Formulir Kartu -->
            <div class="form-wrapper">
                <div class="form-card">
                    <!-- [SINTAKS HTML]: <form> Action & Post | Memicu transmisi kirim/transport array values ke pintu masuk file "proses_masuk.php" berlandasan protokol Method keamanan POST (Tak teridentifikasi di address bar URL) -->
                    <form action="proses_masuk.php" method="POST">
                        
                        <!-- [SINTAKS HTML]: Pengetikan Input Teks -->
                        <div class="form-group">
                            <label>Nomor Plat Kendaraan</label>
                            <!-- [SINTAKS HTML]: Autocomplete-off, Required & Autofocus | Autofocus langsung memusatkan kedipan pointer keyboard mouse (Kursor otomatis nyala standby) sewaktu page mereload tanpa di klick manual -->
                            <input type="text" name="plat_nomor" placeholder="Contoh: B 1234 ABC" required autofocus>
                        </div>

                        <!-- [SINTAKS HTML]: Dropdown Select Jenis Tariffs Options -->
                        <div class="form-group">
                            <label>Jenis Kendaraan</label>
                            <!-- [SINTAKS HTML]: <select> required | Mewajibkan memilih Opsi list, tak boleh biarkan Default Placeholder -->
                            <select name="id_tarif" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php
                                // [SINTAKS PHP]: mysqli_query SELECT | Eksekusi baca master Data Tarif mobil/motor untuk list Dropdown (Dinamic Data Pull)
                                $q_tarif = mysqli_query($koneksi, "SELECT * FROM tb_tarif");
                                // [SINTAKS PHP]: While recordset loop | Mengulang-ulang memproduksi Element-Tag Select <option> berdasarkan peredaran isi baris database Tabel tb_tarif
                                while ($t = mysqli_fetch_assoc($q_tarif)) {
                                    // [SINTAKS PHP]: strtoupper() Konversi Kapita HTML | Menginjeksi nama jenis kendaraan dan melekatkan Primary ID-nya sebagai penanda Value yang bakal ditenagai dikirim dalam $_POST
                                    echo "<option value='" . $t['id_tarif'] . "'>" . strtoupper($t['jenis_kendaraan']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- [SINTAKS HTML]: Dropdown Select Pilihan Slot/Pos Area Parkir (Auto-Select jika via pintasan URL $_GET) -->
                        <div class="form-group">
                            <label>Lokasi Area Parkir</label>
                            <!-- [SINTAKS HTML]: <select name=id_area> -->
                            <select name="id_area" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php
                                // [SINTAKS PHP]: Query Tarik List Kapabilitas Tabel Area Master Data Parking Lots
                                $q_area = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir");
                                
                                // [SINTAKS PHP]: Loop Pencipta Tag Options
                                while ($a = mysqli_fetch_assoc($q_area)) {
                                    // [SINTAKS PHP]: If Ternary Operator (?) Select State Check | Otomatis men-trigger parameter HTML attribut 'selected' apabila text nama_area dari $_GET URL tembus nyala cocok persis dengan loop ini (Menjadikan item opsi aktif terseleksi duluan otomatis)
                                    $selected = (isset($area_terpilih) && $area_terpilih == $a['nama_area']) ? 'selected' : '';
                                    
                                    // [SINTAKS PHP]: Echo Render HTML Teks Modifikasi gabungan Variable di sela-sela raw tags
                                    echo "<option value='" . $a['id_area'] . "' $selected>" . $a['nama_area'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- [SINTAKS HTML]: <button type="submit"> | Panel Penutup Submit pelontar form berdaya mekanika Action Trigger lemparan HTTP -->
                        <button type="submit" name="simpan" class="btn-submit">Simpan & Cetak Karcis</button>
                        
                        <!-- [SINTAKS HTML]: <a> Kembali link -->
                        <a href="dashboard.php"
                            style="display:block; text-align:center; margin-top:20px; font-size:13px; color:#475569; text-decoration:none; font-weight:700;">←
                            Kembali ke Dashboard</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
