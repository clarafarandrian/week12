koneksi.php

<?php
// koneksi.php
// File untuk koneksi ke database MySQL

$host = "localhost";      // Host database
$user = "root";           // Username MySQL
$pass = "";               // Password MySQL
$db   = "profile_mahasiswa"; // Nama database

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset ke utf8
mysqli_set_charset($conn, "utf8");

// echo "Koneksi berhasil!"; // Uncomment untuk testing
?>