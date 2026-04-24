<?php
// Cerrar sesión del usuario

require_once 'config.php';

// Destruir sesión
session_destroy();

// Limpiar variables de sesión
$_SESSION = array();

// Redirigir al login
header("Location: login.php");
exit();
?>
