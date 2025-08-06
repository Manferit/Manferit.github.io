<?php
// includes/conexion.php
$host = '127.0.0.1';
$dbname = 'salsamentaria_el_rey';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch(PDOException $e) {
    error_log("Error de conexión: " . $e->getMessage());
    die("Lo sentimos, estamos experimentando problemas técnicos. Por favor intenta más tarde.");
}
?>