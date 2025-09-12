<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'idrg');
define('DB_USER', 'root');
define('DB_PASS', 'passworddisini');
define('DB_CHARSET', 'latin1');

// Fungsi koneksi database
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}
?>

