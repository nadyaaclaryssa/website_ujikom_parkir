<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/tarif_index.php
// -> Tujuan Spesifik: Modul/komponen List Tarif (Hanya Menampilkan Data Saja) Alternatif.
// ======================================

// [SINTAKS PHP]: session_start() | Me-resume aktivitas identifikasi session
session_start();

// [SINTAKS PHP]: Pemeriksaan hak previlege khusus admin
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: Lemparan Pulang ke Form Login
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Pemanggil script penengah jembatan aplikasi dengan database 
include '../../config/koneksi.php';

// [SINTAKS PHP]: Tembakan bahasa kueri SQL SELECT ALL untuk memuntahkan seluruh baris tanpa sisa dari tabel tarif
$query = mysqli_query($koneksi, "SELECT * FROM tb_tarif");

// [SINTAKS PHP]: Instalasi Deklarasi Variabel Penampung Kosong ber-tipe data Array List
$semua_tarif = [];

// [SINTAKS PHP]: Mesin Pencacah Loop While | Mengurai gumpalan hasil DB menjadi satuan Array Asosiatif per baris per putaran
while($row = mysqli_fetch_assoc($query)){
    // [SINTAKS PHP]: Array Push Otomatis | Memasukkan satu baris array dari DB ke Index paling buntut array $semua_tarif secara terus menerus sampe ludes
    $semua_tarif[] = $row;
}
?>
<!-- [SINTAKS HTML]: Kerangka Skeleton Struktur Awal Web -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    
    <!-- [SINTAKS HTML]: Meta Viewport | Syarat Wajib Membuat Website Responsive (Otomatis menyesuaikan rasio HP vs Desktop PC) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tarif - Hogwarts Admin</title>
    
    <!-- [SINTAKS HTML]: Menyedot API Font Gratis dari perbendaharaan Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* [SINTAKS CSS]: Selektor Global (Bintang) untuk mereset tingkah laku box model default browser menjadi border-box yang presisi ukurannya */
        * { box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex; /* Format Grid 1 Dimensi untuk Penyekatan Papan Kiri Sidebar vs Papan Kanan Konten */
            background: #f1f5f9; 
            color: #0f172a;
        }

        /* [SINTAKS CSS]: Kostumisasi Kotak Laci Susunan List Menu Sebelah Kiri layar */
        .sidebar { 
            width: 260px; 
            height: 100vh; /* Tinggi Penuh memakan layar viewport vertikal */
            background: #8f3434; /* Warna dasar Cat Merah Marun bata */
            color: white; 
            position: fixed; /* Posisi Kaku Tak kan Bergeser wlaupun layarnya panjang ke scroll turun */
            display: flex;
            flex-direction: column; /* Mengalihkan Flexbox dari normalnya mendatar menjadi Menurun Ke Bawah (Atas-ke-Bawah) */
            padding: 20px 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1); /* Bayangan Halus menepi ke arah kanan memisahkan border dengan konten putih */
        }

        .sidebar-header { text-align: center; padding: 20px; margin-bottom: 20px; }
        .sidebar-header img { width: 60px; margin-bottom: 10px; }
        .sidebar-header h2 { font-size: 18px; text-transform: uppercase; letter-spacing: 2px; color: #ffffff; margin: 0; }

        /* [SINTAKS CSS]: Tali penghubung Antar Halama Lainya Di sidebar */
        .sidebar a { 
            display: flex; align-items: center; color: rgba(255,255,255,0.8); 
            padding: 14px 25px; text-decoration: none; /* Hilangin garis Ciri Khas bawaan href Biru */
            transition: all 0.3s; /* Efek memudar lambat 0.3 detik setiap diklik/dideketin kursor tikus Hover */
            font-size: 15px;
        }

        /* [SINTAKS CSS]: Interaksi Sentuh Udara Mouse (Hover) - Memberikan Feedback Visual ke pengguna Aplikasi kalau ini tuh Tombol bisa Diklik Lho! */
        .sidebar a:hover { 
            background: rgba(255,255,255,0.1); 
            color: white; 
            padding-left: 30px; /* Trik Unik Animasi teks seolah-olah Maju ke kanan sedikit! */
        }

        .sidebar a.active {
            background: #782626; color: white;
            border-radius: 0 50px 50px 0; /* Potongan Membulat Pil obat setengah pada sisi kanan link text */
            margin-right: 20px; font-weight: 600;
        }

        /* [SINTAKS CSS]: Wadah Utama yang luas Lega di area kanan bebas (Padding didorong ke Kanan Menghindari Nabrak Sidebar Fixed) */
        .content { margin-left: 260px; padding: 40px; width: 100%; }

        h2 { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 30px; }

        /* [SINTAKS CSS]: Penyekat Lahan Tabel agar rapih punya border batasannya ngga polosan membaur sama Background utama. Dilengkapi efek Sudut membulat */
        .table-container {
            background: white; border-radius: 16px; overflow: hidden; /* Overflow dipangkas, kl ada anak dalem tabel yg bentuknya kotak tajam ga membulat ntar otomatis kepotong melengkung jg dr luar!  */
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 900px; 
        }

        table { width: 100%; border-collapse: collapse; /* Menyilet hilangkan Celah/Tembok ganda spasi bawaan tabel pure Standar W3C supaya Rapat dempet estetik modern */ }
        
        /* [SINTAKS CSS]: Th (Table Header) Sel Mewakili Nama Kepala Kolom List Data */
        th { 
            background: #8f3434; color: #ffffff; font-weight: 600; text-transform: uppercase; 
            font-size: 12px; letter-spacing: 0.05em; padding: 16px 20px; text-align: left; border-bottom: 1px solid #f1f5f9;
        }

        /* [SINTAKS CSS]: Td (Table Data) Sel Badan yang menampung Isi Value Asli Database MySQL-Nya Disini */
        td { padding: 16px 20px; color: #0f172a; font-size: 14px; border-bottom: 1px solid #f1f5f9; }

        tr:last-child td { border-bottom: none; }
        tr:hover { background: #fcfcfd; }

        /* [SINTAKS CSS]: Label Text Link Untuk Memanggil Ke Halaman Lain Punya Gaya Bebas Warnanya Ungu Violet */
        .btn-edit { color: #8f3434; font-weight: 600; text-decoration: none; font-size: 13px; }
        .btn-edit:hover { text-decoration: underline; color: #8d2e2e; }

        .price { font-weight: 600; color: #0f172a; }
    </style>
</head>
<body>
    
    <!-- [SINTAKS HTML]: Navigasi Panel Kompartemen -->
    <div class="sidebar">
        <div class="sidebar-header">
            <!-- [SINTAKS HTML]: Gambar Imagemaker Lambang Universitas Hogwarts Asrama Gryfindor  -->
            <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
            <h2>Admin</h2>
        </div>
        <a href="dashboard.php">Dashboard</a>
        <a href="user_index.php">Kelola User</a>
        <!-- [SINTAKS HTML]: Menyajikan Teks Berwarna Paling Terang sbg Penanda Lokasi Pijakan Kaki Sekarang Di Menu Taris-->
        <a href="tarif_index.php" class="active">Tarif Parkir</a>
        <a href="area_parkir.php">Area Parkir</a>
        <a href="../../auth/logout.php" style="margin-top: auto; color: #ffb1b1;">Logout</a>
    </div>

    <!-- [SINTAKS HTML]: Casing Utama Area Sebelah Kanan Render DOM -->
    <div class="content">
        <h2>Pengaturan Tarif Parkir</h2>
        
        <div class="table-container">
            <table>
                <!-- [SINTAKS HTML]: THEAD Grup Penyatuan Baris Sel Teratas berisi Label Semantik Field Names -->
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif / Jam</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <!-- [SINTAKS HTML]: TBODY Wadah Menampung Ekstrak Data-Data Riil dari Database -->
                <tbody>
                    <?php 
                    // [SINTAKS PHP]: Indikator Counter Nomer Urut Tabel Biasa yang gak ngandelin ID Database Yg suka berantakan angkanya.
                    $no=1; 
                    
                    // [SINTAKS PHP]: Foreach Loop | Perulangan Mesin Pintar Untuk Mencacah/Menggigit Isi Tumpukan Array PHP menjadi satuan data yg lebih kecil sampai habis.
                    foreach($semua_tarif as $t): 
                    ?>
                    
                    <!-- [SINTAKS HTML]: TR (Table Row) Satu balokan garis memanjang horisontal penyekat data Tabel -->
                    <tr>
                        <td style="color: #64748b; width: 50px;"><?= $no++; ?></td>
                        
                        <!-- [SINTAKS PHP]: Inline CSS Manipulasi Text Capitalize men-kapitalkan Huruf Besar Awal Kata Saja (Bukan Sepenuhnya Huruf besar semua kyk strtoupper)  -->
                        <td style="font-weight: 500; text-transform: capitalize;">
                            <?php 
                                // [SINTAKS PHP]: Nested If Pattern Matching | Percabangan String Search Engine Pencari Kata Kunci (strpos), Jika Didapatkan kata Sakti (Motor/Mobil) yang di Ubah Standarkan ke Huruf kecil (strtolower) Maka Eksekusi Emoji Sederhana HTML. 
                                if(strpos(strtolower($t['jenis_kendaraan']), 'motor') !== false) {
                                    echo " ";
                                } else if(strpos(strtolower($t['jenis_kendaraan']), 'mobil') !== false) {
                                    echo " ";
                                } else if(strpos(strtolower($t['jenis_kendaraan']), 'lainnya') !== false) {
                                    echo " ";
                                }
                                
                                // [SINTAKS PHP]: Echoing Value / Mengetikan kembali Hasil Nama asli Kendaraanya stelah ditempel stiker Emoji .
                                echo $t['jenis_kendaraan']; 
                            ?>
                        </td>
                        
                        <!-- [SINTAKS PHP]: Number Format 0 Desima pemisal koma, dan pemisah ribuan titik khas Akuntansi Indo -->
                        <td class="price">Rp <?= number_format($t['tarif_per_jam'], 0, ',', '.'); ?></td>
                        
                        <!-- [SINTAKS HTML]: Kolom Sel untuk Wadah Anchor Tautan Link Tembak Edit -->
                        <td style="text-align: center;">
                            <!-- [SINTAKS HTML PHP]: Anchor Href Edit Membawa Muatan Parameter Beban GET ID Rahasia "?id=99" untuk dititpkan dan ditangkap di halaman Tarif_Edit.php Supaya sistem tau mana yang mau diedit harganya jgn sampe ketuker semua record ikut kerubah!  -->
                            <a href="tarif_edit.php?id=<?= $t['id_tarif']; ?>" class="btn-edit">Ubah Tarif</a>
                        </td>
                    </tr>
                    
                    <?php 
                    // [SINTAKS PHP]: Pertanda Batas Tutupan Foreach Loop Kurung Kurawa.
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
