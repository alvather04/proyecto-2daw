<?php

// ============================================================
// PUBLICACIONES GUARDADAS
// Devuelve todas las publicaciones que un usuario guardó
// Recibe: { user_id }
// Devuelve: { success, posts }
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

try {
    // Lee el ID del usuario
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Falta user_id']);
        exit;
    }

    $db = getDB();

    // ============================================================
    // BUSCAR PUBLICACIONES GUARDADAS CON SUS DATOS
    // ============================================================
    // Combina la información de las tablas para mostrar
    // los datos completos: votos, comentarios, autor
    $stmt = $db->prepare("
        SELECT p.*, u.username,
        (SELECT COUNT(*) FROM post_votes WHERE post_id = p.id AND vote_type = 'up') -
        (SELECT COUNT(*) FROM post_votes WHERE post_id = p.id AND vote_type = 'down') as vote_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
        FROM saved_posts sp
        JOIN posts p ON sp.post_id = p.id
        JOIN users u ON p.user_id = u.id
        WHERE sp.user_id = ?
        ORDER BY sp.created_at DESC
    ");
    $stmt->execute([$data['user_id']]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'posts' => $posts]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
