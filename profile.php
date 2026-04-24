<?php
// Verificar sesión
require_once 'check_session.php';
require_once 'config.php';

// Obtener información del usuario logueado
$ID_usuario = $_SESSION['ID_usuario'];

// Consultar información del usuario
$query = "SELECT * FROM USUARIO WHERE ID_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $ID_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Consultar comunidades del usuario
$query = "SELECT c.* FROM COMUNIDADES c
          INNER JOIN FORMAN f ON c.ID = f.ID_comunidades
          WHERE f.ID_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $ID_usuario);
$stmt->execute();
$result = $stmt->get_result();
$comunidades = [];
while($row = $result->fetch_assoc()) {
    $comunidades[] = $row;
}
$stmt->close();

$conn->close();
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
                <li><a href="profile.php">Mi Perfil</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- PROFILE SECTION -->
    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-header">
                <h2>Mi Perfil</h2>
                <p>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></p>
            </div>

            <div class="profile-info">
                <h3>Información Personal</h3>
                <div class="info-group">
                    <label>ID de Usuario:</label>
                    <p><?php echo htmlspecialchars($usuario['ID_usuario']); ?></p>
                </div>
                <div class="info-group">
                    <label>Nombre:</label>
                    <p><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                </div>
                <div class="info-group">
                    <label>Correo:</label>
                    <p><?php echo htmlspecialchars($usuario['correo']); ?></p>
                </div>
                <div class="info-group">
                    <label>Región:</label>
                    <p><?php echo htmlspecialchars($usuario['region']); ?></p>
                </div>
            </div>

            <?php if (count($comunidades) > 0): ?>
            <div class="profile-communities">
                <h3>Mis Comunidades</h3>
                <div class="communities-list">
                    <?php foreach($comunidades as $comunidad): ?>
                    <div class="community-card">
                        <h4><?php echo htmlspecialchars($comunidad['nombre']); ?></h4>
                        <p>ID: <?php echo htmlspecialchars($comunidad['ID']); ?></p>
                        <p>Roles: <?php echo htmlspecialchars($comunidad['roles']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <footer>
        <p>&copy; 2025 Nexus Hub. Sitio fan no oficial. League of Legends es propiedad de Riot Games.</p>
    </footer>

</body>
</html>
