<?php
header('Content-Type: application/json');
require_once 'config.php';

session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }
    
    $db = getDB();
    
    // Verificar si el usuario ya existe
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$data['username'], $data['email']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'El usuario o email ya existe']);
        exit;
    }
    
    // Crear usuario
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['username'], $data['email'], $hashedPassword]);
    
    $userId = $db->lastInsertId();
    $_SESSION['user_id'] = $userId;
    
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