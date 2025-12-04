<?php
include 'koneksi.php';

// Cek struktur tabel kas
$check_kolom_anak_id = $conn->query("SHOW COLUMNS FROM kas LIKE 'anak_id'");
$kolom_anak_id_exists = $check_kolom_anak_id->num_rows > 0;

if ($kolom_anak_id_exists) {
    // Struktur baru
    $anak_id = $_POST['anak_id'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'] ?? '';

    if (empty($anak_id) || empty($jumlah)) {
        echo "<script>alert('Nama anak dan jumlah harus diisi!'); window.history.back();</script>";
        exit;
    }

    // Insert data kas baru
    $stmt_insert = $conn->prepare("INSERT INTO kas (anak_id, jumlah, keterangan) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("iis", $anak_id, $jumlah, $keterangan);

    if ($stmt_insert->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt_insert->close();
} else {
    // Struktur lama - untuk kompatibilitas
    $nama = $_POST['nama'] ?? '';
    $jumlah = $_POST['jumlah'];

    if (empty($nama) || empty($jumlah)) {
        echo "<script>alert('Nama dan jumlah harus diisi!'); window.history.back();</script>";
        exit;
    }

    $stmt_insert = $conn->prepare("INSERT INTO kas (nama, jumlah) VALUES (?, ?)");
    $stmt_insert->bind_param("si", $nama, $jumlah);

    if ($stmt_insert->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt_insert->close();
}

$conn->close();
?>
