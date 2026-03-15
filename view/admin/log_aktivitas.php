<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/log_aktivitas.php
// -> Tujuan Spesifik: Modul jejak audit keamanan (Audit Trail) berbasis Log pencatatan riwayat aksi para pengguna petugas di lapangan secara historikal runtut memanjang kebelakang.
// ======================================

// [SINTAKS PHP]: session_start() | Me-resume aktivitas identifikasi kunci kuki Memori User
session_start();

// [SINTAKS PHP]: Mengikutsertakan modul koneksi.php utama
include '../../config/koneksi.php';

// [SINTAKS PHP]: Proteksi Lapis Terluar | Mencegat akses bagi yang bukan Pemegang Palu Administrator
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: Lemparan Pulang ke Form Login
    header("location:../../auth/index.php"); 
    exit; 
}

// ==== BLOK LOGIKA: FILTER PENYARINGAN JEJAK DIGITAL LOGS ====
// [SINTAKS PHP]: Coalesce (?? '') | Jika belum ada niatan mencari filter tanggal spesifik dari GET di address bar URL, biarkan nilainya kopong aja 
$filter_tgl = $_GET['tanggal'] ?? '';

// [SINTAKS PHP]: $_GET parameter search | Mangkok penciduk hasil ketikan text pencarian string Nama / Jenis Aktivitas petugas yang ditempel via Form Submit di URL
$search = $_GET['search'] ?? '';

// [SINTAKS PHP]: Rangkaian Utama Kueri Dinamik. Trik 1=1 mempermudah nempelin syntaks AND dibelakangnya kalau klausa If di bawahnya terlewati beneran (True).
$query_str = "SELECT * FROM tb_log WHERE 1=1";

// [SINTAKS PHP]: Percabangan 1 | Kalau bos masukin parameter filter Kalender tanggal...
if($filter_tgl) {
    // [SINTAKS PHP]: Concat (TitikSamaDengan .=) Menempelkan perpanjangan Syntaks AND saringan tanggal DATE SQL dibelakang kalimat query utama
    $query_str .= " AND DATE(waktu) = '$filter_tgl'";
}

// [SINTAKS PHP]: Percabangan 2 | Kalau bos ngetik nama orang di kotak mesin pencari...
if($search) {
    // [SINTAKS PHP]: Concat LIKE Operand | Mencari kesesuaian potongan Teks string liar kembar (Fuzzy Search Wildcard '%') dibagian nama petugas atau nama aktivitas catatannya
    $query_str .= " AND (user_petugas LIKE '%$search%' OR aktivitas LIKE '%$search%')";
}

// [SINTAKS PHP]: Pemungkas String | Selalu memerintahkan MySQL untuk membariskan (ORDER BY) data urut mundur terbalik dari jejak waktu teranyar (WAKAR/DESC) paling atas
$query_str .= " ORDER BY waktu DESC";

// [SINTAKS PHP]: Eksekusi Final peluncuran Kalimat String Query Panjang ke Perut Database engine MySQL
$query = mysqli_query($koneksi, $query_str);
?>

