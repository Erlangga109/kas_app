<?php
include 'koneksi.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$stmt = $conn->prepare("DELETE FROM kas WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='index.php';</script>";
} else {
    echo "<script>alert('gagal hapus!: " . $conn->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
