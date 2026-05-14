<?php
header('Content-Type: application/json');
require_once 'config.php';

session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }
    
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id, username, email, password, avatar FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($data['password'], $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Email o contraseña incorrectos']);
        exit;
    }
    
    $_SESSION['user_id'] = $user['id'];
    
    // Actualizar último login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'avatar' => $user['avatar']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}