<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['subject']) || !isset($data['message'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }
    
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['user_id'] ?? null,
        $data['name'],
        $data['email'],
        $data['subject'],
        $data['message']
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}