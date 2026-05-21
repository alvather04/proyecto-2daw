<?php

// ============================================================
// FORMULARIO DE CONTACTO
// Guarda los mensajes que los usuarios envían
// Recibe: { name, email, subject, message, user_id? }
// Devuelve: { success, error? }
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

try {
    // Lee los datos del formulario
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica que todos los campos obligatorios estén llenos
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['subject']) || !isset($data['message'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }

    $db = getDB();

    // ============================================================
    // GUARDAR EL MENSAJE EN LA BASE DE DATOS
    // ============================================================
    $stmt = $db->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['user_id'] ?? null,  // user_id es opcional (si no inició sesión, se guarda vacío)
        $data['name'],
        $data['email'],
        $data['subject'],
        $data['message']
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
