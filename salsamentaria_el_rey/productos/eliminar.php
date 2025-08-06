<?php
// /productos/eliminar.php
include '../includes/conexion.php';
session_start();

// Verificar si hay sesión activa (ajusta según tu sistema)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: listar.php");
    exit;
} catch (PDOException $e) {
    echo "Error al eliminar el producto: " . $e->getMessage();
}
