<?php

// ============================================================
// REGISTRO (REGISTER)
// Crea una cuenta nueva para el usuario
// Recibe: { username, email, password }
// Devuelve: { success, user?, error? }
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';
session_start();

try {

    // ----------------------------------------------------------
    // 1. LEER LO QUE ENVIÓ EL USUARIO
    // ----------------------------------------------------------
    $data = json_decode(file_get_contents('php://input'), true);

    // Revisa que haya escrito nombre, correo y contraseña
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }

    $db = getDB();

    // ----------------------------------------------------------
    // 2. VERIFICAR QUE NO ESTÉ REPETIDO
    // ----------------------------------------------------------
    // Busca si ya hay alguien con ese nombre o correo
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$data['username'], $data['email']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'El usuario o email ya existe']);
        exit;
    }

    // ----------------------------------------------------------
    // 3. CREAR EL USUARIO
    // ----------------------------------------------------------
    // Encripta la contraseña para guardarla segura
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Guarda el nuevo usuario en la base de datos
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['username'], $data['email'], $hashedPassword]);

    // Obtiene el número de ID que se le asignó
    $userId = $db->lastInsertId();

    // Inicia la sesión automáticamente
    $_SESSION['user_id'] = $userId;

    // ----------------------------------------------------------
    // 4. DEVOLVER LOS DATOS DEL USUARIO CREADO
    // ----------------------------------------------------------
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userId,
            'username' => $data['username'],
            'email' => $data['email']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
