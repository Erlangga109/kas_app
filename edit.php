<?php
include 'koneksi.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_baru = $_POST['jumlah_baru'];
    $keterangan_baru = $_POST['keterangan_baru'];

    if (empty($jumlah_baru)) {
        echo "<script>alert('Jumlah tidak boleh kosong!'); window.history.back();</script>";
    } else {
        $stmt = $conn->prepare("UPDATE kas SET jumlah = ?, keterangan = ? WHERE id = ?");
        $stmt->bind_param("isi", $jumlah_baru, $keterangan_baru, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Data kas berhasil diubah!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah data: " . $conn->error . "'); window.history.back();</script>";
        }
        $stmt->close();
    }
} else {
    // Cek apakah kolom keterangan ada di tabel kas
    $check_kolom_keterangan = $conn->query("SHOW COLUMNS FROM kas LIKE 'keterangan'");
    $kolom_keterangan_exists = $check_kolom_keterangan->num_rows > 0;

    if($kolom_keterangan_exists) {
        $query = "SELECT k.jumlah, k.keterangan, a.nama FROM kas k JOIN anak a ON k.anak_id = a.id WHERE k.id = ?";
    } else {
        $query = "SELECT k.jumlah, k.nama as keterangan, a.nama FROM kas k JOIN anak a ON k.anak_id = a.id WHERE k.id = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $jumlah_lama = $row['jumlah'];
        $keterangan_lama = $row['keterangan'];
        $nama_anak = $row['nama'];
    } else {
        echo "<script>alert('Data tidak ditemukan: " . $conn->error . "'); window.location='index.php';</script>";
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kas</title>
    <link rel="stylesheet" href="assets/style.css?v=1.1">
</head>
<body>
<div class="container">
    <h1>Edit Kas</h1>

    <form action="edit.php?id=<?= $id; ?>" method="POST" class="form">
        <label>Nama Anak:</label>
        <input type="text" value="<?= htmlspecialchars($nama_anak); ?>" disabled>
        <label>Jumlah Lama:</label>
        <input type="text" value="<?= htmlspecialchars($jumlah_lama); ?>" disabled>
        <label>Jumlah Baru:</label>
        <input type="number" name="jumlah_baru" value="<?= htmlspecialchars($jumlah_lama); ?>" required>
        <label>Keterangan Lama:</label>
        <input type="text" value="<?= htmlspecialchars($keterangan_lama); ?>" disabled>
        <label>Keterangan Baru:</label>
        <input type="text" name="keterangan_baru" value="<?= htmlspecialchars($keterangan_lama); ?>">
        <button type="submit" class="btn-submit">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
