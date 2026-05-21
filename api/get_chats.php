<?php

// ============================================================
// HISTORIAL DEL CHAT
// Muestra las conversaciones anteriores con la IA
// Recibe: { user_id }
// Devuelve: { success, chats }
// ============================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Solicitud de verificación
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

// ============================================================
// 1. LEER EL ID DEL USUARIO
// ============================================================
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['user_id']) ? intval($input['user_id']) : null;

try {
    $pdo = getDB();

    // ============================================================
    // 2. BUSCAR LAS CONVERSACIONES GUARDADAS
    // ============================================================
    // Si hay usuario, busca solo las suyas
    if ($userId) {
        // Máximo 50 conversaciones, de la más reciente a la más antigua
        $stmt = $pdo->prepare("SELECT id, message, response, created_at FROM chats WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$userId]);
    } else {
        // Si no hay usuario, muestra las de todos
        $stmt = $pdo->query("SELECT id, message, response, created_at FROM chats ORDER BY created_at DESC LIMIT 50");
    }

    // Obtiene todos los resultados
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ============================================================
    // 3. DEVOLVER LOS RESULTADOS
    // ============================================================
    echo json_encode([
        'success' => true,
        'chats' => $chats
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
