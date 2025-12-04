<?php
include 'koneksi.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// Hapus semua transaksi kas yang terkait dengan anak ini terlebih dahulu
$stmt_delete_kas = $conn->prepare("DELETE FROM kas WHERE anak_id = ?");
$stmt_delete_kas->bind_param("i", $id);

if ($stmt_delete_kas->execute()) {
    // Setelah itu, hapus data anak
    $stmt_delete_anak = $conn->prepare("DELETE FROM anak WHERE id = ?");
    $stmt_delete_anak->bind_param("i", $id);
    
    if ($stmt_delete_anak->execute()) {
        echo "<script>alert('Anak dan semua transaksinya berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data anak: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt_delete_anak->close();
} else {
    echo "<script>alert('Gagal menghapus transaksi anak: " . $conn->error . "'); window.history.back();</script>";
}

$stmt_delete_kas->close();
$conn->close();
?>