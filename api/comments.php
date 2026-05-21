<?php

// ============================================================
// COMENTARIOS Y VOTOS DEL FORO
// Sirve para: votar, ver comentarios, agregar, votar comentarios, borrar
// Acciones: vote, get, add, vote_comment, delete
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

try {
    // Lee los datos que llegaron
    $data = json_decode(file_get_contents('php://input'), true);

    // El ID de la publicación es obligatorio siempre
    if (!isset($data['post_id'])) {
        echo json_encode(['success' => false, 'error' => 'Falta post_id']);
        exit;
    }

    $db = getDB();

    // ============================================================
    // ELEGIR LA ACCIÓN QUE SE QUIERE HACER
    // ============================================================
    switch ($data['action']) {

        // --------------------------------------------------------
        // VOTAR UNA PUBLICACIÓN (vote)
        // --------------------------------------------------------
        case 'vote':
            if (!isset($data['user_id']) || !isset($data['vote_type'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }

            // Revisa si el usuario ya votó esta publicación antes
            $stmt = $db->prepare("SELECT id, vote_type FROM post_votes WHERE user_id = ? AND post_id = ?");
            $stmt->execute([$data['user_id'], $data['post_id']]);
            $existingVote = $stmt->fetch();

            if ($existingVote) {
                // Si ya votó igual, le quita el voto
                if ($existingVote['vote_type'] === $data['vote_type']) {
                    $stmt = $db->prepare("DELETE FROM post_votes WHERE id = ?");
                    $stmt->execute([$existingVote['id']]);
                }
                // Si votó diferente, le cambia el voto
                else {
                    $stmt = $db->prepare("UPDATE post_votes SET vote_type = ? WHERE id = ?");
                    $stmt->execute([$data['vote_type'], $existingVote['id']]);
                }
            } else {
                // Si nunca votó, guarda el voto nuevo
                $stmt = $db->prepare("INSERT INTO post_votes (user_id, post_id, vote_type) VALUES (?, ?, ?)");
                $stmt->execute([$data['user_id'], $data['post_id'], $data['vote_type']]);
            }

            // Cuenta los votos totales (arriba - abajo)
            $stmt = $db->prepare("
                SELECT
                    (SELECT COUNT(*) FROM post_votes WHERE post_id = ? AND vote_type = 'up') -
                    (SELECT COUNT(*) FROM post_votes WHERE post_id = ? AND vote_type = 'down') as votes
            ");
            $stmt->execute([$data['post_id'], $data['post_id']]);
            $votes = $stmt->fetch()['votes'];

            echo json_encode(['success' => true, 'votes' => $votes]);
            break;

        // --------------------------------------------------------
        // VER COMENTARIOS (get)
        // --------------------------------------------------------
        case 'get':
            // Trae los comentarios de la publicación
            $stmt = $db->prepare("
                SELECT c.*, u.username,
                (SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote_type = 'up') -
                (SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote_type = 'down') as vote_count
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$data['post_id']]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'comments' => $comments]);
            break;

        // --------------------------------------------------------
        // AGREGAR UN COMENTARIO (add)
        // --------------------------------------------------------
        case 'add':
            if (!isset($data['user_id']) || !isset($data['content'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }

            // Guarda el comentario nuevo
            $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$data['post_id'], $data['user_id'], $data['content']]);

            $commentId = $db->lastInsertId();

            echo json_encode(['success' => true, 'comment_id' => $commentId]);
            break;

        // --------------------------------------------------------
        // VOTAR UN COMENTARIO (vote_comment)
        // --------------------------------------------------------
        case 'vote_comment':
            if (!isset($data['comment_id']) || !isset($data['user_id']) || !isset($data['vote_type'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }

            // Revisa si el usuario ya votó este comentario
            $stmt = $db->prepare("SELECT id, vote_type FROM comment_votes WHERE user_id = ? AND comment_id = ?");
            $stmt->execute([$data['user_id'], $data['comment_id']]);
            $existingVote = $stmt->fetch();

            if ($existingVote) {
                // Si ya votó igual, quita el voto
                if ($existingVote['vote_type'] === $data['vote_type']) {
                    $stmt = $db->prepare("DELETE FROM comment_votes WHERE id = ?");
                    $stmt->execute([$existingVote['id']]);
                } else {
                    // Si votó diferente, cambia el voto
                    $stmt = $db->prepare("UPDATE comment_votes SET vote_type = ? WHERE id = ?");
                    $stmt->execute([$data['vote_type'], $existingVote['id']]);
                }
            } else {
                // Voto nuevo
                $stmt = $db->prepare("INSERT INTO comment_votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)");
                $stmt->execute([$data['user_id'], $data['comment_id'], $data['vote_type']]);
            }

            // Cuenta los votos totales
            $stmt = $db->prepare("
                SELECT
                    (SELECT COUNT(*) FROM comment_votes WHERE comment_id = ? AND vote_type = 'up') -
                    (SELECT COUNT(*) FROM comment_votes WHERE comment_id = ? AND vote_type = 'down') as votes
            ");
            $stmt->execute([$data['comment_id'], $data['comment_id']]);
            $votes = $stmt->fetch()['votes'];

            echo json_encode(['success' => true, 'votes' => $votes]);
            break;

        // --------------------------------------------------------
        // BORRAR UN COMENTARIO (delete)
        // --------------------------------------------------------
        case 'delete':
            if (!isset($data['comment_id']) || !isset($data['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }

            // Solo el dueño puede borrar su comentario
            $stmt = $db->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
            $stmt->execute([$data['comment_id'], $data['user_id']]);

            echo json_encode(['success' => true]);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
