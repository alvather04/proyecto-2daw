<?php

// ============================================================
// CHAT CON INTELIGENCIA ARTIFICIAL
// Envía un mensaje al asistente Gemini y guarda la conversación
// Recibe: { message, user_id }
// Devuelve: { success, chat?, error? }
// ============================================================

// Le decimos al navegador que vamos a devolver datos en formato texto
header('Content-Type: application/json');

// Permite que cualquier página web pueda usar este servicio
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ============================================================
// PRUEBA DE CONEXIÓN (solicitud de verificación)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Carga la configuración general
require_once 'config.php';

// ============================================================
// REVISAR QUE SEA UNA PETICIÓN VÁLIDA
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ============================================================
// 1. LEER LOS DATOS QUE LLEGAN
// ============================================================
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
$userId = isset($input['user_id']) ? intval($input['user_id']) : null;

// Revisa que el mensaje no esté vacío
if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// Revisa que el usuario haya iniciado sesión
if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

// ============================================================
// 2. PREGUNTARLE A GEMINI
// ============================================================
$aiResponse = callGeminiAPI($message);

// ============================================================
// 3. GUARDAR LA CONVERSACIÓN
// ============================================================
try {
    $pdo = getDB();

    // Guarda lo que preguntó el usuario y lo que respondió la IA
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, message, response, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $message, $aiResponse]);

    // Obtiene el número de ID de esta conversación
    $chatId = $pdo->lastInsertId();

    // ============================================================
    // 4. DEVOLVER LA RESPUESTA
    // ============================================================
    echo json_encode([
        'success' => true,
        'chat' => [
            'id' => $chatId,
            'message' => $message,
            'response' => $aiResponse,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
