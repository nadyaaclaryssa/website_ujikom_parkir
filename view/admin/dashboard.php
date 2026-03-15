<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/dashboard.php
// -> Tujuan Spesifik: Halaman antarmuka beranda Admin Induk yang menyajikan visualisasi data statistik dasar keseluruhan sistem (Total Slot, Income, Log).
// ======================================

// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitas Admin
session_start();

// [SINTAKS PHP]: Seleksi Kondisi Akses | Cegat user selain admin agar tak bisa menjebol masuk laman kontrol utama
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: header loc | Lempar penyusup balik ke habitat Login
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: include koneksi | Menjemput kabel sambungan database 
include '../../config/koneksi.php';

/* [SINTAKS PHP]: Kumpulan Variabel Query Statistik Dashboard Admin */
// -> Menjumlahkan Grand Total (SUM) seluruh riwayat biaya pembayaran tanpa difilter tanggal berapapun
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(biaya_total) as total FROM tb_transaksi"))['total'] ?? 0;

// -> Menghitung Mobil/Motor yang STNK nya masih ditahan di dalam area parkir (Status='masuk')
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;

// -> Menghitung jumlah kepala/Aktor dengan hak akses 'Petugas' di tabel user
$total_petugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_user WHERE role='petugas'"))['total'] ?? 0;

// [SINTAKS PHP]: Aritmatika Ringan Pengurangan | Mencari jatah sisa lot parkir kosong dari total maksimum absolut 1350 tempat
$sisa_slot = 1350 - $kendaraan_masuk; 
?>

