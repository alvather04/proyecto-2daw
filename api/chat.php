<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
$userId = isset($input['user_id']) ? intval($input['user_id']) : null;

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

$aiResponse = callGeminiAPI($message);

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, message, response, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $message, $aiResponse]);
    
    $chatId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'chat' => [
            'id' => $chatId,
            'message' => $message,
            'response' => $aiResponse,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}