<?php
include '../includes/conexion.php';
session_start();

$categorias = ['Embutidos', 'Lácteos', 'Carnes', 'Ahumados', 'Otros'];
$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = $_POST['categoria'];
    $imagen = $_FILES['imagen'];

    $carpetaDestino = '../img/productos/';
    if (!file_exists($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    if ($imagen['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid('producto_') . "." . $ext;
        $rutaDestino = $carpetaDestino . $nombreImagen;

        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            try {
                $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $nombreImagen]);
                // Redirige a la categoría correspondiente
                header("Location: listar.php?categoria=" . urlencode($categoria));
                exit;
            } catch (PDOException $e) {
                $error = "Error al guardar en la base de datos.";
            }
        } else {
            $error = "No se pudo mover la imagen al servidor.";
        }
    } else {
        $error = "Error al subir la imagen: " . $imagen['error'];
    }
}
?>

<!-- HTML (mismo diseño anterior, no modificado aquí por brevedad) -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #fefce8;
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, #7f1d1d, #991b1b, #dc2626);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            padding: 0.8rem 1.8rem;
            border-bottom: 2px solid #facc15;
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

        .card-header {
            background-color: #facc15 !important;
            font-weight: bold;
        }

        .btn-success {
            background-color: #dc2626;
            border: none;
        }

        .btn-success:hover {
            background-color: #b91c1c;
        }

        .btn-secondary {
            background-color: #6b7280;
            border: none;
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
                <li class="nav-item">
                    <a class="nav-link" href="../lobby.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listar.php">Productos</a>
                </li>
            </ul>
            <a href="../logout.php" class="btn btn-logout">Cerrar sesión</a>
        </div>
    </div>
</nav>

<!-- FORMULARIO -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header text-dark">
                    Agregar nuevo producto
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción:</label>
                            <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Precio:</label>
                                <input type="number" name="precio" class="form-control" step="0.01" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Stock:</label>
                                <input type="number" name="stock" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría:</label>
                            <select name="categoria" class="form-select" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen:</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Guardar</button>
                            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($exito): ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Producto creado!',
    text: '<?= $exito ?>',
    confirmButtonColor: '#facc15'
});
</script>
<?php elseif ($error): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= $error ?>',
    confirmButtonColor: '#dc2626'
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
