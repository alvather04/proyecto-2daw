<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

$userId = getUserId();

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, message, response, created_at FROM chats WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([$userId]);
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'chats' => $chats
    ]);
} catch(Exception $e) {
    $localChats = [];
    if (isset($_SESSION['local_chats'])) {
        $localChats = $_SESSION['local_chats'];
    }
    
    echo json_encode([
        'success' => true,
        'chats' => array_reverse($localChats)
    ]);
}