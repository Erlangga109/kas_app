<?php
include 'koneksi.php';

$nama = $_POST['nama'];

if (empty($nama)) {
    echo "<script>alert('Nama anak harus diisi!'); window.history.back();</script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO anak (nama) VALUES (?)");
$stmt->bind_param("s", $nama);

if ($stmt->execute()) {
    echo "<script>alert('Anak berhasil ditambahkan!'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Gagal menambahkan anak: " . $conn->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>