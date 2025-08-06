<?php
session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Procesar logout si se hizo clic en el botón
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

require 'includes/conexion.php';

// Obtener productos destacados
try {
    $sql = "SELECT * FROM productos ORDER BY fecha_registro DESC LIMIT 8";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_productos = "Error al cargar productos: " . $e->getMessage();
}

// Obtener categorías
try {
    $sql = "SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $error_categorias = "Error al cargar categorías: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salsamentaría El Rey - Productos de Calidad</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/salsamentaria_el_rey/lobby.css">
    <style>
        /* Estilos para los botones del navbar */
        .user-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-actions a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-name {
            color: var(--accent);
            font-weight: 600;
            margin-right: 0.5rem;
        }
        
        .sell-button {
            background-color: var(--accent);
            color: var(--primary) !important;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .sell-button:hover {
            background-color: #e6c200;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .logout-button {
            background-color: #dc3545;
            color: white !important;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-button:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
     .categories {
  text-align: center;
  padding: 2rem 0;
}

.categories h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #333;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  padding: 0 2rem;
}

.category-card {
  background: white;
  border-radius: 10px;
  overflow: hidden;
  text-decoration: none;
  color: #333;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.category-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.category-card h3 {
  font-size: 1.5rem;
  margin: 1rem 0;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}


    </style>
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
                <span class="user-name">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <a href="#"><i class="fas fa-heart"></i> Favoritos</a>
                
                <a href="#"><i class="fas fa-shopping-cart"></i> Carrito</a>
                <a href="admin_login.php"><i class="fas fa-plusping-cart"></i> ADMIN</a>
                
                <a href="?logout=true" class="logout-button" onclick="return confirm('¿Estás seguro que deseas salir?');">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
                  <a class="sell-button" href="productos/listar.php">Administrar productos</a>
            </div>
                  
        </div>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Los Mejores Productos Cárnicos y Alimenticios</h1>
            <p>Calidad y tradición en cada uno de nuestros productos. Más de 20 años sirviendo a la comunidad.</p>
            <a href="productos.php" class="cta-button">Ver Productos</a>
        </div>
    </section>
    
   <section class="categories">
  <h2>Nuestras Categorías</h2>
  <div class="category-grid">
    <a href="embutidos.php" class="category-card">
      <img src="img/embutidos.jpeg" alt="Embutidos">
      <h3>Embutidos</h3>
    </a>
    <a href="lacteos.php" class="category-card">
      <img src="img/lacteos.jpeg" alt="Lácteos">
      <h3>Lácteos</h3>
    </a>
    <a href="salsas.php" class="category-card">
      <img src="img/salsas.jpeg" alt="Salsas">
      <h3>Salsas</h3>
    </a>
  </div>
</section>

    
    <section class="featured-products">
        <div class="section-header">
            <h2>Productos Destacados</h2>
            <a href="productos.php">Ver todos los productos</a>
        </div>

        <div class="product-grid">
            <?php if(isset($productos) && !empty($productos)): ?>
                <?php foreach($productos as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?= !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : 'assets/img/producto-default.jpg' ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?= htmlspecialchars($producto['nombre']) ?></h3>
                            <div class="product-price">$<?= number_format($producto['precio'], 2) ?></div>
                            <div class="product-stock <?= 
                                $producto['stock'] > 10 ? 'in-stock' : 
                                ($producto['stock'] > 0 ? 'low-stock' : 'out-of-stock') 
                            ?>">
                                <?= 
                                    $producto['stock'] > 10 ? 'Disponible' : 
                                    ($producto['stock'] > 0 ? 'Últimas unidades' : 'Agotado')
                                ?>
                            </div>
                            <button class="add-to-cart" <?= $producto['stock'] <= 0 ? 'disabled' : '' ?>>
                                <?= $producto['stock'] > 0 ? 'Añadir al carrito' : 'Agotado' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos destacados disponibles</p>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="about-section">
        <div class="about-content">
            <h2>Sobre Salsamentaría El Rey</h2>
            <p>Desde 2003, nos hemos dedicado a ofrecer los mejores productos cárnicos y alimenticios a nuestros clientes. Nuestro compromiso con la calidad y el servicio nos ha convertido en un referente en la región.</p>
            <p>Trabajamos directamente con productores locales para garantizar la frescura y calidad de nuestros productos, manteniendo siempre los más altos estándares de higiene y procesamiento.</p>
            <a href="nosotros.php" class="cta-button">Conoce más sobre nosotros</a>
        </div>
    </section>
    
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
                    <li><a href="productos.php?categoria=carnes">Carnes</a></li>
                    <li><a href="productos.php?categoria=embutidos">Embutidos</a></li>
                    <li><a href="productos.php?categoria=quesos">Quesos</a></li>
                    <li><a href="productos.php?categoria=bebidas">Bebidas</a></li>
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
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> Av. Principal #123, Ciudad</p>
                    <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> info@salsamentariaelrey.com</p>
                    <p><i class="fas fa-clock"></i> Lunes a Sábado: 8am - 7pm</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Salsamentaría El Rey. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Confirmación antes de cerrar sesión
        document.querySelector('.logout-button').addEventListener('click', function(e) {
            if(!confirm('¿Estás seguro que deseas cerrar sesión?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>