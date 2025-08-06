<?php
// /productos/dashboard.php
include 'includes/conexion.php';
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../lobby.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Panel Admin</div>
            <div class="user-actions">
                <a class="sell-button" href="listar.php">Gestionar Productos</a>
                <a class="logout-button" href="../logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <main class="categories">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <p>Desde aquí puedes gestionar los productos que se muestran en la tienda.</p>

        <div class="category-grid">
            <div class="category-card" onclick="location.href='listar.php'">
                <i class="fas fa-box"></i>
                <h3>Productos</h3>
            </div>
            <!-- Puedes agregar más tarjetas aquí para gestionar pedidos, usuarios, etc. -->
        </div>
    </main>
</body>
</html>
