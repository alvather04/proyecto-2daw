<?php
// Verificar que el usuario esté logueado
require_once 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Nexus Hub</title>
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
                <li><a href="perfil.php">Mi Perfil</a></li>
                <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- PROFILE SECTION -->
    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-header">
                <h2>Mi Perfil</h2>
                <p>Bienvenido <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
            </div>
            
            <div class="profile-content">
                <div class="profile-info">
                    <h3>Información Personal</h3>
                    <div class="info-item">
                        <label>ID de Usuario:</label>
                        <p><?php echo htmlspecialchars($_SESSION['id_usuario']); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Nombre:</label>
                        <p><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Correo Electrónico:</label>
                        <p><?php echo htmlspecialchars($_SESSION['correo']); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Región:</label>
                        <p><?php echo htmlspecialchars($_SESSION['region']); ?></p>
                    </div>
                </div>

                <div class="profile-actions">
                    <h3>Acciones</h3>
                    <a href="forum.html" class="btn-action">Ir al Foro</a>
                    <a href="cerrar_sesion.php" class="btn-action btn-logout">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Nexus Hub. Sitio fan no oficial. League of Legends es propiedad de Riot Games.</p>
    </footer>

</body>
</html>
