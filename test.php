<?php
include 'koneksi.php';

// Test koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "<h2>Test Koneksi Database</h2>";
echo "Koneksi ke database berhasil!<br>";

// Test tabel anak
$result_anak = $conn->query("SELECT * FROM anak LIMIT 1");
if ($result_anak) {
    echo "Tabel anak: OK<br>";
} else {
    echo "Tabel anak: ERROR - " . $conn->error . "<br>";
}

// Test tabel kas
$result_kas = $conn->query("SELECT * FROM kas LIMIT 1");
if ($result_kas) {
    echo "Tabel kas: OK<br>";
} else {
    echo "Tabel kas: ERROR - " . $conn->error . "<br>";
}

// Test join
$result_join = $conn->query("SELECT k.id, k.jumlah, a.nama FROM kas k JOIN anak a ON k.anak_id = a.id LIMIT 1");
if ($result_join) {
    echo "Join antar tabel: OK<br>";
} else {
    echo "Join antar tabel: ERROR - " . $conn->error . "<br>";
}

$conn->close();
?>
