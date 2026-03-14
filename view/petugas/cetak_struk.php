
<?php
// [SINTAKS PHP]: include Memanggil koneksi database agar query dapat dieksekusi oleh file ini
include '../config/koneksi.php';

$id = isset(// [SINTAKS PHP]: $_GET | Menangkap parameter URL untuk memproses logic database spesifik
$_GET['id']) ? mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_GET | Menangkap parameter URL untuk memproses logic database spesifik
$_GET['id']) : 0;

// QUERY BARU: Menggabungkan transaksi, kendaraan, dan user
$query = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, u.nama_lengkap as petugas 
          FROM tb_transaksi t 
          JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
          JOIN tb_user u ON t.id_user = u.id_user
          WHERE t.id_parkir = '$id'"; 

$data = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, $query);
$r = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / mengambil sebaris record array berdasarkan key kolom asosiatif
mysqli_fetch_assoc($data);

if (!$r) {
    die("Error: Data parkir dengan ID $id tidak ditemukan.");
}
?>

