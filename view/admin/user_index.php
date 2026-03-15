<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/user_index.php
// -> Tujuan Spesifik: Modul/komponen Menampilkan Daftar Akun Sistem (User, Admin, Petugas, Eksekutif dll).
// ======================================

// [SINTAKS PHP]: session_start() | Memastikan Kuki Pelacak Log-in bekerja di halaman ini
session_start();

// [SINTAKS PHP]: Proteksi Lapis Privilese | Menyeleksi apakah Kasta Role Sesi User yg mampir ke Hal Ini Benar-benar Dewa Admin Sistem Tertinggi apa hanya Kasir bias 
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: Tendang Paksa Ke form otentikasi
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Include Kawat Penyambung Konektivitas Port Ke Mesin SQL Server.
include '../../config/koneksi.php';

// [SINTAKS PHP]: Sedot Data Total SQL | Mengajurkan Penyeretan Seluruh Array List Record Pemetaan Data Pegawai di DB. Ditambah Perintah ORDER BY DESC buat Ngurutin dari yg paling Buntut Terbaru dulu!
$query = mysqli_query($koneksi, "SELECT * FROM tb_user ORDER BY id_user DESC");

// [SINTAKS PHP]: Kriteria Penilaian UKK (Array Binding Data Map)
$users = [];

