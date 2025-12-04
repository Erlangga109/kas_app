<?php
include 'koneksi.php';

echo "<h2>Proses Migrasi Database</h2>";

// Cek apakah tabel kas ada
$result_kas_check = $conn->query("SHOW TABLES LIKE 'kas'");
if ($result_kas_check->num_rows == 0) {
    // Buat tabel kas baru jika tidak ada
    $sql_create_kas = "CREATE TABLE kas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jumlah INT NOT NULL,
        nama VARCHAR(100) NOT NULL,
        tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql_create_kas) === TRUE) {
        echo "Tabel kas awal berhasil dibuat.<br>";
    } else {
        echo "Error membuat tabel kas awal: " . $conn->error . "<br>";
    }
}

// Cek apakah tabel anak ada
$result_anak = $conn->query("SHOW TABLES LIKE 'anak'");
if ($result_anak->num_rows == 0) {
    // Buat tabel anak
    $sql_create_anak = "CREATE TABLE anak (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql_create_anak) === TRUE) {
        echo "Tabel anak berhasil dibuat.<br>";
    } else {
        echo "Error membuat tabel anak: " . $conn->error . "<br>";
    }
} else {
    echo "Tabel anak sudah ada.<br>";
}

// Cek struktur tabel kas saat ini
$result_kas = $conn->query("SHOW COLUMNS FROM kas");
if ($result_kas) {
    $columns = [];
    while ($row = $result_kas->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    // Cek apakah struktur tabel kas adalah yang lama (dengan kolom nama dan tanpa anak_id)
    if (in_array('nama', $columns) && !in_array('anak_id', $columns)) {
        echo "Mendeteksi struktur tabel kas lama. Melakukan migrasi...<br>";

        // Buat tabel anak sementara dari data nama di tabel kas jika belum ada
        $result_nama = $conn->query("SELECT DISTINCT nama FROM kas");
        $nama_inserted = [];
        while ($row = $result_nama->fetch_assoc()) {
            $nama = $conn->real_escape_string($row['nama']);
            if (!in_array($nama, $nama_inserted)) {
                $check = $conn->query("SELECT id FROM anak WHERE nama = '$nama'");
                if ($check->num_rows == 0) {
                    $conn->query("INSERT INTO anak (nama) VALUES ('$nama')");
                }
                $nama_inserted[] = $nama;
            }
        }

        // Tambah kolom anak_id ke tabel kas
        $add_anak_id = "ALTER TABLE kas ADD COLUMN anak_id INT DEFAULT NULL";
        if ($conn->query($add_anak_id) === TRUE) {
            echo "Kolom anak_id berhasil ditambahkan ke tabel kas.<br>";
        } else {
            echo "Error menambahkan kolom anak_id: " . $conn->error . "<br>";
        }

        // Update tabel kas untuk menghubungkan dengan tabel anak
        $update_query = "
            UPDATE kas k
            JOIN anak a ON k.nama = a.nama
            SET k.anak_id = a.id
        ";
        if ($conn->query($update_query) === TRUE) {
            echo "Data kas berhasil dihubungkan dengan tabel anak.<br>";
        } else {
            echo "Error mengupdate data kas: " . $conn->error . "<br>";
        }

        // Tambah kolom keterangan sebelum menghapus nama
        $add_keterangan = "ALTER TABLE kas ADD COLUMN keterangan VARCHAR(255) DEFAULT NULL";
        if ($conn->query($add_keterangan) === TRUE) {
            echo "Kolom keterangan berhasil ditambahkan.<br>";
        } else {
            echo "Error menambahkan kolom keterangan: " . $conn->error . "<br>";
        }

        // Hapus kolom nama lama (jika tidak dibutuhkan lagi)
        // Jangan hapus kolom nama jika masih diperlukan untuk sementara
        $conn->query("ALTER TABLE anak ADD COLUMN tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP");

        // Tambah foreign key constraint
        // Kita abaikan constraint dulu karena mungkin ada masalah dengan data yang sudah ada
        echo "Langkah migrasi awal selesai. Sekarang jalankan migrasi lanjutan...<br>";
    } else if (!in_array('anak_id', $columns)) {
        // Tabel kas ada tapi tidak memiliki anak_id, mungkin struktur yang berbeda
        echo "Menambahkan kolom anak_id ke tabel kas...<br>";
        $conn->query("ALTER TABLE kas ADD COLUMN anak_id INT DEFAULT NULL");
    }

    if (!in_array('keterangan', $columns)) {
        echo "Menambahkan kolom keterangan ke tabel kas...<br>";
        $conn->query("ALTER TABLE kas ADD COLUMN keterangan VARCHAR(255) DEFAULT NULL");
    }

    // Jika tabel anak belum memiliki kolom tanggal_daftar
    if (!in_array('tanggal_daftar', $columns)) {
        $result_anak_cols = $conn->query("SHOW COLUMNS FROM anak");
        $anak_columns = [];
        while ($row = $result_anak_cols->fetch_assoc()) {
            $anak_columns[] = $row['Field'];
        }
        if (!in_array('tanggal_daftar', $anak_columns)) {
            $conn->query("ALTER TABLE anak ADD COLUMN tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP");
        }
    }

    echo "Menambah foreign key constraint...<br>";
    // Tambah foreign key constraint jika belum ada
    $fk_check = $conn->query("SELECT
        CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'kas'
        AND COLUMN_NAME = 'anak_id'
        AND REFERENCED_TABLE_NAME = 'anak'");

    if ($fk_check->num_rows == 0) {
        // Pastikan tidak ada null values di anak_id sebelum menambahkan foreign key
        $conn->query("DELETE FROM kas WHERE anak_id IS NULL AND id NOT IN (
            SELECT k2.id FROM (SELECT * FROM kas) k2 JOIN anak a ON k2.nama = a.nama
        )");

        // Update lagi untuk mengisi anak_id jika belum lengkap
        $conn->query("
            UPDATE kas k
            JOIN anak a ON k.nama = a.nama
            SET k.anak_id = a.id
            WHERE k.anak_id IS NULL
        ");

        // Coba tambah foreign key
        $conn->query("ALTER TABLE kas ADD CONSTRAINT fk_kas_anak FOREIGN KEY (anak_id) REFERENCES anak(id) ON DELETE CASCADE");
    }

    echo "Proses migrasi sebagian besar selesai. Jika masih ada error, mungkin perlu restart aplikasi.";
} else {
    echo "Tidak dapat mengakses struktur tabel kas: " . $conn->error . "<br>";
}

$conn->close();
?>