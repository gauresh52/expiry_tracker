<?php
// config.php
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default is empty
$DB_NAME = 'expiry_tracker';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "<h3 style='color:green'>✅ Database connection successful!</h3>";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper: redirect
function redirect($url) {
    header("Location: $url");
    exit;
}
?>