// [SINTAKS PHP]: While Loop Tarik Ulur | Terus Menerus Mengais sisa Row Database yg masi ada belom difetch jadi Object Associative array PHP (Dicicil 1 per 1 Baris)
while($row = mysqli_fetch_assoc($query)){
    // [SINTAKS PHP]: Operasi Push Array (Memasukan Item Objek Array Baru ke Kotakan Kosong Array Induk "$users" dipaling belakang index)
    $users[] = $row;
}
?>
<!-- [SINTAKS HTML]: Penampang Template Dokumen Visual Tag Root Utama HTML-->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Hogwarts Admin</title>
    <!-- [SINTAKS HTML]: Font External Injector Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* [SINTAKS CSS]: Penyesuaian Reset Ukuran Tdk Molor Margin */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex; 
            background: #f1f5f9; 
            color: #0f172a;
        }

        /* [SINTAKS CSS]: Sidebar Styling - Tema Hogwarts Marun Tua  */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: #8f3434; 
            color: white; 
            position: fixed; /* Berdiri Tetap tegak melayang menembus panjang halaman document scroling */
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header { text-align: center; padding: 20px; margin-bottom: 20px; }
        .sidebar-header img { width: 60px; margin-bottom: 10px; }
        .sidebar-header h2 { font-size: 18px; text-transform: uppercase; letter-spacing: 2px; color: #ffffff; margin: 0; }

        .sidebar a { 
            display: flex; align-items: center; color: rgba(255,255,255,0.8); 
            padding: 14px 25px; text-decoration: none; transition: all 0.3s; font-size: 15px;
        }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; padding-left: 30px; }
        .sidebar a.active { background: #782626; color: white; border-radius: 0 50px 50px 0; margin-right: 20px; font-weight: 600; }

        /* [SINTAKS CSS]: Content Area Bebas yg ditarik mundur 260 Pixel ke Kanan via Margin_Left menghindari nabrak menimpa si Sidebar Fixed td! */
        .content { margin-left: 260px; padding: 40px; width: 100%; }

        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h2 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }

        /* [SINTAKS CSS]: Button Styling Model Pil Tumpul Modern Tombol Klik Plus Buat Tambah Staff Anyar Baru Keras*/
        .btn { 
            padding: 10px 20px; border-radius: 10px; text-decoration: none; 
            font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; transition: all 0.3s;
        }
        .btn-tambah { background: #8f3434; color: white; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4); }
        .btn-tambah:hover { background: rgb(92, 24, 24); transform: translateY(-2px); /* Trik Geser Sumbu Y Titik koding Float Hover Animation  */ }

        /* [SINTAKS CSS]: Table Styling Modern Kotak Sudut halus Tepi Border  */
        .table-container { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #8f3434; color: #ffffff; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.05em; padding: 16px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        td { padding: 16px 20px; color: #0f172a; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #fcfcfd; }

        /* [SINTAKS CSS]: Badge Status Kapsul Warnawarni menandai nyala matinya posisi Karyawan Aktif/Keluar DB */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        
        /* [SINTAKS CSS]: Lencana Hijau Subur Tanda Dia Masi Nguli dstu blm pecat DB */
        .badge-aktif { background: #dcfce7; color: #166534; }
        /* [SINTAKS CSS]: Lencana Merah Mawar Tanda Karyawan Ga aktif Non-Aktif/Banned */
        .badge-non { background: #fee2e2; color: #991b1b; }

        /* [SINTAKS CSS]: Anchor Button Links Action Control Panel Cell TR Bawah Kanan Delete dll  */
        .btn-edit { color: #6366f1; margin-right: 15px; }
        .btn-edit:hover { text-decoration: underline; }
        .btn-hapus { color: #ef4444; }
        .btn-hapus:hover { text-decoration: underline; }
    </style>
</head>
<body>
    
    <!-- [SINTAKS HTML]: Navigasi Kiri Laci (Side Panel) -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
            <h2>Admin</h2>
        </div>
        
        <!-- [SINTAKS HTML]: Menu Anchor Nav Link Lists -->
        <a href="dashboard.php">Dashboard</a>
        <a href="user_index.php" class="active">Kelola User</a>
        <a href="tarif_index.php">Tarif Parkir</a>
        <a href="area_index.php">Area Parkir</a>
        
        <div style="margin-top: auto;">
            <a href="../../auth/logout.php" style="color: #ffb1b1;">Logout</a>
        </div>
    </div>

    <!-- [SINTAKS HTML]: Ruang Render List Tabel Sebelah Kanan (Main Panel)-->
    <div class="content">
        <!-- [SINTAKS HTML]: Wadah FlexBox penjajar H2 Tulisan Sama Tombol Biar Sebelahan Kanan Kiri Sejajar Indah Rapi Horizontal Lurus -->
        <div class="header-flex">
            <h2>Daftar Pengguna</h2>
            
            <!-- [SINTAKS HTML]: Link Direct Menuju Page Formulir Tambah Rekrut Akun Staff Karyawan Baru -->
            <a href="user_tambah.php" class="btn btn-tambah">+ Tambah User</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <!-- [SINTAKS HTML]: Table Header Cells List Atribut Profil Akun Db  -->
                        <th>No</th><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // [SINTAKS PHP]: Pencacah Nomber Numerator Palsu Tabel (Pilihan Cerdas Daripada Make Asli ID yg Angkanya suka Acak acak longkap2 gr gr riwayat hapus record sblomnya)
                    $no = 1;
                    
                    // [SINTAKS PHP]: Foreach Blok Perulangan (Mirip While cm beda style doang, lbh luwes dibaca sintaksnya foreach per item) Mengurai Data Array Collection PHP 
                    foreach($users as $u): 
                    ?>
                    <!-- [SINTAKS HTML]: TR Table Record Row Baris List Pegawai Turunan DB Loop-->
                    <tr>
                        <!-- [SINTAKS PHP]: Print Angka Nol Bertumbuh 1 demi 1 (PlusPlus Increment $no++) -->
                        <td><span style="color: #64748b; font-weight: 600;"><?= $no++; ?></span></td>
                        
                        <!-- [SINTAKS PHP]: Membingkai Print Teks Nama dan User -->
                        <td style="font-weight: 500;"><?= $u['nama_lengkap']; ?></td>
                        <td style="color: #475569;"><?= $u['username']; ?></td>
                        
                        <td>
                            <!-- [SINTAKS PHP]: UcFIrst() Modifikator kapital tipografi huruf terdepan kata (misal 'petugas'->'Petugas')  -->
                            <span style="font-size: 13px;"><?= ucfirst($u['role']); ?></span>
                        </td>
                        
                        <td>
                            <?php 
                            // [SINTAKS PHP]: Percabangan Evaluasi boolean (True/False Binary 1 / 0) apakah Atribut db Tabel Tunjukin status doi Aktif kerja / Suspend NonAktif. 
                            if($u['status_aktif'] == 1): ?>
                                <!-- [SINTAKS HTML]: Render HTML ini kalo syart Terpenuhi Aktif ID=1 -->
                                <span class="badge badge-aktif">Aktif</span>
                            <?php else: ?>
                                <!-- [SINTAKS HTML]: Render HTML Merah Ini kalo Status Drop Selain 1 Alias Dipecat Suspend ID=0 -->
                                <span class="badge badge-non">Non-Aktif</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <!-- [SINTAKS HTML/PHP]: Konfigurasi HyperLink Lempar Param "?id" yang meruncing Langsung Kepada Nomor Urut Ktp (ID) Data di Baris Tersebut Khusus! Ini Penting Biar gk ketuker hapus akun bos owner klau asal asalan ngebenam  -->
                            <a href="user_edit.php?id=<?= $u['id_user']; ?>" class="btn-edit">Edit</a>
                            
                            <!-- [SINTAKS HTML/JS]: Trik Klasik Mengawinkan PHP dgn Alert JS. (OnClick Return Confirm). Konfirmasi Popup di Browser Keluar nanya yakin/tidak Membasi Parameter Nama Doi Yg di Echo! Seru buat interaksi. -->
                            <a href="user_hapus.php?id=<?= $u['id_user']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus si <?= $u['nama_lengkap']; ?>?')">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                    // [SINTAKS PHP]: Engsel Penutup Akhiran dari Foreach Loop List Array DB.
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
