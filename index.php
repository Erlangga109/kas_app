<?php
include 'koneksi.php';

// Cek apakah kolom anak_id ada di tabel kas (indikator struktur baru)
$check_kolom_anak_id = $conn->query("SHOW COLUMNS FROM kas LIKE 'anak_id'");
$kolom_anak_id_exists = $check_kolom_anak_id->num_rows > 0;

// Cek apakah kolom keterangan ada di tabel kas
$check_kolom_keterangan = $conn->query("SHOW COLUMNS FROM kas LIKE 'keterangan'");
$kolom_keterangan_exists = $check_kolom_keterangan->num_rows > 0;

// Cek apakah kolom tanggal_daftar ada di tabel anak
$check_kolom_tanggal_daftar = $conn->query("SHOW COLUMNS FROM anak LIKE 'tanggal_daftar'");
$kolom_tanggal_daftar_exists = $check_kolom_tanggal_daftar->num_rows > 0;
?>
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

    <?php if($kolom_anak_id_exists): ?>
    <form action="tambah.php" method="POST" class="form">
        <label>Nama Anak:</label>
        <select name="anak_id" required>
            <option value="">Pilih Nama</option>
            <?php
            $anak_query = "SELECT id, nama FROM anak ORDER BY nama ASC";
            $anak_result = $conn->query($anak_query);
            if (!$anak_result) {
                echo '<option value="">Error: ' . $conn->error . '</option>';
            } else {
                while ($anak_row = $anak_result->fetch_assoc()) {
                    echo '<option value="' . $anak_row['id'] . '">' . htmlspecialchars($anak_row['nama']) . '</option>';
                }
            }
            ?>
        </select>
        <label>Jumlah Kas:</label>
        <select name="jumlah" required>
            <option value="">Pilih Jumlah</option>
            <option value="1000">Rp 2.000</option>
            <option value="2000">Rp 2.000</option>
            <option value="3000">Rp 3.000</option>
            <option value="4000">Rp 4.000</option>
            <option value="5000">Rp 10.000</option>
        </select>
        <label>Keterangan:</label>
        <input type="text" name="keterangan" placeholder="Keterangan (opsional)">
        <button type="submit" class="btn-submit">Simpan</button>
    </form>
    <?php else: ?>
    <p>Untuk menggunakan aplikasi ini, silakan jalankan migrasi database terlebih dahulu: <a href="migrate.php">Klik di sini untuk migrasi</a></p>
    <?php endif; ?>

    <h2>Daftar Kas</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jumlah (Rp)</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($kolom_anak_id_exists) {
                // Struktur baru dengan relasi
                $query = "SELECT k.id, k.jumlah, k.tanggal, k.keterangan, a.nama FROM kas k JOIN anak a ON k.anak_id = a.id ORDER BY k.id DESC";
            } else {
                // Struktur lama, tampilkan semua data tanpa join
                $query = "SELECT id, nama, jumlah, tanggal, '' as keterangan FROM kas ORDER BY id DESC";
            }

            $result = $conn->query($query);
            if (!$result) {
                echo "<tr><td colspan='6'>Error dalam query: " . $conn->error . "</td></tr>";
            } else {
                // Hitung total
                $total_query = "SELECT SUM(jumlah) as total FROM kas";
                $total_result = $conn->query($total_query);
                if ($total_result) {
                    $total_row = $total_result->fetch_assoc();
                    $total = $total_row['total'] ?? 0;
                } else {
                    $total = 0;
                }

                $no = 1;
                while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td>
                    <?php if($kolom_anak_id_exists): ?>
                        <?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?>
                    <?php else: ?>
                        <?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                    <a href="hapus.php?id=<?= $row['id']; ?>" class="btn-delete">Hapus</a>
                </td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Kas</strong></td>
                <td colspan="4"><strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <h2>Daftar Anak</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anak</th>
                <th>Tanggal Daftar</th>
                <th>Total Kas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($kolom_anak_id_exists) {
                // Jika struktur tabel baru digunakan
                if($kolom_tanggal_daftar_exists) {
                    $anak_query = "SELECT a.id, a.nama, a.tanggal_daftar, COALESCE(SUM(k.jumlah), 0) as total_kas FROM anak a LEFT JOIN kas k ON a.id = k.anak_id GROUP BY a.id, a.nama, a.tanggal_daftar ORDER BY a.nama ASC";
                } else {
                    // Jika kolom tanggal_daftar belum ditambahkan
                    $anak_query = "SELECT a.id, a.nama, a.tanggal_daftar, COALESCE(SUM(k.jumlah), 0) as total_kas FROM anak a LEFT JOIN kas k ON a.id = k.anak_id GROUP BY a.id, a.nama ORDER BY a.nama ASC";
                }

                $anak_result = $conn->query($anak_query);
                if (!$anak_result) {
                    echo "<tr><td colspan='5'>Error dalam query: " . $conn->error . "</td></tr>";
                } else {
                    $no = 1;
                    while ($anak_row = $anak_result->fetch_assoc()) {
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($anak_row['nama']); ?></td>
                <td>
                    <?php if($kolom_tanggal_daftar_exists && !empty($anak_row['tanggal_daftar'])): ?>
                        <?= date('d-m-Y H:i', strtotime($anak_row['tanggal_daftar'])); ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>Rp <?= number_format($anak_row['total_kas'], 0, ',', '.'); ?></td>
                <td>
                    <a href="hapus_anak.php?id=<?= $anak_row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus anak ini dan semua transaksinya?')">Hapus Anak</a>
                </td>
            </tr>
            <?php
                    }
                }
            } else {
                // Jika masih menggunakan struktur lama, sembunyikan tabel ini atau beri pesan
            ?>
            <tr>
                <td colspan="5">Fitur ini memerlukan migrasi database. Silakan jalankan migrate.php terlebih dahulu.</td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <?php if($kolom_anak_id_exists): ?>
    <h2>Tambah Anak Baru</h2>
    <form action="tambah_anak.php" method="POST" class="form">
        <label>Nama Anak:</label>
        <input type="text" name="nama" required>
        <button type="submit" class="btn-submit">Tambah Anak</button>
    </form>
    <?php endif; ?>
    <!-- Tidak ada script QRIS lagi karena sudah dihapus -->
</div>
</body>
</html>
