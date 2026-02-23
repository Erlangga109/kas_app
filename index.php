<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Kas Sederhana</title>
    <link rel="stylesheet" href="assets/style.css?v=1.1">
</head>
<body>
<div class="container">
    <h1>Aplikasi Kas Sederhana</h1>

    <form action="tambah.php" method="POST" class="form">
        <label>Nama Anak:</label>
        <input type="text" name="nama" list="nama-list" required>
        <?php
        $nama_result = $conn->query("SELECT DISTINCT nama FROM kas");
        echo '<datalist id="nama-list">';
        while ($nama_row = $nama_result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($nama_row['nama']) . '">';
        }
        echo '</datalist>';
        ?>
        <label>Jumlah Kas:</label>
        <select name="jumlah" required>
            <option value="">Pilih Jumlah</option>
            <option value="1000">Rp 1.000</option>
            <option value="2000">Rp 2.000</option>
            <option value="3000">Rp 3.000</option>
            <option value="4000">Rp 4.000</option>
            <option value="5000">Rp 5.000</option>
        </select>
        <button type="submit" class="btn-submit">Simpan</button>
    </form>

    <h2>Daftar Kas</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jumlah (Rp)</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM kas ORDER BY id DESC");
            $no = 1; $total = 0;
            while ($row = $result->fetch_assoc()):
                $total += $row['jumlah'];
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                    <a href="hapus.php?id=<?= $row['id']; ?>" class="btn-delete">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Kas</strong></td>
                <td colspan="3"><strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong></td>
            </tr>
        </tfoot>
    </table>
<!-- Bagian QRIS -->
<h2>Pembayaran QRIS</h2>
<form id="qris-form">
    <label>Nama Pembayar:</label>
    <input type="text" id="qris-nama" placeholder="Masukkan nama" required>

    <label>Jumlah Pembayaran:</label>
    <select id="qris-jumlah" required>
        <option value="">Pilih Jumlah</option>
        <option value="1000">Rp 1.000</option>
        <option value="2000">Rp 2.000</option>
        <option value="3000">Rp 3.000</option>
        <option value="4000">Rp 4.000</option>
        <option value="5000">Rp 5.000</option>
    </select>

    <button type="button" class="btn-submit" id="generate-qris">Generate QRIS</button>
</form>

<div class="qris-container">
    <h3>Kode QRIS Anda:</h3>
    <div id="qris-display" style="margin:20px auto; text-align:center;"></div>
</div>

<!-- Library QRCode -->
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script src="assets/script.js?v=1.2"></script>

</div>
</body>
</html>
