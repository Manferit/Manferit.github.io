<?php
include '../includes/conexion.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: listar.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

$categorias = ['Embutidos', 'Lácteos', 'Carnes', 'Ahumados', 'Otros'];
$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = $_POST['categoria'];
    $imagen = $producto['imagen'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = uniqid('producto_') . "." . $ext;
        $ruta = "../img/productos/" . $imagen;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            $mensaje = "Error al subir la nueva imagen.";
            $tipo = "error";
        }
    }

    if (empty($mensaje)) {
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria=?, imagen=? WHERE id=?");
        $ok = $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $imagen, $id]);
        if ($ok) {
            $mensaje = "Producto actualizado correctamente.";
            $tipo = "success";
        } else {
            $mensaje = "Error al actualizar el producto.";
            $tipo = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Roboto&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* === NAVBAR === */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #dc2626;
            color: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            font-family: 'Playfair Display', serif;
            color: #fff;
        }

        .user-actions a {
            margin-left: 20px;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .user-actions a:hover {
            color: #facc15;
        }

        .logout {
            background-color: #facc15;
            color: #000;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            margin-left: 30px;
        }

        .logout:hover {
            background-color: #fde047;
            color: #111;
        }

        /* === BODY === */
        body {
            font-family: 'Roboto', sans-serif;
            background: #fff7ed;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #dc2626;
            font-family: 'Playfair Display', serif;
            text-align: center;
            margin: 40px auto 30px auto;
        }

        form {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
            color: #111827;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            margin-bottom: 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        button {
            background: #dc2626;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-right: 10px;
        }

        button:hover {
            background: #b91c1c;
        }

        .cancelar {
            background: #facc15;
            color: #000;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .cancelar:hover {
            background: #fde047;
        }

        .imagen-actual img {
            max-width: 120px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header>
        <div class="navbar">
            <div class="logo">Salsamentaria El Rey</div>
            <div class="user-actions">
                <a href="../lobby.php">Inicio</a>
                <a href="listar.php">Productos</a>
                
            </div>
        </div>
    </header>

    <!-- FORMULARIO -->
    <h2>✏️ Editar Producto</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>

        <label>Descripción:</label>
        <textarea name="descripcion" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>

        <label>Precio (COP):</label>
        <input type="number" name="precio" step="0.01" value="<?= htmlspecialchars($producto['precio']) ?>" required>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock']) ?>" required>

        <label>Categoría:</label>
        <select name="categoria" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat ?>" <?= $producto['categoria'] == $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>

        <label>Imagen (opcional):</label>
        <input type="file" name="imagen" accept="image/*">
        <?php if (!empty($producto['imagen'])): ?>
            <div class="imagen-actual">
                <strong>Imagen actual:</strong><br>
                <img src="../img/productos/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen actual">
            </div>
        <?php endif; ?>

        <button type="submit">Guardar Cambios</button>
        <a href="listar.php" class="cancelar">Cancelar</a>
    </form>

    <?php if (!empty($mensaje)): ?>
    <script>
        Swal.fire({
            icon: '<?= $tipo ?>',
            title: '<?= $tipo === "success" ? "¡Éxito!" : "Error" ?>',
            text: '<?= $mensaje ?>',
            confirmButtonColor: '<?= $tipo === "success" ? "#facc15" : "#dc2626" ?>'
        }).then(() => {
            <?php if ($tipo === 'success'): ?>
                window.location.href = "listar.php";
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>
