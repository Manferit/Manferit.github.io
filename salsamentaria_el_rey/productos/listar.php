<?php
include '../includes/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM productos ORDER BY fecha_registro DESC");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #fff7ed;
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, #7f1d1d, #991b1b, #dc2626);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            padding: 0.8rem 1.8rem;
            border-bottom: 2px solid #facc15;
        }
        .nav-item{
             display: block;
            width: fit-content;
         
            padding: 10px 20px;
            background-color: #ffca28;
            color: #333;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;

        }
        .navbar-brand {
            color: #facc15;
            font-weight: bold;
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            margin-right: 1rem;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #facc15 !important;
            text-shadow: 0 0 8px #facc15;
        }

        .btn-logout {
            background-color: #facc15;
            color: #000;
            border: none;
            padding: 8px 18px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background-color: #fde047;
            transform: scale(1.05);
            box-shadow: 0 0 10px #facc15a0;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 3px solid #ffca28;
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .product-price {
            color: #d32f2f;
            font-weight: 600;
            margin-top: 5px;
        }

        .product-stock {
            margin: 8px 0;
            font-size: 0.95rem;
            color: #555;
        }

        .cta-button {
            display: inline-block;
            background-color: #d32f2f;
            color: white;
            padding: 8px 15px;
            margin-top: 8px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #b71c1c;
        }

        h2 {
            text-align: center;
            margin-top: 2rem;
            color: #333;
        }

        .create-btn {
            display: block;
            width: fit-content;
            margin: 1.5rem auto 0 auto;
            padding: 10px 20px;
            background-color: #ffca28;
            color: #333;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }

        .create-btn:hover {
            background-color: #f4b400;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Salsamentaria El Rey</a>
        <button class="navbar-toggler bg-warning" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                
                
            </ul>
            <li class="nav-item">
                    <a class="nav-link" href="../lobby.php">Inicio</a>
                </li>
        </div>
    </div>
</nav>

<main>
    <h2>Lista de productos</h2>
    <a class="create-btn" href="crear.php">+ Crear nuevo producto</a>

    <div class="product-grid">
        <?php foreach ($productos as $producto): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="../img/productos/<?php echo htmlspecialchars($producto['imagen'] ?? 'comida_ejemplo.jpg'); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                </div>
                <div class="product-info">
                    <div class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                    
                    <div class="product-price">$<?php echo number_format($producto['precio'], 2); ?></div>
                    <div class="product-stock">
                        <?php echo ($producto['stock'] > 0) ? 'Stock: '.$producto['stock'] : '<span class="text-danger">Agotado</span>'; ?>
                    </div>
                    <div class="product-category">
     
                    Categoría: <span><?= htmlspecialchars($producto['categoria']) ?></span>
  
                </div>
                    <a href="editar.php?id=<?php echo $producto['id']; ?>" class="cta-button">Editar</a>
                    <a href="eliminar.php?id=<?php echo $producto['id']; ?>" class="cta-button" style="background-color:#c62828" onclick="return confirm('¿Deseas eliminar este producto?')">Eliminar</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
