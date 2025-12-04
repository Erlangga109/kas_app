<?php
include 'koneksi.php';

echo "<h2>Proses Verifikasi dan Perbaikan Struktur Database</h2>";

// Cek dan tampilkan struktur tabel saat ini
echo "<h3>Struktur tabel kas:</h3>";
$result = $conn->query("DESCRIBE kas");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Kolom: " . $row['Field'] . " | Tipe: " . $row['Type'] . " | Null: " . $row['Null'] . " | Key: " . $row['Key'] . "<br>";
    }
} else {
    echo "Tabel kas tidak ditemukan!<br>";
}

echo "<h3>Struktur tabel anak:</h3>";
$result2 = $conn->query("DESCRIBE anak");
if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        echo "Kolom: " . $row['Field'] . " | Tipe: " . $row['Type'] . " | Null: " . $row['Null'] . " | Key: " . $row['Key'] . "<br>";
    }
} else {
    echo "Tabel anak tidak ditemukan!<br>";
}

// Cek apakah tabel kas memiliki kolom anak_id
$has_anak_id = false;
$result = $conn->query("DESCRIBE kas");
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'anak_id') {
        $has_anak_id = true;
        break;
    }
}

if (!$has_anak_id) {
    echo "<h3>Memperbaiki struktur tabel kas...</h3>";
    
    // Tambah kolom anak_id jika belum ada
    $conn->query("ALTER TABLE kas ADD COLUMN anak_id INT DEFAULT NULL");
    echo "Kolom anak_id ditambahkan ke tabel kas.<br>";
    
    // Jika tabel kas masih memiliki kolom 'nama', kita perlu proses data
    $has_nama = false;
    $result = $conn->query("DESCRIBE kas");
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] == 'nama') {
            $has_nama = true;
            break;
        }
    }
    
    if ($has_nama) {
        echo "Mendeteksi kolom nama lama di tabel kas. Menghubungkan ke tabel anak...<br>";
        
        // Pastikan semua nama di tabel kas juga ada di tabel anak
        $result = $conn->query("SELECT DISTINCT nama FROM kas WHERE nama NOT IN (SELECT nama FROM anak)");
        while ($row = $result->fetch_assoc()) {
            $nama = $conn->real_escape_string($row['nama']);
            $conn->query("INSERT INTO anak (nama) VALUES ('$nama')");
            echo "Menambahkan '$nama' ke tabel anak.<br>";
        }
        
        // Update tabel kas untuk menghubungkan dengan tabel anak
        $conn->query("
            UPDATE kas k 
            JOIN anak a ON k.nama = a.nama 
            SET k.anak_id = a.id
        ");
        echo "Data kas berhasil dihubungkan ke tabel anak.<br>";
        
        // Tambah kolom keterangan jika belum ada
        $has_keterangan = false;
        $result = $conn->query("DESCRIBE kas");
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] == 'keterangan') {
                $has_keterangan = true;
                break;
            }
        }
        
        if (!$has_keterangan) {
            $conn->query("ALTER TABLE kas ADD COLUMN keterangan VARCHAR(255) DEFAULT NULL");
            echo "Kolom keterangan ditambahkan ke tabel kas.<br>";
        }
    }
    
    // Coba tambahkan foreign key constraint
    try {
        // Hapus constraint lama jika ada
        $conn->query("ALTER TABLE kas DROP FOREIGN KEY fk_kas_anak");
    } catch (Exception $e) {
        // Jika tidak ada constraint lama, lanjutkan
    }
    
    // Tambah foreign key
    $conn->query("ALTER TABLE kas ADD CONSTRAINT fk_kas_anak FOREIGN KEY (anak_id) REFERENCES anak(id) ON DELETE CASCADE");
    echo "Foreign key constraint berhasil ditambahkan.<br>";
    
    // Jika kolom nama masih ada di tabel kas, kita bisa hapus setelah migrasi
    // (opsional, untuk membersihkan struktur)
    if ($has_nama) {
        $conn->query("ALTER TABLE kas DROP COLUMN nama");
        echo "Kolom nama lama dihapus dari tabel kas.<br>";
    }
}

// Pastikan tabel anak juga memiliki kolom tanggal_daftar
$has_tanggal_daftar = false;
$result = $conn->query("DESCRIBE anak");
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'tanggal_daftar') {
        $has_tanggal_daftar = true;
        break;
    }
}

if (!$has_tanggal_daftar) {
    $conn->query("ALTER TABLE anak ADD COLUMN tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP");
    echo "Kolom tanggal_daftar ditambahkan ke tabel anak.<br>";
}

echo "<h3>Verifikasi Selesai!</h3>";
echo "Silakan coba kembali aplikasi. Jika masih ada error, mungkin perlu restart server web Anda.";
$conn->close();
?>