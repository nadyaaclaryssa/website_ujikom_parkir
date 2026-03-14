<p align="center">
  <img src="public/logoo.png" alt="Parline Logo" width="200" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 20px;" />
</p>

<h1 align="center">PARLINE</h1>

<p align="center">
  <strong>Sistem Manajemen Parkir Cerdas & Terintegrasi</strong><br/>
  Aplikasi web komprehensif untuk mendigitalisasi area parkir — mulai dari pencatatan check-in/out realtime, kalkulasi biaya otomatis, manajemen kapasitas slot, hingga pencetakan struk.
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
- [Panduan Sintaks PHP (Presentasi UKK)](#-panduan-sintaks-php-lengkap-untuk-presentasi)
- [Struktur Proyek](#-struktur-proyek)
- [Instalasi & Setup](#-instalasi--setup)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Lisensi](#-lisensi)

---

## 🏗️ Tentang Proyek

**PARLINE** adalah sistem manajemen parkir cerdas berbasis web yang direkayasa menggunakan tumpukan teknologi *Native* tertempa. Dibangun secara khusus sebagai solusi sistematis untuk **UKK RPL (Uji Kompetensi Keahlian Rekayasa Perangkat Lunak)**, aplikasi ini membawakan ekosistem Multi-User dengan batas otoritas yang terdefinisi kuat secara hierarkis (Role-Based Access Control) bagi **Admin**, **Petugas**, dan **Owner**.

### Mengapa PARLINE?

| Masalah Konvensional | Solusi Cerdas PARLINE |
|---|---|
| Pencatatan kertas rentan hilang & manipulasi | ✅ Data terenkripsi & tersimpan terpusat di Database |
| Sering cekcok tentang sisa slot parkir | ✅ Monitoring kapasitas Area parkir secara Real-Time |
| Kesalahan hitung tarif parkir manual | ✅ Kalkulasi harga otomatis melesat berdasarkan argo durasi |
| Sulit memantau rekap uang yang masuk | ✅ Dashboard agregat pendapatan visual & laporan tervalidasi PDF |
| Tidak ada kendali aktivitas pegawai | ✅ Rekaman Log aktivitas merekam setiap nafas interaksi sistem |

---

## ✨ Fitur Utama

### 👨‍💼 Admin (Pengendali Sistem)
- **Komando Pusat** — Dashboard visual pantauan metrik masuk/keluar kendaraan, dan pendapatan total.
- **Kelola Pengguna** — Modul komprehensif CRUD untuk mendaftarkan akun Petugas, Owner, dan Admin.
- **Manajemen Tarif** — Setir konfigurasi harga flat/jam untuk mobil, motor, maupun kendaraan spesifik lain.
- **Manajemen Area Parkir** — Ekspansi lantai parkir, kontrol digit kapasitas maksimum, dan purna pantau slot aktif.
- **Log Audit Terpusat** — Data *audit trail* transparan untuk mem-backtrace pergerakan seluruh *user*.

### 🧑‍🔧 Petugas (Garda Operasional)
- **Pintu Masuk (Check-In)** — Antarmuka input kilat pelat nomor & pemilihan spot parkir.
- **Pintu Keluar (Check-Out)** — Search engine cepat pelat nomor yang ditenagai mesin penghitung durasi parkir & matematis tagihan.
- **Receipt Generator** — Cetak Setruk termal fisik sebagai bukti sah transaksi pembayaran parkir.
- **Live Slot HUD** — Grid radar sisa slot parkir Real-Time membantu petugas memandu pengendara.
- **Quick History** — Antrean visibel riwayat instan mobil yang baru saja melintas.

### 💰 Owner (Pemangku Kepentingan)
- **Financial Dashboard** — View agregat dari kesehatan finansial omset (*Pendapatan Hari Ini vs Bulan Ini*).
- **Laporan Metrik** — Filter rentang (*date range*) kustom spesifik untuk menggali performa kas historis.
- **Diagram Pendapatan** — Bar Chart interaktif penterjemah arus kas pendapatan periodik.
- **Report Exporter** — Fungsionalitas konversi mutasi transaksi total dan ditarik menjadi arsip cetak (Print).

---

## 🛠️ Tech Stack

### Core Component
| Teknologi | Keterangan Fungsional |
|---|---|
| **PHP Native (8.x)** | Mesin server-side solid yang mengatur rute, data, logika, dan injeksi *backend*. |
| **MySQLi** | Sambungan jembatan *driver* data asinkronus ke mesin struktur relasional Database. |
| **HTML5** | Kanvas kerangka penopang elemen antarmuka browser. |
| **Vannila CSS3** | *Engine stylesheet* yang menjahit pesona UX premium bergaya paduan *Glass-morphism*. |

### Security & Libraries
| Alat/Metode | Deskripsi |
|---|---|
| **Chart.js / Canvas** | Alat rendering grafik persentase interaktif (Diagram Omset). |
| **Session Control** | Mesin kompartemenasi privasi berbasis server yang mengatur durasi masuk (*Login*). |
| **MD5 Cryptography** | Algoritma *One-way Hashing* pelerai teks sandi mentah pada basis data server MySQL. |

---

## 🗄️ Arsitektur Database

Data direlasikan kokoh pada **MySQL** yang menyatukan **7 Tabel Aktif**:

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
                └───────────────────┘     └──────────────────┘
```

---

## 📖 Panduan Sintaks PHP Lengkap (Untuk Presentasi)

Bagian khusus ini menyajikan *"Cheat Sheet"* krusial pembedah struktur kodingan yang dirancang untuk pendalaman materi saat ditanya Tim Penguji UKK:

### 1. Siklus Autentikasi (`session` & `header`)
- **`session_start();`** : Semboyan PHP dan harus di pucuk file. Perintah sakral pembuka gembok server agar ingat memori tentang *"Siapa User yang barusan Login?"*
- **`$_SESSION['role'];`** : Lemari kabinet sementara berkapasitas dinamis. Diisi tipe kekuasaan spesifik misal (`'petugas'` atau `'admin'`) yang menemani perjalanan sang user berpindah-pindah klik di semua web page.
- **`header("location: /auth/index.php");`** : Mesin *Direct-Teleportation* PHP. Jika user terdeteksi nakal menyusup tanpa tiket autentikasi, script ini memantulkannya mental paksa kembali ke ujung pintu awal seketika.

### 2. Pipa Komunikasi Database (`include`, `connect`, `query`)
- **`include 'config/koneksi.php';`** : Colokan jembatan universal. Memfasilitasi injeksi konfigurasi sandi Root Database kepada lembar manapun tanpa membuang tenaga untuk menuliskannya ulang terus berkali-kali (`DRY Principle`).
- **`mysqli_query($koneksi, "SINTAKS SQL");`** : Sang Kurir Eksekutor! Inilah senapan yang bertanggung jawab mutlak menjalankan permohonan Data entah itu ambil (**SELECT**), tembak (**INSERT**), mutasi (**UPDATE**), atau hapus (**DELETE**) ke dalam MySQL.
- **`mysqli_fetch_assoc($query_result);`** : Translator Data Array. Mengurai tumpukan obyek kriptikal mentah dari database untuk dipecah kembali ke susunan piring lauk murni *Key-Value* format (cth: `$plat['nopol']`) demi kebutuhan disajikan (*echo*) langsung ke elemen Label/Input HTML.
- **`mysqli_num_rows($query_result);`** : Kalkulator Pendeteksi. Mengembalikan sinyal absolut angka (`Integer`), misal `5`, ketika server diinstruksi menanyakan total baris riwayat transaksi *Bulan Ini*.

### 3. Sterilisasi Komunikasi & Keamanan (`$_POST`, `escape`)
- **`$_POST['nama_input'];`** : Jaringan terowongan belakang yang menampung dan menghisap nilai Form isian User tanpa ketahuan terekspos ke bar URL *Web Browser*.
- **`mysqli_real_escape_string($koneksi, $input);`** : Tameng Sterilisasi Data *Anti-Hacker*. Cairan antibodi otomatis pembersih kutip serampangan ( ` ' ` , ` " ` ) agar Database kita terlepas dari musibah suntikan injeksi paksa (`SQL Injection`).
- **`MD5($password);`** : Cincang Enkripsi Tulisan. Merusak kode sandi pengguna dan mengkonversinya jadi serangkaian gabungan alfanumerik panjang tidak wajar. Tujuan utamanya: Kalau Database diretas maling, si maling tidak berdaya membaca isi pelatuk sandi manusianya.

### 4. Trik Manuver Algoritma Terselubung
- **`mysqli_insert_id($koneksi);`** : Kompas Pencari Jejak PHP. Bila baru saja kita menembak (INSERT) data Pelanggan baru, sintaks akan secepat kilat meretrieve sisa *Primary Key AUTO_INCREMENT ID* terakhir dari pelanggan malang tersebut yang selanjutnya dilempar ke tabel *Detail Pembayaran* di sedetik kemudian secara berkelindan erat.
- **`date_default_timezone_set('Asia/Jakarta');`** : Relativator Ruang dan Waktu. Meretas ulang paksa sinkronisasi jam pasif dari cangkang *Motherboard Server* demi tunduk taat seutuhnya menyamai Waktu Indonesia Barat (WIB).

---

## 📂 Struktur Proyek

```text
ukk_aplikasi_parkir/
├── auth/                    # Area Modul Gerbang Autentikasi Keamanan
│   ├── index.php            # Halaman Antarmuka Login (UI Premium Front)
│   ├── proses_login.php     # Mesin Pengecek Kredensial & Pembangun Session 
│   └── logout.php           # Pemecah Gelombang Sesi & Pembersih Memori 
├── config/                  # Urat Syaraf Koneksi Utama
│   └── koneksi.php          # Titik Pusat Sambungan PDO / MySQLi ke Server
├── public/                  # Cawan Aset Penunjang Visual (Media)
│   └── ...                  # Brand Logo SVG, Icon Flat, Renderan Gambar
├── view/                    # Struktur Partisi Antarmuka Moduler (Pusat View)
│   ├── admin/               # Area Hak Akses Khusus Tertutup Level Sistem "Admin"
│   │   ├── dashboard.php    # Pusat Komando & Monitoring Area Macro
│   │   ├── kelola_area.php  # CRUD Eksekusi Penambah Slot Parkir Geografis
│   │   └── ...              # Modul Operasional Kelola User & Tabel Harga
│   ├── owner/               # Area Pantauan Pemangku Jabatan Investor (BOS)
│   │   ├── dashboard.php    # Panel View Grafik Profit Visual (Canvas Chart)
│   │   └── ...              # Partisi Pembukuan Analitik Rekapitulasi Final
│   └── petugas/             # Garis Depan Area Khusus Penjaga Loket Area
│       ├── dashboard.php    # Layar Real-time Sisa Slot Radar Geografis Lahan
│       ├── transaksi_*.php  # Gerbang Uji & Pengecekan Angka Plat Mobil (In/Out)
│       ├── proses_*.php     # Enjin Algoritma Eksternal Kalkulator Tagihan Jasa
│       └── cetak_*.php      # Layouting Kertas Struk Resmi POS Termal Print
├── index.php                # Jembatan Redirect Awal Pengguna Non-Otoritas Root
├── schema.db                # Kerangka Relasional Pola Master Pembentuk Database 
└── all_code.txt             # Berkas Pure Source Code Mentah Utuh Tanpa Komentar
```

---

## 🚀 Instalasi & Setup Lokal

Proyek aplikasi berbasis Web Server `XAMPP`/`MAMP` ini sangat efisien disiagakan dengan cepat:

1. **Unduh Proyek**: Pindahkan seluruh map (*folder*) Parline secara mandiri ke keruntuhan rekam laci Root Anda (contoh `C:/xampp/htdocs/` )
2. **Hidupkan Servis**: Akses ke antarmuka tombol *Control Panel XAMPP* dan **klik START** secara agresif pada servis layanan modul **Apache** lalu disusul **MySQL**.
3. **Bangun Data Basis**:
   - Ketik tajam URL di tab Browser: `http://localhost/phpmyadmin`
   - Buat satu bilik Data Basis baru beri julukan seragam: `ukk_parkir`.
   - Melalui Tab antarmuka sentral (Import), tembak muatkan berkas berekstensi `schema.db` agar sistem Database tergenerasi terstruktur presisi di sana.
4. **Testing Validasi Koneksi**: 
   - Bukalah paksa lembar pengarahan `config/koneksi.php`.
   - Telaah sejenak variabel spesifik perantara `$db` di sana wajib disetarakan telak memanggil nama Data Basis tadi ( `ukk_parkir` ).
5. **Kickstart Eksekusi**:
   - Terbirit-birit buka telunjuk kembali tuju jendela tab URL mu: `http://localhost/ukk_aplikasi_parkir`
   - Coblos masuk akses Identitias Sistem secara universal:
     - Username: `admin` | Password: `123`
     - Username: `petugas` | Password: `123`
     - Username: `owner` | Password: `123`

---

## 📄 Lisensi

Berlian Sistem Terintegrasi ini dimodelkan, diracik kode per kodenya, dan diukir semata-mata dengan tajam dan akurat secara mutlak demi menjawab tuntutan **Syarat Kelolosan Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak Nasional Tahun Ini**. Seluruh privasi model terlisensi pendidikan edukasi.

<p align="center">Made with ❤️ for UKK RPL </p>
