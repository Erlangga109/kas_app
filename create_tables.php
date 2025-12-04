<?php
include 'koneksi.php';

// Membuat database dan tabel jika belum ada
$sql = "
-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS kasdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kasdb;

-- Membuat tabel anak jika belum ada
CREATE TABLE IF NOT EXISTS anak (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Membuat tabel kas jika belum ada
CREATE TABLE IF NOT EXISTS kas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anak_id INT NOT NULL,
  jumlah INT NOT NULL,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  keterangan VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (anak_id) REFERENCES anak(id) ON DELETE CASCADE
);
";

if ($conn->multi_query($sql) === TRUE) {
    echo "Struktur database berhasil diperbarui!";
} else {
    echo "Error membuat struktur database: " . $conn->error;
}

// Tunggu hasil query selesai
do {
    if ($result = $conn->store_result()) {
        $result->free();
    }
} while ($conn->more_results() && $conn->next_result());

$conn->close();
?>