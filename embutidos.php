<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Procesar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

require 'includes/conexion.php';

// Cargar productos de categoría Embutidos
try {
    $sql = "SELECT * FROM productos WHERE categoria = 'Embutidos' ORDER BY fecha_registro DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_productos = "Error al cargar productos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Embutidos - Salsamentaría El Rey</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="/salsamentaria_el_rey/lobby.css">
</head>
<body>

<!-- NAVBAR -->
<header>
    <div class="navbar">
        <div class="logo">
            <i class="fas fa-crown"></i> Salsamentaría El Rey
        </div>
        <div class="search-bar">
            <form action="productos.php" method="GET">
                <input type="text" name="busqueda" placeholder="Busca productos, categorías...">
            </form>
        </div>
        <div class="user-actions">
            <span class="user-name">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['nombre']) ?>
            </span>
            <a href="lobby.php"><i class="t"></i> inicio   </a>
            <a href="vender.php" class="sell-button">
                <i class="fas fa-plus"></i> Vender
            </a>
            <a href="?logout=true" class="logout-button" onclick="return confirm('¿Estás seguro que deseas salir?');">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </div>
    </div>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <h1>Embutidos</h1>
        <p>Explora nuestra selección de embutidos frescos y deliciosos.</p>
    </div>
</section>

<!-- PRODUCTOS -->
<main>
    <section class="featured-products">
        <div class="section-header">
            <h2>Todos los Embutidos</h2>
            <a href="productos.php">Ver todos los productos</a>
        </div>

        <div class="product-grid">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?= !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : 'assets/img/producto-default.jpg' ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                        </div>
                        <div class="product-info">
                            <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                            <p class="price">$<?= number_format($producto['precio'], 2) ?></p>
                            <p class="stock <?= 
                                $producto['stock'] > 10 ? 'in-stock' : 
                                ($producto['stock'] > 0 ? 'low-stock' : 'out-of-stock') 
                            ?>">
                                <?= 
                                    $producto['stock'] > 10 ? 'Disponible' : 
                                    ($producto['stock'] > 0 ? 'Últimas unidades' : 'Agotado')
                                ?>
                            </p>
                            <button class="add-to-cart" <?= $producto['stock'] <= 0 ? 'disabled' : '' ?>>
                                <?= $producto['stock'] > 0 ? 'Añadir al carrito' : 'Agotado' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">No hay embutidos disponibles actualmente.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer>
    <div class="footer-content">
        <div class="footer-column">
            <h3>Salsamentaría El Rey</h3>
            <p>Tu proveedor de confianza para productos cárnicos y alimenticios de la más alta calidad.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="footer-column">
            <h3>Productos</h3>
            <ul>
                <li><a href="productos.php">Todos los productos</a></li>
                <li><a href="embutidos.php">Embutidos</a></li>
                <li><a href="lacteos.php">Lácteos</a></li>
                <li><a href="salsas.php">Salsas</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Información</h3>
            <ul>
                <li><a href="nosotros.php">Sobre nosotros</a></li>
                <li><a href="contacto.php">Contacto</a></li>
                <li><a href="preguntas-frecuentes.php">Preguntas frecuentes</a></li>
                <li><a href="terminos.php">Términos y condiciones</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Contacto</h3>
            <p><i class="fas fa-map-marker-alt"></i> Av. Principal #123, Ciudad</p>
            <p><i class="fas fa-phone"></i> (123) 456-7890</p>
            <p><i class="fas fa-envelope"></i> info@salsamentariaelrey.com</p>
            <p><i class="fas fa-clock"></i> Lunes a Sábado: 8am - 7pm</p>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y') ?> Salsamentaría El Rey. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>
