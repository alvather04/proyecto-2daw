<?php

// ============================================================
// INICIO DE SESIÓN (LOGIN)
// Verifica el correo y la contraseña para que el usuario entre
// Recibe: { email, password }
// Devuelve: { success, user?, error? }
// ============================================================

// Le decimos al navegador que vamos a devolver datos en formato texto
header('Content-Type: application/json');

// Carga la configuración general
require_once 'config.php';

// Inicia la sesión para recordar al usuario mientras navega
session_start();

// ============================================================
// EMPIEZA EL PROCESO
// ============================================================
try {

    // ----------------------------------------------------------
    // 1. LEER LO QUE ENVIÓ EL USUARIO
    // ----------------------------------------------------------
    $data = json_decode(file_get_contents('php://input'), true);

    // Revisa que haya escrito email y contraseña
    if (!isset($data['email']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }

    // ----------------------------------------------------------
    // 2. BUSCAR AL USUARIO EN LA BASE DE DATOS
    // ----------------------------------------------------------
    $db = getDB();

    // Busca por correo electrónico
    $stmt = $db->prepare("SELECT id, username, email, password, avatar FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ----------------------------------------------------------
    // 3. REVISAR LA CONTRASEÑA
    // ----------------------------------------------------------
    // Si el usuario no existe o la contraseña no coincide
    if (!$user || !password_verify($data['password'], $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Email o contraseña incorrectos']);
        exit;
    }

    // ----------------------------------------------------------
    // 4. INICIAR LA SESIÓN
    // ----------------------------------------------------------
    // Guarda el ID del usuario para saber quién es
    $_SESSION['user_id'] = $user['id'];

    // Anota la fecha y hora del último ingreso
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // ----------------------------------------------------------
    // 5. TODO BIEN, DEVOLVER LOS DATOS DEL USUARIO
    // ----------------------------------------------------------
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'avatar' => $user['avatar']
        ]
    ]);

// ============================================================
// SI ALGO SALE MAL
// ============================================================
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
