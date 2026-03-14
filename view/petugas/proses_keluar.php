
<?php
// [SINTAKS PHP]: session_start() Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
// [SINTAKS PHP]: include | Memanggil koneksi database agar query dapat dieksekusi oleh file ini
include '../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (isset(// [SINTAKS PHP]: $_POST | Mengambil data POST dari antarmuka pengguna form
$_POST['cari_transaksi'])) { 
    $keyword = mysqli_real_escape_string($koneksi, strtoupper(// [SINTAKS PHP]: $_POST | Mengambil data POST dari antarmuka pengguna form
$_POST['keyword']));
    $waktu_keluar = date('Y-m-d H:i:s');

    // QUERY BARU: Cari berdasarkan plat_nomor di tb_kendaraan
    $sql = "SELECT t.*, k.plat_nomor, tr.tarif_per_jam 
            FROM tb_transaksi t 
            JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif 
            WHERE k.plat_nomor = '$keyword' AND t.status = 'masuk'
            ORDER BY t.id_parkir DESC LIMIT 1";
            
    $query = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, $sql);
    $data = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / mengambil sebaris record array berdasarkan key kolom asosiatif
mysqli_fetch_assoc($query);

    if ($data) {
        $id_parkir = $data['id_parkir'];
        
        // Hitung durasi jam
        $awal  = new DateTime($data['waktu_masuk']);
        $akhir = new DateTime($waktu_keluar);
        $diff  = $awal->diff($akhir);

        $jam = $diff->h + ($diff->days * 24);
        if ($diff->i > 0 || $diff->s > 0) { $jam++; } 
        if ($jam <= 0) { $jam = 1; }

        $total_bayar = $jam * $data['tarif_per_jam'];

        // UPDATE BARU: Simpan durasi_jam sesuai ERD
        $update = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, "UPDATE tb_transaksi SET 
                                          waktu_keluar = '$waktu_keluar', 
                                          durasi_jam = '$jam',
                                          biaya_total = '$total_bayar', 
                                          status = 'keluar' 
                                          WHERE id_parkir = '$id_parkir'");

        if ($update) {
            // Kurangi kapasitas terisi di area
            // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
mysqli_query($koneksi, "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = '{$data['id_area']}'");
            
            // [SINTAKS PHP]: header() | Pengalihan sistem otomatis (Redirect) ke modul terkait
header("location:cetak_struk_keluar.php?id=$id_parkir");
            exit;
        } else {
            echo "Gagal update: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>alert('Plat Nomor $keyword tidak ditemukan atau sudah Checkout!'); window.location='transaksi_keluar.php';</script>";
    }
} else {
    // [SINTAKS PHP]: header() | Pengalihan sistem otomatis (Redirect) ke modul terkait
header("location:transaksi_keluar.php");
    exit;
}
?>

