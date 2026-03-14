
<?php
// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
// [SINTAKS PHP]: include | Memanggil koneksi database agar query dapat dieksekusi oleh file ini
include '../config/koneksi.php'; 
date_default_timezone_set('Asia/Jakarta');

if(isset(// [SINTAKS PHP]: $_POST | Mengambil data POST dari antarmuka pengguna form
$_POST['simpan'])) {
    $plat = mysqli_real_escape_string($koneksi, strtoupper(// [SINTAKS PHP]: $_POST | Mengambil data POST dari antarmuka pengguna form
$_POST['plat_nomor']));
    $id_tarif = // [SINTAKS PHP]: $_POST | Mengambil data POST dari antarmuka pengguna form
$_POST['id_tarif'];
    $id_area = // [SINTAKS PHP]: $_POST | Mengambil data POST dari antarmuka pengguna form
$_POST['id_area'];
    $waktu_masuk = date("Y-m-d H:i:s");
    
    // Ambil ID petugas dari session
    $id_user = $_SESSION['id_user'] ?? 1; 

    // 1. Ambil nama jenis kendaraan dari tb_tarif
    $q_tarif = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, "SELECT jenis_kendaraan FROM tb_tarif WHERE id_tarif='$id_tarif'");
    $d_tarif = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / mengambil sebaris record array berdasarkan key kolom asosiatif
mysqli_fetch_assoc($q_tarif);
    $jenis = $d_tarif['jenis_kendaraan'] ?? 'lainnya';

    // 2. Insert ke tb_kendaraan dulu
    // Karena di form belum ada input warna dan pemilik, kita isi strip (-) dulu
    $sql_kendaraan = "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, id_user) 
                      VALUES ('$plat', '$jenis', '-', '-', '$id_user')";
    
    if(// [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, $sql_kendaraan)) {
        
        // 3. Ambil id_kendaraan yang barusan otomatis dibuat oleh database
        $id_kendaraan = mysqli_insert_id($koneksi);

        // 4. Insert ke tb_transaksi (Perhatikan nama kolomnya sudah pakai id_parkir & id_kendaraan)
        $sql_transaksi = "INSERT INTO tb_transaksi (id_kendaraan, waktu_masuk, id_tarif, id_area, id_user, status, biaya_total) 
                          VALUES ('$id_kendaraan', '$waktu_masuk', '$id_tarif', '$id_area', '$id_user', 'masuk', 0)";
        
        if(// [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, $sql_transaksi)) {
            // Ambil id_parkir untuk dicetak di struk
            $id_parkir = mysqli_insert_id($koneksi);
            
            // 5. Update jumlah terisi di tb_area_parkir
            // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, "UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area = '$id_area'");

            // Lempar ke halaman cetak struk
            // [SINTAKS PHP]: header() | Pengalihan sistem otomatis (Redirect) ke modul terkait
header("location:cetak_struk.php?id=$id_parkir");
            exit;
        } else {
            die("Gagal simpan transaksi: " . mysqli_error($koneksi));
        }
    } else {
        die("Gagal simpan kendaraan: " . mysqli_error($koneksi));
    }
}
?>

