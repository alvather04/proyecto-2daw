<?php
// Incluir configuración
require_once 'config.php';

// Redirigir si ya está logueado
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header("Location: index.html");
    exit();
}

$error = '';
$exito = '';

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
    
    // Validar que los campos no estén vacíos
    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor completa todos los campos";
    } else {
        // Preparar consulta para buscar el usuario
        $consulta = "SELECT ID_usuario, nombre, correo, contrasena, region FROM USUARIO WHERE correo = ?";
        $stmt = $conn->prepare($consulta);
        
        if ($stmt === false) {
            $error = "Error en la consulta: " . $conn->error;
        } else {
            // Vincular parámetro
            $stmt->bind_param("s", $correo);
            
            // Ejecutar consulta
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            // Verificar si el usuario existe
            if ($resultado->num_rows == 1) {
                $usuario = $resultado->fetch_assoc();
                
                // Verificar contraseña
                if ($usuario['contrasena'] === $contrasena) {
                    // Crear sesión
                    $_SESSION['id_usuario'] = $usuario['ID_usuario'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['correo'] = $usuario['correo'];
                    $_SESSION['region'] = $usuario['region'];
                    $_SESSION['logueado'] = true;
                    
                    // Redirigir a página principal
                    header("Location: index.html");
                    exit();
                } else {
                    $error = "Contraseña incorrecta";
                }
            } else {
                $error = "El correo no existe en el sistema";
            }
            
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Nexus Hub</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <!-- HEADER -->
    <header>
        <div class="logo">Nexus Hub</div>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="forum.html">Foro</a></li>
                <li><a href="profile.html">Mi Perfil</a></li>
                <li><a href="login.php">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <!-- LOGIN SECTION -->
    <section class="login-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Iniciar Sesión</h2>
                <p>Accede a tu cuenta de Nexus Hub</p>
            </div>
            <div class="login-form-box">
                <?php
                // Mostrar mensaje de error si existe
                if (!empty($error)) {
                    echo '<div style="color: red; margin-bottom: 15px; padding: 10px; background-color: #ffe6e6; border-radius: 5px;">';
                    echo htmlspecialchars($error);
                    echo '</div>';
                }
                // Mostrar mensaje de éxito si existe
                if (!empty($exito)) {
                    echo '<div style="color: green; margin-bottom: 15px; padding: 10px; background-color: #e6ffe6; border-radius: 5px;">';
                    echo htmlspecialchars($exito);
                    echo '</div>';
                }
                ?>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" required placeholder="tu@email.com">
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" required placeholder="Tu contraseña">
                    </div>
                    <button type="submit" class="btn-login">Iniciar Sesión</button>
                </form>
                <div class="login-links">
                    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    <a href="#" class="register-link">¿No tienes cuenta? Regístrate</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Nexus Hub. Sitio fan no oficial. League of Legends es propiedad de Riot Games.</p>
    </footer>

</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>
