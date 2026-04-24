<?php
// Verificar si el usuario está logueado
// Este archivo se incluye en otras páginas para verificar sesión

require_once 'config.php';

// Si no está logueado, redirigir al login
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit();
}
?>