<!-- [SINTAKS HTML]: Document Node Tree Standar Dasar -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas - Hogwarts Admin</title>
    <!-- [SINTAKS HTML]: Google font Eksternal Library - Varian Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* [SINTAKS CSS]: Konfigurasi CSS TEMA Hogwarts Merah Terpusat */
        * { box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex; 
            background: #f1f5f9; 
            color: #0f172a;
        }

        /* [SINTAKS CSS]: Sidebar Konsep Melayang Tinggi mentok (100vh) dan Tetap Terpaku Posisi (Fixed) Menahan Goncangan saat scroll mouse */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: #8f3434; /* Merah Gelap Bercampur Darah (Gryffindor Theme) */
            color: white; 
            position: fixed; 
            display: flex; 
            flex-direction: column; 
            padding: 20px 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header { text-align: center; padding: 20px; margin-bottom: 20px; }
        .sidebar-header img { width: 65px; margin-bottom: 10px; }
        
        /* [SINTAKS CSS]: Renggang Font Spasi Huruf Label Admin Panel */
        .sidebar-header h2 { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; margin: 0; }

        /* [SINTAKS CSS]: Link Navigasi Estetika Rapat Lembut tanpa Garis Bawah Bawaan */
        .sidebar a { 
            display: flex; align-items: center; color: rgba(255,255,255,0.8); 
            padding: 14px 25px; text-decoration: none; transition: 0.3s; font-size: 15px;
        }

        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }

        /* [SINTAKS CSS]: Anchor Merah Marun Terang untuk menyoroti jejak Page aktif Logs */
        .sidebar a.active { 
            background: #782626; color: white; border-radius: 0 50px 50px 0; /* Sudut Melengkung setengah bulat di pantat kanan */
            margin-right: 20px; font-weight: 600; 
        }

        /* [SINTAKS CSS]: Celah Konten Besar yang menghindari nabrak sidebar karena sifat sidebar melayang absolute fixed */
        .main-content { 
            margin-left: 260px; 
            padding: 40px; 
            width: 100%; 
        }

        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }

        /* [SINTAKS CSS]: Papan Custom Area Kotak Mesin Pencari Fleksibel */
        .filter-card {
            background: white; padding: 25px; border-radius: 16px; 
            display: flex; gap: 20px; margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
            border: 1px solid #e2e8f0;
        }

        .input-group { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .input-group label { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; }

        /* [SINTAKS CSS]: Batang Ketik teks Form */
        .input-group input { 
            padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; 
            font-family: inherit; font-size: 14px; outline: none; transition: 0.2s;
        }

        /* [SINTAKS CSS]: Focus Ring Pseudo Selector | Melingkari ujung sudut form menjadi merah marun bersinar ketika ujung pointer masuk klik ngetik text */
        .input-group input:focus {
            border-color: #8f3434;
            box-shadow: 0 0 0 3px rgba(143, 52, 52, 0.1);
        }

        /* [SINTAKS CSS]: Desain Rangka Bingkai Tabel Log Audit Modern Corner Radius Terpotong Overflow hiden */
        .log-container {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;
        }

        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; padding: 18px 25px; font-size: 12px; font-weight: 600;
            color: #475569; text-transform: uppercase; letter-spacing: 0.05em;
            background: #f1f5f9; border-bottom: 1px solid #f1f5f9;
        }

        td { 
            padding: 20px 25px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; 
        }

        /* [SINTAKS CSS]: Menggilas hilang garis celah di baris paling Bontot akhir (last-child selector modern) */
        tr:last-child td { border-bottom: none; }

        /* [SINTAKS CSS]: Lebar lebar spesifik porsi pembagian kolom wajar Tipe Tabel Fix Layout */
        .time-col { color: #64748b; width: 200px; }
        .user-col { font-weight: 700; color: #0f172a; width: 250px; }
        .activity-col { color: #475569; }

        tr:hover { background: #fcfcfd; }

        /* [SINTAKS CSS]: Hilangkan tampilan fisik UI Tombol Submit pencarian form karena fungsinya memutar JS Onchange di Element. */
        .btn-submit { display: none; }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- [SINTAKS HTML]: Komponen Header Panel Samping Kiri -->
        <div class="sidebar-header">
            <!-- [SINTAKS HTML]: Ikon statik Gambar Jpg Lokal -->
            <img src="../../hogwarts.jpg" alt="Logo">
            <h2>ADMIN PANEL</h2>
        </div>
        
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="data_user.php">👥 Data User</a>
        <!-- [SINTAKS HTML]: Aksen Sorotoan Khusus Link Log Aktivitas -->
        <a href="log_aktivitas.php" class="active">📜 Log Aktivitas</a>
        
        <div style="margin-top: auto;">
            <!-- [SINTAKS HTML]: Keluar Gerbang Utama Session Drop Pinggir warna Merah pucat mawar muda pink (!ffb1b1) -->
            <a href="../../auth/logout.php" style="color: #ffb1b1;">🚪 Logout</a>
        </div>
    </div>

    <!-- [SINTAKS HTML]: Layar Panel Pertunjukan Riwayat Sejarah Tengah Leluasa -->
    <div class="main-content">
        <div class="page-header">
            <h1>Log Aktivitas Sistem</h1>
        </div>

        <!-- [SINTAKS HTML]: <form GET> Formulir Injeksi URL. Menggantung variabel Filter_Tgl ke atas parameter Alamat Link browser ketika dipicu. Bertujuan untuk nge-Filter tanggal. Eksekusi ini ga rahasia jd pakai GET aja. -->
        <form method="GET">
            <div class="filter-card">
                <div class="input-group">
                    <label>Pilih Tanggal</label>
                    <!-- [SINTAKS HTML/JS/PHP]: Input Date picker calendar OS Browser beraliran OnChange Auto-Submit. Kalau Admin asal Ngeklik klik ganti Kalender, sistem otomatis reload narik Data DB baru pake JS (this.form.submit) tapa tombol pencetan manual! Nilai echo value nge-render data yg lagi aktif diseleksi saat ini.  -->
                    <input type="date" name="tanggal" value="<?= $filter_tgl ?>" onchange="this.form.submit()">
                </div>
                <div class="input-group">
                    <label>Cari Aktivitas / User</label>
                    <!-- [SINTAKS HTML/JS]: Tipe Box search string biasa buat nyari kalimat fuzzy LIKE Sql string pencocokan per hurup. Bisa dikentengin OnChange Submit tanpa kenal henti setiap ngetik juga -->
                    <input type="text" name="search" placeholder="Ketik kata kunci..." value="<?= $search ?>" onchange="this.form.submit()">
                </div>
            </div>
        </form>

        <!-- [SINTAKS HTML]: Blok Pembatas Radius Tumpul Frame Visualisasi Tabel Laporan Teks -->
        <div class="log-container">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User / Petugas</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // [SINTAKS PHP]: Evaluasi Eksistensi Rekaman Peristiwa. Memeriksa ketersediaan kuota minimal SATU rekaman sejarah SQL Query DB berjalan, sebelum dipaksakan melukis isi Data Array baris kolom table row. 
                    if(mysqli_num_rows($query) > 0):
                        // [SINTAKS PHP]: Loop Array Pengubah Wujud Fetch Tdk teratur dari MySQL Engine menjadi Kompartemen Variabel Row Tunggal Khusus Terbungkus Per Baris
                        while($row = mysqli_fetch_assoc($query)): 
                    ?>
                    <!-- [SINTAKS HTML]: Satu Strip Rangkaian Cerita Kejadian Aktivitas (Satu Baris) -->
                    <tr>
                        <!-- [SINTAKS PHP]: strtotime. Merekonstruksi String Bawaan Format Mesin Basis Data (contoh: 2024-05-18 10:45:00) yang kaku ke dalam format ramah baca otak manusia (Desember 24 Hari, 10:45) -->
                        <td class="time-col"><?= date('d M Y, H:i', strtotime($row['waktu'])) ?></td>
                        
                        <!-- [SINTAKS PHP]: Eksekusi Lempar Text String Murni Identitas Akun -->
                        <td class="user-col"><?= $row['user_petugas'] ?></td>
                        
                        <!-- [SINTAKS PHP]: Eksekusi lembar rekapan Deskripsi Kronologi singkat perilaku kegiatan akun ybs di masa lampau -->
                        <td class="activity-col"><?= $row['aktivitas'] ?></td>
                    </tr>
                    <?php 
                        // [SINTAKS PHP]: Akhir Penutup Kurung Silang Looping While Rentetan Kejadian
                        endwhile; 
                    
                    // [SINTAKS PHP]: Else Fallback | Mengatasi Skenario Mati jika Anggota Filter tanggal Tidak Valid / Kosong Kopong Total Datanya sama Sekali gak ada riwayat kejadian apapun
                    else:
                    ?>
                    <tr>
                        <!-- [SINTAKS HTML / CSS]: Colspan=3 Menyatukan 3 Petakan Kolom menengahi Teks Peringatan Ketiadaan Data ke Tengah Lebar Penuh (Supaya Gak Patah Struktur Layout) -->
                        <td colspan="3" style="text-align: center; padding: 50px; color: #64748b;">
                            Tidak ditemukan rekaman aktivitas.
                        </td>
                    </tr>
                    <?php 
                    // [SINTAKS PHP]: Limit Kurung Akhiran Penutup Siklus Pengevaluasi Ketersediaan Riwayat IF End.
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
