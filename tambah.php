<?php
include 'koneksi.php';

// Struktur baru dengan tabel anak
$nama = $_POST['nama'] ?? '';
$jumlah = $_POST['jumlah'];

if (empty($nama) || empty($jumlah)) {
    echo "<script>alert('Nama anak dan jumlah harus diisi!'); window.history.back();</script>";
    exit;
}

// Cari anak_id berdasarkan nama
$stmt_anak = $conn->prepare("SELECT id FROM anak WHERE nama = ?");
$stmt_anak->bind_param("s", $nama);
$stmt_anak->execute();
$result_anak = $stmt_anak->get_result();

if ($result_anak->num_rows === 0) {
    echo "<script>alert('Nama anak tidak ditemukan! Silakan tambah anak terlebih dahulu.'); window.history.back();</script>";
    $stmt_anak->close();
    exit;
}

$row_anak = $result_anak->fetch_assoc();
$anak_id = $row_anak['id'];
$stmt_anak->close();

// Insert data kas baru
$stmt_insert = $conn->prepare("INSERT INTO kas (anak_id, jumlah) VALUES (?, ?)");
$stmt_insert->bind_param("ii", $anak_id, $jumlah);

if ($stmt_insert->execute()) {
    echo "<script>alert('Data berhasil disimpan!'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan data: " . $conn->error . "'); window.history.back();</script>";
}

$stmt_insert->close();
$conn->close();
?>
