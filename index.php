<?php
session_start();

// Configuración para desarrollo (mostrar errores)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario ya está logueado
if(isset($_SESSION['usuario_id'])) {
    header('Location: lobby.php');
    exit;
}

require 'includes/conexion.php';
require 'includes/funciones.php';

$error = '';

// Procesar el formulario de login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = limpiarDatos($_POST['username']);
    $password = $_POST['password'];
    
    try {
        // Consulta preparada con PDO
        $stmt = $conn->prepare("SELECT id, nombre, contrasena, rol FROM usuarios WHERE correo = :correo LIMIT 1");
        $stmt->bindParam(':correo', $username);
        $stmt->execute();
        
        if($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if(password_verify($password, $usuario['contrasena'])) {
                // Configurar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];
                
                // Recordar usuario si está marcado
                if(isset($_POST['remember'])) {
                    setcookie('remember_user', $usuario['id'], time() + (86400 * 30), "/");
                }
                
                header('Location: lobby.php');
                exit;
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
    } catch(PDOException $e) {
        error_log("Error en login: " . $e->getMessage());
        $error = "Error al procesar la solicitud. Intente nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Salsamentaría Delicias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
     <style>
        body {
  background: linear-gradient(135deg,rgb(153, 56, 56),rgb(134, 40, 40), #fbeaea);
  height: 100vh;
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="text-center mb-4">
                <img src="img/jamon.jpg" alt="Salsamentaría Delicias" class="logo">
                <h3>Iniciar Sesión</h3>
                <p class="text-muted">Sistema de gestión de salsamentaría</p>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Recordar mis datos</label>
                </div>
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i> Ingresar
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <a href="recuperar.php" class="text-decoration-none d-block mb-2">¿Olvidaste tu contraseña?</a>
                <p class="mb-0">¿No tienes una cuenta? <a href="registrarse.php" class="text-decoration-none fw-bold">Regístrate aquí</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                
                if(passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>