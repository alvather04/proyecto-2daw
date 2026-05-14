<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['post_id'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }
    
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$data['user_id'], $data['post_id']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $stmt = $db->prepare("DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$data['user_id'], $data['post_id']]);
        echo json_encode(['success' => true, 'saved' => false]);
    } else {
        $stmt = $db->prepare("INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$data['user_id'], $data['post_id']]);
        echo json_encode(['success' => true, 'saved' => true]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}