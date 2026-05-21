<?php

// ============================================================
// GUARDAR / QUITAR PUBLICACIONES
// Si ya está guardada la quita, si no está guardada la agrega
// Recibe: { user_id, post_id }
// Devuelve: { success, saved (true=guardado, false=quitado) }
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

try {
    // Lee los datos que llegaron
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica que tenga usuario y publicación
    if (!isset($data['user_id']) || !isset($data['post_id'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos']);
        exit;
    }

    $db = getDB();

    // ============================================================
    // 1. REVISAR SI YA LA TIENE GUARDADA
    // ============================================================
    $stmt = $db->prepare("SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$data['user_id'], $data['post_id']]);
    $existing = $stmt->fetch();

    // ============================================================
    // 2. ALTERNAR: GUARDAR O QUITAR
    // ============================================================
    if ($existing) {
        // Ya estaba guardada -> la quitamos
        $stmt = $db->prepare("DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$data['user_id'], $data['post_id']]);
        echo json_encode(['success' => true, 'saved' => false]);
    } else {
        // No estaba guardada -> la guardamos
        $stmt = $db->prepare("INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$data['user_id'], $data['post_id']]);
        echo json_encode(['success' => true, 'saved' => true]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