<!-- [SINTAKS HTML]: Document Type Declaration Standar 5 -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts Dashboard - Premium Gradient</title>
    <!-- [SINTAKS HTML]: Memanggil gaya huruf eksternal -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: Tumpuan Style Atribut UI Dashboard Admin -->
    <style>
        /* [SINTAKS CSS]: Kamus Variabel Dasar Custom Admin Theme */
        :root {
            --primary: #1d4ed8;
            --primary-light: #60a5fa;
            --grad-1: #e0f2fe; 
            --grad-2: #bae6fd;
        }

        /* [SINTAKS CSS]: CSS Reset Box Sizing */
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* [SINTAKS CSS]: Body bg gradient biru langit cerah konstan (Fixed Background) */
        body { 
            margin: 0; 
            background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* [SINTAKS CSS]: Area Putih Utama Applikasi Shell layarnya lebih lebar mentok 1400px dibanding peran lain */
        .app-container {
            width: 100%;
            max-width: 1400px;
            height: 92vh;
            background: white;
            border-radius: 32px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.08); /* Bayang layang blur 60px */
        }

        /* [SINTAKS CSS]: Panel Navigasi Samping kiri */
        .sidebar {
            width: 280px;
            background: white;
            padding: 40px 25px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* [SINTAKS CSS]: Tempat Logo dan Teks Parline flexbox Sebaris */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0 10px;
            margin-bottom: 40px;
        }
        
        .logo-section img { width: 45px; height: 45px; border-radius: 12px; }
        .logo-section h2 { font-size: 20px; margin: 0; color: #0f172a; font-weight: 800; }

        /* [SINTAKS CSS]: Menu Tab flex grow */
        .nav-menu { flex-grow: 1; }
        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            text-decoration: none;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
            border-radius: 18px;
            transition: 0.3s;
        }

        /* [SINTAKS CSS]: Active menyorot tab biru yg diklik Admin */
        .nav-menu a.active {
            background: #1d4ed8;
            color: white;
            box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.25);
        }
        
        /* [SINTAKS CSS]: Pseudo class hover pengecualian yg sudah aktif tak berkedip lg warnanya */
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #0f172a; }

        /* [SINTAKS CSS]: Bagian konten scrollable overflow-y area utama dikanan sidebar */
        .main-content {
            flex: 1;
            background: #f1f5f9;
            padding: 40px 50px;
            overflow-y: auto;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* [SINTAKS CSS]: Custom input dummy pencarian estetik */
        .search-bar {
            background: #f1f5f9;
            padding: 12px 25px;
            border-radius: 20px;
            width: 350px;
            border: none;
            color: #475569;
            font-size: 14px;
            /* Placeholder UI dummy untuk desain estetika */
        }

        .section-title { font-size: 14px; font-weight: 800; margin-bottom: 25px; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        
        /* [SINTAKS CSS]: CSS Grid | Membangun deretan 4 kartu atas yang akan membungkus Informasi Numerik Stat Card menggunakan Repeat fungsi pecahan fr */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 50px;
        }

        /* [SINTAKS CSS]: Panel Kardus Info Putih radius lengkung */
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 28px;
            border: 1px solid #e2e8f0;
            transition: 0.4s;
        }

        /* [SINTAKS CSS]: Kartu Utama disemprot gradasi primer Biru */
        .stat-card.primary-card { 
            background: linear-gradient(135deg, #2563eb, #3b82f6); 
            color: white;
            box-shadow: 0 20px 30px -10px rgba(37, 99, 235, 0.2);
            border: none;
        }
        
        .stat-card h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 15px 0; opacity: 0.7; }
        .stat-card .val { font-size: 28px; font-weight: 800; }
        
        /* [SINTAKS CSS]: Tabel Papan Kartu log riwayat aktivitas */
        .table-card {
            background: white;
            padding: 30px;
            border-radius: 32px;
            border: 1px solid #e2e8f0;
        }

        /* [SINTAKS CSS]: Konfigurasi standar Tabel tanpa celah antar sel garis */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #cbd5e1; font-size: 11px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
        td { padding: 22px 15px; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }

        /* [SINTAKS CSS]: Modifier Label Type Badge motor mobil dinamis */
        .badge {
            padding: 7px 14px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 800;
        }
        .badge-motor { background: #e0f2fe; color: #0369a1; } /* Tema biruan untuk motor */
        .badge-mobil { background: #dcfce7; color: #166534; } /* Tema hijauan untuk mobil */

        /* [SINTAKS CSS]: Monospace | Font khusus penulisan angka ala mesin ketik buat Plat Nomor */
        .plat-code {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            background: #f1f5f9;
            padding: 5px 10px;
            border-radius: 8px;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        .storage-box {
            margin-top: auto;
            padding: 25px;
            background: #f1f5f9;
            border-radius: 24px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- [SINTAKS HTML]: Bilik Tab Samping -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: Logo Brand -->
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <!-- [SINTAKS HTML]: Nav Bar Master Data Management (Admin Punya Modul Lebih lengkap dari yg lain) -->
            <div class="nav-menu">
                <a href="dashboard.php" class="active">🏠 Dashboard</a>
                <a href="kelola_user.php">👥 Data User</a>
                <a href="tarif_parkir.php">📂 Data Tarif</a>
                <a href="area_parkir.php">🕒 Data Area</a>
            </div>

            <!-- [SINTAKS HTML]: Meteran Kapasitas Terukur -->
            <div class="storage-box">
                <p style="margin: 0 0 12px 0; color: #64748b; font-size: 10px; font-weight: 800;">STORAGE DETAILS</p>
                <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                    <!-- [SINTAKS PHP]: Inject Kalkulasi Persentase ke properti css Width biru inline -->
                    <div style="width: <?= ($kendaraan_masuk/1350)*100 ?>%; height: 100%; background: #1d4ed8;"></div>
                </div>
                <p style="margin: 0; font-weight: 700; font-size: 13px; color: #0f172a;">Slot: <?= $kendaraan_masuk ?> / 1350</p>
                <!-- [SINTAKS HTML]: Link Kosong (Dummy feature display) -->
                <a href="#" style="color: #1d4ed8; text-decoration: none; font-size: 11px; font-weight: 700; display: block; margin-top: 10px;">Upgrade Slot ↗</a>
            </div>
            
            <a href="../../auth/logout.php" style="margin-top: 25px; color: #64748b; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <!-- [SINTAKS HTML]: Header Profil Pojok Kanan Atas & Box Search Pajangan -->
            <div class="header-top">
                <div class="search-bar">🔍 Search Data Transaksi...</div>
                <div style="display: flex; gap: 25px; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px; border-left: 1px solid #e2e8f0; padding-left: 20px;">
                        <div style="text-align: right;">
                            <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Admin Hogwarts</div>
                            <div style="font-size: 11px; color: #64748b;">Gringotts Level</div>
                        </div>
                        <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 12px; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800;">H</div>
                    </div>
                </div>
            </div>

            <h2 class="section-title">Quick Access</h2>
            
            <!-- [SINTAKS HTML]: Display Data Grid Statistik hasil Array Fetcher di awal file script (Inline Echo print) -->
            <div class="stats-grid">
                <div class="stat-card primary-card">
                    <h3>Total Pendapatan</h3>
                    <div class="val">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></div>
                    <p style="font-size: 11px; margin-top: 15px; opacity: 0.8;">Bulan ini: Vault Records</p>
                </div>
                <div class="stat-card">
                    <h3>Kendaraan Masuk</h3>
                    <div class="val"><?= $kendaraan_masuk ?></div>
                    <p style="font-size: 11px; margin-top: 15px; color: #64748b;">Status: Aktif Sekarang</p>
                </div>
                <div class="stat-card">
                    <h3>Sisa Slot</h3>
                    <div class="val"><?= $sisa_slot ?></div>
                    <p style="font-size: 11px; margin-top: 15px; color: #64748b;">Kapasitas: 1350</p>
                </div>
                <div class="stat-card">
                    <h3>Total Petugas</h3>
                    <div class="val"><?= $total_petugas ?></div>
                    <p style="font-size: 11px; margin-top: 15px; color: #64748b;">Role: Petugas Aktif</p>
                </div>
            </div>

            <h2 class="section-title">All Activity Logs</h2>
            
            <!-- [SINTAKS HTML]: Log Area Riwayat (Menampilkan 5 Data Terkini realtime) -->
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Petugas</th>
                            <th>Kendaraan</th>
                            <th>Plat Nomor</th>
                            <th>Waktu Masuk</th>
                            <th>Estimasi Biaya</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // [SINTAKS PHP]: mysql_query 5 Relasi Data | Meminta jejak rekaman transaksi masuk dengan metode LEFT JOIN. Limit 5 dibatasi cuma nongolin lima mobil terbaru doang
                        $q_log = mysqli_query($koneksi, "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, u.nama_lengkap as petugas FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan LEFT JOIN tb_user u ON t.id_user = u.id_user ORDER BY t.waktu_masuk DESC LIMIT 5");
                        
                        // [SINTAKS PHP]: Putaran While fetch
                        while($row = mysqli_fetch_assoc($q_log)) {
                        ?>
                        <tr>
                            <!-- [SINTAKS PHP]: Coalesce Fallback Operator | Mencegah display error teks nol/putih blank jika penjaga gerbang anonim (fallback teks 'sistem) -->
                            <td style="font-weight: 700; color: #0f172a;"><?= $row['petugas'] ?? 'Sistem' ?></td>
                            
                            <!-- [SINTAKS PHP]: Inline Ternary Conditional & strtoUpper() | Jika value array di DB jenis mobil, lempar tag Class Badge hijau-mobil, Kalau bukan ya lempar biru-motor. Nilai dikapitalin stringnya -->
                            <td><span class="badge <?= ($row['jenis_kendaraan'] ?? '') == 'motor' ? 'badge-motor' : 'badge-mobil' ?>"><?= strtoupper($row['jenis_kendaraan'] ?? 'NULL') ?></span></td>
                            
                            <td><span class="plat-code"><?= $row['plat_nomor'] ?></span></td>
                            
                            <!-- [SINTAKS PHP]: Pemanis format waktu (H:i) dari waktu standar MySQL DateTime Default -->
                            <td style="color: #475569;"><?= date('d M, H:i', strtotime($row['waktu_masuk'])) ?></td>
                            
                            <td style="font-weight: 800; color: #0f172a;">Rp <?= number_format($row['biaya_total'], 0, ',', '.') ?></td>
                            
                            <td style="color: #cbd5e1; font-weight: bold; cursor: pointer;">•••</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
