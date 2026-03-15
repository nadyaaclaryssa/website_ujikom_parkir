
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/petugas/transaksi_masuk.php
// -> Tujuan Spesifik: Form HTML pengisian plat kendaraan untuk inisiasi proses pendataan Check-In Parkir.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
// Pastikan pengecekan role sesuai dengan data di session login kamu
if ($_SESSION['role'] != "petugas") { // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
    // [SINTAKS PHP]: header() | Pengalihan sistem otomatis (Redirect) ke modul terkait
header("location:../../auth/index.php");
    exit;
}
// [SINTAKS PHP]: include | Menyertakan file konfigurasi koneksi ke server MySQL Database agar tabel bisa dibaca/ditulis
include '../../config/koneksi.php';

$area_terpilih = isset( // [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
    // [SINTAKS PHP]: $_GET | Menangkap parameter URL untuk memproses logic database spesifik
$_GET['area']) ? // [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
    // [SINTAKS PHP]: $_GET | Menangkap parameter URL untuk memproses logic database spesifik
$_GET['area'] : '';
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hogwarts Petugas - Transaksi Masuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1e40af;
            --bg-gradient: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
            --text-main: #1e293b;
            --text-sub: #64748b;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            margin: 0;
            background: var(--bg-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 15px;
        }

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

        .nav-menu a.active {
            background: #2563eb;
            color: var(--white);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .nav-menu a:hover:not(.active) {
            background: #f1f5f9;
            color: #2563eb;
        }

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

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

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

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563eb;
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

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
        <div class="sidebar">
            <div class="logo-section">
                <img src="../../public/hogwarts-removebg-preview.png" width="38">
                <h2>Parline</h2>
            </div>

            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="transaksi_masuk.php" class="active">📥 Transaksi Masuk</a>
                <a href="transaksi_keluar.php">📤 Transaksi Keluar</a>
            </div>

            <div class="storage-box">
                <p
                    style="margin:0 0 10px 0; font-size:10px; font-weight:800; color:#475569; letter-spacing:0.5px;">
                    STORAGE DETAILS</p>
                <div style="height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:12px;">
                    <div
                        style="width: <?=($kendaraan_masuk / 1350) * 100?>%; height:100%; background:#2563eb;">
                    </div>
                </div>
                <p style="margin:0; font-size:13px; font-weight:800; color:#0f172a;">Slot:
                    <?= $kendaraan_masuk?> <span style="color:#94a3b8; font-weight:600;">/ 1350</span>
                </p>
            </div>

            <a href="../../auth/logout.php"
                style="margin-top:25px; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;">🚪
                Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top" style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Input Transaksi</h1>
                    <p style="color:#475569; margin:5px 0 0 0; font-size:14px;">
                        <?= date('l, d F Y')?>
                    </p>
                </div>
                <div style="display:flex; align-items:center; gap:15px;">
                    <div style="text-align:right">
                        <div style="font-size:14px; font-weight:800; color:#0f172a;">
                            <?= $_SESSION['nama']?>
                        </div>
                        <div style="font-size:11px; color:#475569; font-weight:600;">Petugas Aktif</div>
                    </div>
                    <div
                        style="width:45px; height:45px; background:#2563eb; border-radius:14px; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);">
                        <?= substr($_SESSION['nama'], 0, 1)?>
                    </div>
                </div>
            </div>

            <div class="form-wrapper">
                <div class="form-card">
                    <form action="proses_masuk.php" method="POST">
                        <div class="form-group">
                            <label>Nomor Plat Kendaraan</label>
                            <input type="text" name="plat_nomor" placeholder="Contoh: B 1234 ABC" required autofocus>
                        </div>

                        <div class="form-group">
                            <label>Jenis Kendaraan</label>
                            <select name="id_tarif" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php
$q_tarif = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, "SELECT * FROM tb_tarif");
while ($t = mysqli_fetch_assoc($q_tarif)) {
    echo "<option value='" . $t['id_tarif'] . "'>" . strtoupper($t['jenis_kendaraan']) . "</option>";
}
?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Lokasi Area Parkir</label>
                            <select name="id_area" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php
$q_area = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir");
while ($a = mysqli_fetch_assoc($q_area)) {
    $selected = (isset($area_terpilih) && $area_terpilih == $a['nama_area']) ? 'selected' : '';
    echo "<option value='" . $a['id_area'] . "' $selected>" . $a['nama_area'] . "</option>";
}
?>
                            </select>
                        </div>

                        <button type="submit" name="simpan" class="btn-submit">Simpan & Cetak Karcis</button>
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

