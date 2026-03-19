<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/owner/detail_laporan.php
// -> Tujuan Spesifik: Modul/komponen pelengkap Owner untuk meninjau detail tabel rekap bukti pemasukan per satu slot kendaraan Transaksi Keluar.
// ======================================

// [SINTAKS PHP]: session_start() | Memulai buffer jejak Sesi Autentikasi untuk menjaga kerahasiaan laman Owner The Boss
session_start();

// [SINTAKS PHP]: Validasi Lapis Proteksi | Apabila role kedudukan pendaftar bukan 'owner', pintu ditolak masuk
if($_SESSION['role'] != "owner") { 
    // [SINTAKS PHP]: Location header | Lempar terbang pengacau sistem kembali ke Laman Muka Autentikator
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: include | Tarik memori relasi Mesin Database Server (Konektor) Configs
include '../../config/koneksi.php';

// [SINTAKS PHP]: mysqli_query (Multiple Inner Join bersilang Logika) | Ambil Tumpukan Data Pelaporan Sejarah
// Skenario Logikal DB: Relasikan Entitas Transaksi, Entitas Kendaraan. Hanya Filter data yg Status mobilnya sudah Checkout ('Keluar') - Biar yang muncul cuma yg emang udah "Lunas/Bayar Uang Pendapatan Total". Diurutkan Descendant dari yg terbaru paling atas.

// [SINTAKS PHP]: Ambil parameter filter tanggal dari URL (GET)
$tanggal_awal  = isset($_GET['dari']) ? mysqli_real_escape_string($koneksi, $_GET['dari']) : '';
$tanggal_akhir = isset($_GET['sampai']) ? mysqli_real_escape_string($koneksi, $_GET['sampai']) : '';

$sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan WHERE t.status='keluar'";

if(!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $sql .= " AND DATE(t.waktu_keluar) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}
$sql .= " ORDER BY t.waktu_keluar DESC";

$query = mysqli_query($koneksi, $sql);

// [SINTAKS PHP]: Hitung grand total dari data yang difilter
$total_query = mysqli_query($koneksi, str_replace("SELECT t.*, k.plat_nomor, k.jenis_kendaraan", "SELECT SUM(t.biaya_total) as total", $sql));
$grand_total = mysqli_fetch_assoc($total_query)['total'] ?? 0;
?>

<!-- [SINTAKS HTML]: Deklarator Versi HTML5 -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts Owner - Detail Laporan</title>

    <!-- [SINTAKS HTML]: <link> typography URL Payload -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: Tumpuan Lembar Custom Paint Element Internal -->
    <style>
        /* [SINTAKS CSS]: :root Global Pallete Selector | Mewariskan kode angka warna hex dasar agar tak capek nulis kode berulang */
        :root {
            --primary: #3b82f6; 
            --bg: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
            --text-dark: #1e293b;
            --text-light: #64748b;
            --white: #ffffff;
        }

        /* [SINTAKS CSS]: Asterisk (*) CSS Resets Layout Engine Padding Chrome/Edge */
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* [SINTAKS CSS]: Canvas Papan Background Utama Area Pandang (Viewport-Height Full) */
        body { margin: 0; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 15px; }

        /* [SINTAKS CSS]: Layout Container Box Putih Terpusat Aplikasi App-Shell */
        .app-container {
            width: 100%; max-width: 1250px; height: 90vh; 
            background: var(--white); border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: Bilik Sidebar Kiri Membeku dengan Bingkai Separator kanan tipis border(1px solid color opacity) */
        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 20px; color: var(--text-dark); margin: 0; font-weight: 800; }

        /* [SINTAKS CSS]: Stylist Tab Anchor baris navigasi */
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 18px;
            text-decoration: none; color: var(--text-light); font-size: 14px; font-weight: 600;
            margin-bottom: 8px; border-radius: 16px; transition: 0.3s;
        }

        /* [SINTAKS CSS]: Active Class | Menyoroti Menu yg lagi dibuka (Detail Lap.) */
        .nav-menu a.active { background: #1d4ed8; color: white; box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3); }

        /* [SINTAKS CSS]: Penyalur Konten Frame Kanan Ekstensif Y-Scrollable */
        .main-content { flex: 1; padding: 40px; overflow-y: auto; background: #f1f5f9; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; color: var(--text-dark); margin: 0; font-weight: 800; }

        /* [SINTAKS CSS]: Kotak Bingkai Table | Merampingkan Ujung Tabel sehingga melengkung harmonis border-radius dgn sistem Hidden overflow corner */
        .table-container {
            background: white; border-radius: 24px; border: 1px solid #e2e8f0;
            overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        /* [SINTAKS CSS]: Tag HTML Tables Modifiers | Menghapus renggang antar kotak Row & Cell Table Default Browser yang kaku dengan "collapse" */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        
        /* [SINTAKS CSS]: Header Row Background abu-abu muda ala tabel admin modern Excel style */
        thead { background: #f1f5f9; }
        
        /* [SINTAKS CSS]: Th mod | Teks Kolom Judul Tabel di-set kapital semua + Spasi lapang dan dikecilkan font-size nya biar estetik korporat */
        thead th { 
            padding: 18px 25px; font-size: 12px; font-weight: 700; 
            color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* [SINTAKS CSS]: Baris TR dipisah oleh garis tipis bawah */
        tbody tr { border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
        
        /* [SINTAKS CSS]: Tabel Interaktif | Jika satu baris spesifik data tabel disorot mouse hover, warnanya berubah gelap tipis */
        tbody tr:hover { background: #f1f5f9; }
        
        /* [SINTAKS CSS]: Ruang bernafas dalam Padding elemen Td (Data cell) */
        tbody td { padding: 18px 25px; font-size: 14px; color: var(--text-dark); }
        
        /* [SINTAKS CSS]: Badge-Money Pill Label | Ornamen Uang yang membungkus background hijau di belakang teks Rupiah agar mencolok gampang dibaca Laporan Auditor */
        .badge-money {
            background: #dcfce7; color: #166534; padding: 6px 12px;
            border-radius: 10px; font-weight: 700; font-size: 13px;
        }

        /* [SINTAKS CSS]: Badge Type Pil Label | Teks Tipe kendaraan dilatar belakangi biru es cerah */
        .badge-type {
            background: #eff6ff; color: #1e40af; padding: 4px 10px;
            border-radius: 8px; font-size: 12px; font-weight: 600;
        }

        /* [SINTAKS CSS]: Settingan Desain Tombol Hitam Cetak Ekspor Berkas Printer */
        .btn-print {
            background: var(--text-dark); color: white; border: none;
            padding: 12px 20px; border-radius: 14px; font-weight: 700; cursor: pointer;
        }

        /* [SINTAKS CSS]: Tombol Hijau Export Excel */
        .btn-excel {
            background: linear-gradient(135deg, #16a34a, #15803d); color: white; border: none;
            padding: 12px 20px; border-radius: 14px; font-weight: 700; cursor: pointer;
            font-size: 13px; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-excel:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -5px rgba(22, 163, 74, 0.4); }

        /* [SINTAKS CSS]: Bar Filter Tanggal */
        .filter-bar {
            display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
            background: white; padding: 18px 22px; border-radius: 18px;
            border: 1px solid #e2e8f0; margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .filter-bar label { font-size: 13px; font-weight: 700; color: var(--text-dark); }
        .filter-bar input[type="date"] {
            padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px;
            font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark);
            outline: none; transition: 0.3s;
        }
        .filter-bar input[type="date"]:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .btn-filter {
            background: var(--primary); color: white; border: none;
            padding: 10px 18px; border-radius: 12px; font-weight: 700; cursor: pointer;
            font-size: 13px; transition: 0.3s;
        }
        .btn-filter:hover { background: #2563eb; }
        .btn-reset {
            background: #f1f5f9; color: var(--text-light); border: 1px solid #e2e8f0;
            padding: 10px 18px; border-radius: 12px; font-weight: 600; cursor: pointer;
            font-size: 13px; text-decoration: none; transition: 0.3s;
        }
        .btn-reset:hover { background: #e2e8f0; }
        .btn-group { display: flex; gap: 10px; align-items: center; }

        /* [SINTAKS CSS]: Custom UI Scrollbar Safari Kit Mod | Ganti palang grey penggulir ke samping jadi transparan dengan peluru abu tumpul lengkung */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        /* [SINTAKS CSS]: Sembunyikan filter bar saat print agar laporan bersih */
        @media print {
            .filter-bar, .btn-print, .btn-excel, .btn-group, .sidebar { display: none !important; }
            .app-container { box-shadow: none; border-radius: 0; height: auto; }
            .main-content { padding: 10px; }
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- [SINTAKS HTML]: Bilik Modul Samping -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Impor Logo -->
                <img src="../../public/hogwarts-removebg-preview.png" width="35">
                <h2>Parline</h2>
            </div>
            
            <!-- [SINTAKS HTML]: Navigasi Links. Class penanda aktf menyorot Detail Laporan -->
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="detail_laporan.php" class="active">📜 Detail Laporan</a>
            </div>
            
            <!-- [SINTAKS HTML]: Link Kunci Keluar Aplikasi margin ke bawah sendiri menjauh mentok dinding fleksbox karena M-Top parameter "auto" di CSS  -->
            <a href="../../auth/logout.php" style="margin-top:auto; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <!-- [SINTAKS HTML]: Kop Laporan UI dengan tombol tempel atas -->
            <div class="header">
                <div>
                    <h1>Detail Laporan</h1>
                    <p style="color:var(--text-light); margin:5px 0 0; font-size:14px;">Seluruh data transaksi keluar dari sistem pos parkir.</p>
                </div>
                
                <div class="btn-group">
                    <!-- [SINTAKS HTML]: Tombol Export Excel | Mengarah ke export_excel.php dengan parameter tanggal -->
                    <a href="export_excel.php<?= (!empty($tanggal_awal) && !empty($tanggal_akhir)) ? '?dari='.$tanggal_awal.'&sampai='.$tanggal_akhir : '' ?>" class="btn-excel" style="text-decoration:none;">📊 Export Excel</a>
                    
                    <!-- [SINTAKS JAVASCRIPT]: Window Print Inject Event | Saat bos ngeklik memicu Window Browser menge-Print kertas dokumen data tabular Table Data di bawahnya -->
                    <button class="btn-print" onclick="window.print()">🖨️ Cetak Laporan</button>
                </div>
            </div>

            <!-- [SINTAKS HTML]: Bar Filter Tanggal untuk memfilter data berdasarkan rentang waktu -->
            <form class="filter-bar" method="GET" action="">
                <label>📅 Dari:</label>
                <input type="date" name="dari" value="<?= htmlspecialchars($tanggal_awal) ?>">
                <label>Sampai:</label>
                <input type="date" name="sampai" value="<?= htmlspecialchars($tanggal_akhir) ?>">
                <button type="submit" class="btn-filter">🔍 Filter</button>
                <a href="detail_laporan.php" class="btn-reset">↩️ Reset</a>
                <?php if($grand_total > 0): ?>
                <div style="margin-left:auto; background:#dcfce7; padding:10px 18px; border-radius:12px; font-weight:700; color:#166534; font-size:14px;">
                    💰 Total: Rp <?= number_format($grand_total, 0, ',', '.') ?>
                </div>
                <?php endif; ?>
            </form>

            <!-- [SINTAKS HTML]: Wadah Kotak Pembatas Table -->
            <div class="table-container">
                <!-- [SINTAKS HTML]: <table> Tag Rangka Ekosistem Data Baris x Kolom (Dibutuhkan untuk laporan excel export standar kompetensi industri RPL UKK) -->
                <table>
                    
                    <!-- [SINTAKS HTML]: <thead> (Table Header Kepala) Kelompok Baris Penamaan Kolom Labeling -->
                    <thead>
                        <tr>
                            <!-- [SINTAKS HTML]: <th> Judul Kolom Indeks 1/2/3 dsb -->
                            <th>No</th>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Total Bayar</th>
                        </tr>
                    </thead>
                    
                    <!-- [SINTAKS HTML]: <tbody> (Table Body Inti Isi Data) -->
                    <tbody>
                        <?php 
                        // [SINTAKS PHP]: Var Iterasi Numerik ($no) | Disajikan buat nampil counter urut Nomor 1,2,3 dst ketimbang menonjolkan ID Transaksi UUID panjang database di Laporan Keuangan
                        $no = 1;

                        // [SINTAKS PHP]: while + mysqli_fetch_array() | Merotasi Eksekusi Tipe Data Baris (Membedah tiap data keluar MySQL List SQL Array satu persatu menjadi Element Baris TR TD berulang ulang sejumlah kendaraan)
                        while($data = mysqli_fetch_array($query)) { 
                        ?>
                        <!-- [SINTAKS HTML]: <tr> Table Row Baris Data -->
                        <tr>
                            <!-- [SINTAKS HTML]: <td> Table Data Index Cell Number -->
                            <!-- [SINTAKS PHP]: Operator Increment Buntut (++) | Memprint 1 lalu ditambah di balik layar jadi dua saat dilooping loop puteran table baris berikutnya -->
                            <td><?= $no++; ?></td>
                            
                            <!-- [SINTAKS PHP]: Print var array String Data Plat Motor/Mobil Pengguna -->
                            <td><strong><?= $data['plat_nomor']; ?></strong></td>
                            
                            <!-- [SINTAKS PHP]: Default Value Evaluator (??) | Manakala kolom Value Jenis kosong, timbak fallback text String 'Umum' di dalam Element Pill Biru Badge Tipe -->
                            <td><span class="badge-type"><?= $data['jenis_kendaraan'] ?? 'Umum'; ?></span></td>
                            
                            <!-- [SINTAKS PHP]: Echoing timestamp terekam server SQL -->
                            <td style="color: var(--text-light); font-size: 13px;"><?= $data['waktu_masuk']; ?></td>
                            <td style="color: var(--text-light); font-size: 13px;"><?= $data['waktu_keluar']; ?></td>
                            
                            <td>
                                <!-- [SINTAKS HTML]: <span badge-money> Pembungkus Background Hijau Kotak -->
                                <span class="badge-money">
                                    <!-- [SINTAKS PHP]: Fungsi Akuntansi number_format() | Menyisip Tanda Titik Rupiah dalam Value nominal kolom biaya di DB Supaya elok di mata Bos Pemilik saat rekap pajak harian uang kartal -->
                                    Rp <?= number_format($data['biaya_total'], 0, ',', '.'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php 
                        } // [SINTAKS PHP]: Limit Kurung Kurawal Tutup Eksekutor Looping TR di Akhir pembacaan row terakhir DB 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
