<?php
// Incluir configuración
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    // Si no está logueado, redirigir al login
    header("Location: login.php");
    exit();
}
?>
