<p align="center">
  <img src="public/logoo.png" alt="Parline Logo" width="200" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 20px;" />
</p>

<h1 align="center">PARLINE 🚗🅿️</h1>

<p align="center">
  <strong>Sistem Manajemen Parkir Modern & Terintegrasi</strong><br/>
  Aplikasi web komprehensif untuk mendigitalisasi area parkir — mulai dari pencatatan check-in/out realtime, kalkulasi pendapatan otomatis, manajemen kapasitas slot, hingga pencetakan struk.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
</p>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Arsitektur Database](#-arsitektur-database)
- [Panduan Sintaks (Presentasi)](#-panduan-sintaks-php-lengkap-untuk-presentasi)
- [Struktur Proyek](#-struktur-proyek)
- [Instalasi & Setup](#-instalasi--setup)
- [Lisensi](#-lisensi)

---

## 🏗️ Tentang Proyek

**PARLINE** adalah sistem manajemen parkir cerdas berbasis web yang direkayasa secara murni menggunakan tumpukan teknologi asli (Native). Dibangun secara khusus sebagai solusi sistematis untuk **UKK RPL (Uji Kompetensi Keahlian Rekayasa Perangkat Lunak)**, aplikasi ini membawakan ekosistem Multi-User dengan batas otoritas yang terdefinisi kuat secara hierarkis (Role-Based Access Control) bagi **Admin**, **Petugas**, dan **Owner**.

### Mengapa Parline?

| Masalah Konvensional | Solusi Cerdas PARLINE |
|---|---|
| Pencatatan kertas yang gampang hilang | ✅ Data dienkripsi & tersimpan aman terpusat di Database |
| Sering cekcok tentang slot lahan kosong | ✅ Monitoring kapasitas Area parkir secara Real-Time |
| Kesalahan hitung biaya parkir manual | ✅ Kalkulasi argo otomatis berdasarkan durasi per jam |
| Tidak ada rekap uang yang masuk | ✅ Dashboard agregat pendapatan & laporan tervalidasi |
| Pegawai nakal yang curang | ✅ Log aktivitas merekam setiap nafas interaksi pada sistem |

---

## ✨ Fitur Utama

### 👨‍💼 Admin (Pengendali Sistem)
- **Komando Pusat** — Dashboard visual dengan metrik kendaraan, pendapatan total, dan tren log.
- **Kelola Pengguna** — Modul CRUD penuh untuk mengatur akun Petugas, Owner, dan Admin lainnya.
- **Manajemen Tarif** — Konfigurasi harga dasar dan harga progresif per jam untuk berbagai tipe kendaraan.
- **Manajemen Area Parkir** — Ekspansi wilayah parkir, kontrol kapasitas maksimum, dan monitor slot terisi.
- **Log Audit Keamanan** — Tabel transparan yang merekam seluruh histori pergerakan pengguna dalam sistem.

### 🧑‍🔧 Petugas (Garda Terdepan)
- **Pintu Masuk (Check-In)** — Antarmuka input kilat plat nomor, pencarian plat, dan lock area parkir.
- **Pintu Keluar (Check-Out)** — Search engine plat nomor tajam dengan mesin kalkulasi matematika durasi/biaya otomatis.
- **Receipt Generator** — Pencetakan struk termal resmi sebagai bukti legal transaksi parkir.
- **Live Slot Monitor** — Grid pantauan sisa kapasitas pada setiap lantai/area secara *head-up-display*.
- **Quick History** — Laporan ringkas riwayat transaksi yang baru saja diproses.

### 💰 Owner (Pemangku Kepentingan)
- **Financial Dashboard** — *Overhead view* dari kesehatan finansial parkiran (Pendapatan Hari Ini, Bulan Ini).
- **Laporan Analitik** — Filter pintar rentang tanggal untuk menggali performa kas historis.
- **Diagram Pendapatan** — Chart grafis memukau yang memetakan tren volume pemasukan dari waktu ke waktu.
- **Print PDF/Excel** — Fitur mengekspor rekapan finansial total langsung ditarik dari basis data.

---

## 🛠️ Tech Stack

### Core Component
| Teknologi | Keterangan Fungsional |
|---|---|
| **PHP Native (8.x)** | Mesin *backend* arsitektur server-side untuk me-routing *logic* & autentikasi. |
| **MySQLi** | Sambungan jembatan *driver* data langsung dari script PHP ke bongkahan Database. |
| **HTML5 / DOM** | Kerangka pondasi (*skeleton*) penyusun hierarki struktural elemen di *browser*. |
| **Vannila CSS3** | *Engine stylesheet* modern bergaya *Glass-morphism* dan *Fluid-gradient* premium. |

### Utility & Libraries
| Konsep / Library | Deskripsi |
|---|---|
| **Chart.js** | Alat *rendering* grafik persentase dan tren interaktif berbasis kanvas (HTML5 Canvas). |
| **Session Control** | Mesin *state-management* berbasis server untuk mengawal sesi masuk (Login). |
| **MD5 Hashing** | Algoritma *cryptography* satu arah pencegah kebocoran kata sandi mentah pada basis data. |

---

## 🗄️ Arsitektur Database

Sistem ini didesain efisien menggunakan **MySQL** dengan **7 Tabel Relasional**:

```text
┌──────────────────┐       ┌──────────────────┐
│    tb_user       │       │    tb_tarif      │
├──────────────────┤       ├──────────────────┤
│ id_user (PK)     │       │ id_tarif (PK)    │
│ nama_lengkap     │       │ jenis_kendaraan  │
│ username (UQ)    │       │ harga_per_jam    │
│ password (MD5)   │       │ tarif_per_jam    │
│ role (enum)      │       └────────┬─────────┘
│ status_aktif     │                │
└──┬───────┬───────┘                │
   │       │                        │
   │       │    ┌───────────────────┼──────────────────┐
   │       │    │  tb_transaksi     │                  │
   │       │    ├───────────────────┤                  │
   │       ├───►│ id_parkir (PK)    │                  │
   │       │    │ id_kendaraan (FK) │◄─────────────────┐
   │       │    │ id_tarif (FK)     │                  │
   │       │    │ id_user (FK)      │◄── Petugas       │
   │       │    │ id_area (FK)      │◄── Area Parkir   │
   │       │    │ waktu_masuk       │                  │
   │       │    │ waktu_keluar      │                  │
   │       │    │ durasi_jam        │                  │
   │       │    │ biaya_total       │                  │
   │       │    │ status (In/Out)   │                  │
   │       │    └───────────────────┘                  │
   │       │                                           │
   │       │    ┌───────────────────┐                  │
   │       └───►│ tb_kendaraan      ├──────────────────┘
   │            ├───────────────────┤
   │            │ id_kendaraan (PK) │
   │            │ plat_nomor        │
   │            │ jenis_kendaraan   │
   │            │ warna             │
   │            │ id_user (FK)      │
   │            └───────────────────┘
   │
   │            ┌───────────────────┐     ┌──────────────────┐
   └───────────►│ tb_log_aktivitas  │     │ tb_area_parkir   │
                ├───────────────────┤     ├──────────────────┤
                │ id_log (PK)       │     │ id_area (PK)     │
                │ id_user (FK)      │     │ nama_area        │
                │ aktivitas         │     │ kapasitas        │
                │ waktu_aktivitas   │     │ terisi           │
                └───────────────────┘     └──────────────?  │            │ warna             ?n   │    engkap (Untuk Presentasi)

Berikut adalah *"Cheat Sheet"* penjelasan bedah kodingan yang siap dipresentasikan di hadapan dosen penguji:

### 1. Siklus Autentikasi (`session` & `header`)
- **`session_start();`** : Semboyan mutlak PHP. Wajib ditaruh paling atas. Ini adalah kunci agar server bisa mengingat identitas siapa yang sedang Login.
- **`$_SESSION['role'];`** : Variabel wadah (*array* penyimpanan) memori sementara. Dipakai untuk menampung hak akses (Admin/Petugas) sampai _browser_ pengguna dimatikan.
- **`header("location: auth/index.php");`** : Fungsi *Direct-Teleportation* PHP. Jika user belum login, lemparkan paksa kembali ke ujung pintu halaman awal tanpa toleransi.

### 2. Pipa Database MySQLi (`include`, `connect`, `query`)
- **`include 'config/koneksi.php';`** : Semacam saklar colokan. Fungsi ini akan *menarik* script perihal Database lalu mencangkokkannya ke file saat ini, jadi kita bisa berinteraksi penuh dengan *tabel*.
- **`mysqli_query($koneksi, "SELECT * FROM ...");`** : Senjata utama (*Executor*). Inilah yang bertanggung jawab menembakkan berbagai perintah SQL (Ambil, Masukkan, Ubah, Hapus) ke dalam urat nadi Database.
- **`mysqli_fetch_assoc($query);`** : Sang "Penerjemah". Membongkar hasil tumpukan data dari hasil eksekusi (yang asalnya terenkripsi samar) memecahnya menjadi format Array (berpasangan rapi per-kolom) agar siap dicetak ke HTML.
- **`mysqli_num_rows($query);`** : Kalkulator digital otomatis pemandu validasi. Ia bertugas mendeteksi "ada berapa kardus data" yang berhasil ditarik dari eksekusi *Query*.

### 3. Keamanan *Bodyguard* (`$_POST`, `escape_string`)
- **`$_POST['keyword'];`** : Jaringan rahasia untuk menyedot input ketikan form HTML (misal Input Kolom Plat Nomor) yang berlalu lalang di *background*, tidak kasat mata lewat URL.
- **`mysqli_real_escape_string($koneksi, $input);`** : *Tameng Anti Hacker*. Sebuah semprotan desinfektan canggih. Ia bereaksi melumpuhkan dan mensterilisasi simbol-simbol aneh (`'`, `"`, dll) supaya database kita tidak bisa dijebol lewat trik *SQL Injection*.
- **`MD5($password);`** : Mesin penghancur dokumen jadi konfeti. Mengacak string tulisan kata sandi (misal `123`) menjadi - **`mysqli_num_rowsadi agar kalau database-nya bocor, wajah aslinya tidak terlihat.

### 4. Sinkronisasi Data Relasional Lanjutan
- **`mysqli_insert_id($koneksi);`** : Kemampuan Radar terbaru PHP. Jika kita baru saja "Meng-INSERT" data baru di suatu tabel, perintah ini akan sanggup melacak dan mencomot ID unik berantai urutan (AUTO INCREMENT) dari data yang baru di-input, berguna untuk *ForeignKey*.
- **`date_default_timezone_set('Asia/Jakarta');`** : Kompas waktu. Mereset total jam patokan di CPU server agar sepenuhnya seragam dan serempak dengan jam dinding Indonesia Barat (WIB).

---

## 📂 Struktur Proyek

```text
ukk_aplikasi_parkir/
├── auth/                    # Modul Gerbang Masuk & Keamanan
│   ├── index.php            # Halaman Antarmuka Login (UI Premium)
│   ├── proses_login.php     # Mesin Pengecek Kredensial & Pembuat Session
│   └── logout.php           # Mesin Penghancur Sesi & Pembersih Memori
├── config/                  # Inti Syaraf Koneksi
│   └── koneksi.php          # Titik Pusat Sambungan ke Database MySQL
├── public/                  # Gudang Aset Pendukung Web
│   └── ...                  # Logo, Ikon, Gambar Raster
├── view/                    # Struktur Direktori Modul Tampilan (Views)
│   ├── admin/               # Halaman Tertutup Khusus Level Admin
│   │   ├── dashboard.php    # Pusat Komando & Monitoring Area
│   │   ├── kelola_area.php  # CRUD Eksekusi Penambah Slot Parkir
│   │   └── ...              # Modul Manajemen User & Tarif Harga
│   ├── owner/               # Halaman Tertutup Khusus Level Owner
│   │   ├── dashboard.php    # Panel Agregat Keuangan Visual (Chart)
│   │   └── ...              # Modul Rekapitulasi Pembukuan & Cetak
│   └── petugas/             # Halaman Transaksional Operasional Petugas
│       ├── dashboard.php    # Tampilan Sisa Slot Parkir Live (Head-up)
│       ├── transaksi_*.php  # Gerbang Cek Ketersediaan Plat Mobil (Masuk/Keluar)
│       ├── proses_*.php     # Mesin Pengeksekusi Data Validasi & Biaya 
│       └── cetak_*.php      # Template Kertas Struk Bukti Parkir Format Print
├── index.php                # Jembatan Redirect Otomatis Pertama Kali (Root)
└── schema.db                # Kerangka Dasar Cetak Biru (Blueprint) Database MySql
```

---

## 🚀 Instalasi & Setup (Localhost)

Aplikasi dibangun untuk arsitektur server lokal *XAMPP/MAMP*.

1. **Unduh Proyek**: Pindahkan seluruh folder ke dalam root direktori web server (contoh: `htdocs` pada XAMPP).
2. **Aktifkan Servis**: Buka aplikasi *Control Panel XAMPP* dan nyalakan (Start) modul **Apache** dan **MySQL**.
3. **Impor Database**:
   - Buka penjelajah *phpMyAdmin* secara lokal di browser (`http://localhost/phpmyadmin`).
   - Buat satu database baru letakkan nama, contoh: `ukk_parkir`.
   - Gunakan fitur tab **Import** untuk menyuntikkan isi script yang ada di dalam berkas `schema.db` ke database tersebut.
4. **Validasi Koneksi**: 
   - Buka file `config/koneksi.php`.
   - Pastikan variabel perantara `$db` selaras dengan nama database yang Anda buat (misal: `ukk_parkir`).
5. **Akses Sistem**:
   - Buka tab baru penjelajah browser Anda ketik alamat: `http://localhost/ukk_aplikasi_parkir`
   - Gunakan Identitas Standar:
     - Username: `admin` | Password: `123`
     - Username: `petugas` | Password: `123`
     - Username: `owner` | Password: `123`

---

## 📄 Lisensi

Proyek aplikasi terintegrasi ini dirancang, ditulis kodenya, dan diajukan khusus untuk menjawab **Syarat Prasyarat Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak Tahun Ajaran Terbaru**. Seluruh hak distribusi internal akademik dipelihara.
