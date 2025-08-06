<?php
session_start();

// Verificar si el usuario ya está logueado
if(isset($_SESSION['usuario_id'])) {
    header('Location: lobby.php');
    exit;
}

require 'includes/conexion.php';
require 'includes/funciones.php';

$errores = [];
$valores = [
    'nombre' => '',
    'correo' => ''
];

// Procesar el formulario de registro
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y limpiar datos
    $valores['nombre'] = limpiarDatos($_POST['nombre']);
    $valores['correo'] = limpiarDatos($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    
    // Validaciones básicas
    if(empty($valores['nombre'])) {
        $errores['nombre'] = "El nombre completo es obligatorio";
    }
    
    if(empty($valores['correo']) || !filter_var($valores['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = "Ingrese un correo electrónico válido";
    }
    
    if(empty($contrasena) || strlen($contrasena) < 6) {
        $errores['contrasena'] = "La contraseña debe tener al menos 6 caracteres";
    } elseif($contrasena !== $confirmar_contrasena) {
        $errores['confirmar_contrasena'] = "Las contraseñas no coinciden";
    }
    
    // Verificar términos y condiciones
    if(!isset($_POST['terminos'])) {
        $errores['terminos'] = "Debes aceptar los términos y condiciones";
    }
    
    // Si no hay errores, proceder con el registro
    if(empty($errores)) {
        try {
            // Verificar si el correo ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
            $stmt->execute([$valores['correo']]);
            
            if($stmt->rowCount() > 0) {
                $errores['general'] = "El correo electrónico ya está registrado";
            } else {
                // Hash de la contraseña
                $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
                
                // Insertar nuevo usuario
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, 'cliente')");
                $stmt->execute([$valores['nombre'], $valores['correo'], $contrasena_hash]);
                
                if($stmt->rowCount() > 0) {
                    $_SESSION['registro_exitoso'] = true;
                    $_SESSION['mensaje'] = "¡Registro exitoso! Por favor inicia sesión";
                    header('Location: index.php');
                    exit;
                }
            }
        } catch(PDOException $e) {
            // Registrar el error real para diagnóstico
            error_log("Error en registro: " . $e->getMessage());
            $errores['general'] = "Ocurrió un error al registrar. Por favor, intente nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<!-- [Resto del HTML permanece igual] -->
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Salsamentaría Delicias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        body {
  background: linear-gradient(135deg,rgba(238, 11, 11, 0.56),rgb(194, 66, 66),rgb(202, 111, 111));
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
        <div class="registro-container">
            <div class="text-center mb-4">
                <img src="img/salsamentaria.jpeg" alt="Salsamentaría Delicias" class="logo">
                <h3>Crear Cuenta</h3>
                <p class="text-muted">Registro para el sistema de salsamentaría</p>
            </div>
            
            <?php if(isset($errores['general'])): ?>
                <div class="alert alert-danger"><?php echo $errores['general']; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre Completo *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>" 
                               id="nombre" name="nombre" value="<?php echo htmlspecialchars($valores['nombre']); ?>" required>
                    </div>
                    <?php if(isset($errores['nombre'])): ?>
                        <div class="invalid-feedback"><?php echo $errores['nombre']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control <?php echo isset($errores['correo']) ? 'is-invalid' : ''; ?>" 
                               id="correo" name="correo" value="<?php echo htmlspecialchars($valores['correo']); ?>" required>
                    </div>
                    <?php if(isset($errores['correo'])): ?>
                        <div class="invalid-feedback"><?php echo $errores['correo']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contrasena" class="form-label">Contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control <?php echo isset($errores['contrasena']) ? 'is-invalid' : ''; ?>" 
                                   id="contrasena" name="contrasena" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="password-strength-bar"></div>
                        </div>
                        <?php if(isset($errores['contrasena'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['contrasena']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control <?php echo isset($errores['confirmar_contrasena']) ? 'is-invalid' : ''; ?>" 
                                   id="confirmar_contrasena" name="confirmar_contrasena" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if(isset($errores['confirmar_contrasena'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['confirmar_contrasena']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="terminos" name="terminos" required>
                    <label class="form-check-label" for="terminos">Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a></label>
                </div>
                
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i> Registrarse
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="mb-0">¿Ya tienes una cuenta? <a href="index.php" class="text-decoration-none fw-bold">Inicia sesión aquí</a></p>
                </div>
            </form>
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
        
        // Indicador de fortaleza de contraseña
        const passwordInput = document.getElementById('contrasena');
        const strengthBar = document.getElementById('password-strength-bar');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Verificar longitud
            if(password.length >= 8) strength += 1;
            if(password.length >= 12) strength += 1;
            
            // Verificar caracteres especiales
            if(/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
            
            // Verificar números
            if(/\d/.test(password)) strength += 1;
            
            // Verificar mayúsculas y minúsculas
            if(/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Actualizar barra de progreso
            const width = strength * 20;
            strengthBar.style.width = `${width}%`;
            
            // Cambiar color según fortaleza
            if(strength <= 2) {
                strengthBar.style.backgroundColor = '#dc3545'; // Rojo
            } else if(strength <= 4) {
                strengthBar.style.backgroundColor = '#ffc107'; // Amarillo
            } else {
                strengthBar.style.backgroundColor = '#28a745'; // Verde
            }
        });
    </script>
</body>
</html>