<?php
include 'koneksi.php';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anak - Aplikasi Kas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h1>Tambah Anak Baru</h1>
    
    <form method="POST" class="form">
        <label>Nama Anak:</label>
        <input type="text" name="nama" placeholder="Masukkan nama anak" required>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Simpan</button>
            <a href="index.php" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
</body>
</html>