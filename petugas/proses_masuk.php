<?php
session_start();
// Pastikan path ke koneksi benar. Jika folder config sejajar dengan folder petugas:
include '../config/koneksi.php'; 
date_default_timezone_set('Asia/Jakarta');

if(isset($_POST['simpan'])) {
    $plat = mysqli_real_escape_string($koneksi, strtoupper($_POST['plat_nomor']));
    $id_tarif = $_POST['id_tarif'];
    $id_area = $_POST['id_area'];
    $waktu_masuk = date("Y-m-d H:i:s");
    
    // Ambil ID petugas dari session (sesuai database yang isinya angka 2, 3)
    $petugas = $_SESSION['id_user'] ?? '1'; 

    // Ambil nama jenis kendaraan dari tb_tarif supaya tidak NULL di database
    $q_tarif = mysqli_query($koneksi, "SELECT jenis_kendaraan FROM tb_tarif WHERE id_tarif='$id_tarif'");
    $d_tarif = mysqli_fetch_assoc($q_tarif);
    $jenis = $d_tarif['jenis_kendaraan'];

    // Simpan ke database (biaya_total set 0 untuk status 'masuk')
    $sql = "INSERT INTO tb_transaksi (plat_nomor, jenis_kendaraan, id_tarif, waktu_masuk, status, petugas, id_area, biaya_total) 
            VALUES ('$plat', '$jenis', '$id_tarif', '$waktu_masuk', 'masuk', '$petugas', '$id_area', '0')";
    
    if(mysqli_query($koneksi, $sql)) {
        $id_terakhir = mysqli_insert_id($koneksi);
        // Karena cetak_struk.php satu folder dengan file ini, jangan pakai ../
        header("location:cetak_struk.php?id=$id_terakhir");
        exit;
    } else {
        // Jika error, tampilkan di layar (biar tidak putih polos)
        die("Gagal simpan: " . mysqli_error($koneksi));
    }
}
?>