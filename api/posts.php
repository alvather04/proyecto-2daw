<?php

// ============================================================
// PUBLICACIONES DEL FORO
// Sirve para: ver publicaciones (GET), crear (POST), borrar (DELETE)
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

// Averigua qué quiere hacer el usuario (ver, crear o borrar)
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();

    // ============================================================
    // ELEGIR LA ACCIÓN SEGÚN LO QUE LLEGUE
    // ============================================================
    switch ($method) {

        // --------------------------------------------------------
        // VER TODAS LAS PUBLICACIONES (GET)
        // --------------------------------------------------------
        case 'GET':
            // Trae todas las publicaciones con el nombre del autor,
            // cuántos votos tiene y cuántos comentarios
            $stmt = $db->query("
                SELECT p.*, u.username,
                (SELECT COUNT(*) FROM post_votes WHERE post_id = p.id AND vote_type = 'up') -
                (SELECT COUNT(*) FROM post_votes WHERE post_id = p.id AND vote_type = 'down') as vote_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
            ");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        // --------------------------------------------------------
        // CREAR UNA PUBLICACIÓN NUEVA (POST)
        // --------------------------------------------------------
        case 'POST':
            // Lee los datos que envió el usuario
            $data = json_decode(file_get_contents('php://input'), true);

            // Verifica que tenga todos los datos necesarios
            if (!isset($data['user_id']) || !isset($data['title']) || !isset($data['description']) || !isset($data['tag'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }

            // Guarda la publicación con 1 voto al inicio
            $stmt = $db->prepare("INSERT INTO posts (user_id, title, description, tag, image, votes) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$data['user_id'], $data['title'], $data['description'], $data['tag'], $data['image'] ?? '']);

            // Obtiene el ID de la publicación que se acaba de crear
            $postId = $db->lastInsertId();

            // --------------------------------------------------------
            // EL AUTOR SE VOTA A SÍ MISMO (voto inicial)
            // --------------------------------------------------------
            $stmt = $db->prepare("INSERT INTO post_votes (user_id, post_id, vote_type) VALUES (?, ?, 'up')");
            $stmt->execute([$data['user_id'], $postId]);

            echo json_encode(['success' => true, 'post_id' => $postId]);
            break;

        // --------------------------------------------------------
        // BORRAR UNA PUBLICACIÓN (DELETE)
        // --------------------------------------------------------
        case 'DELETE':
            // Lee el ID de la publicación y del usuario
            $id = $_GET['id'] ?? 0;
            $userId = $_GET['user_id'] ?? 0;

            // Solo puede borrar el dueño de la publicación
            $stmt = $db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);

            echo json_encode(['success' => true]);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
