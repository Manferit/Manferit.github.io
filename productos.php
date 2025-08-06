<?php
// productos.php - Vista pública de productos por categoría
include 'includes/conexion.php';

$categoria = $_GET['categoria'] ?? '';
$stmt = $conn->prepare("SELECT * FROM productos WHERE categoria = ?");
$stmt->execute([$categoria]);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - <?php echo htmlspecialchars($categoria); ?></title>
    <link rel="stylesheet" href="lobby.css">
</head>
<body>
    <header>
          <div class="navbar">
            <div class="logo">
                <i class="fas fa-crown"></i>
                Salsamentaría El Rey
            </div>
            <div class="search-bar">
                <form action="productos.php" method="GET">
                    <input type="text" name="busqueda" placeholder="Busca productos, categorías...">
                </form>
            </div>
            <div class="user-actions">
                <br><br>
                
                <a href="#"><i class="fas fa-heart"></i> Favoritos</a>
                <a href="#"><i class="fas fa-shopping-cart"></i> Carrito</a>
                <a href="vender.php" class="sell-button">
                    <i class="fas fa-plus"></i> Vender
                </a>
                
                <a href="?logout=true" class="logout-button" onclick="return confirm('¿Estás seguro que deseas salir?');">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
    </header>

    <main class="categories">
        <h2>Productos en categoría: <?php echo htmlspecialchars($categoria); ?></h2>
        <div class="product-grid">
            <?php foreach ($resultado as $producto): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                        <div class="product-price">$<?php echo number_format($producto['precio'], 2); ?></div>
                        <div class="product-stock">
                            <?php
                                if ($producto['stock'] > 10) {
                                    echo '<span class="in-stock">Disponible</span>';
                                } elseif ($producto['stock'] > 0) {
                                    echo '<span class="low-stock">Pocas unidades</span>';
                                } else {
                                    echo '<span class="out-of-stock">Agotado</span>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
