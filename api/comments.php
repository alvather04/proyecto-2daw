<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['post_id'])) {
        echo json_encode(['success' => false, 'error' => 'Falta post_id']);
        exit;
    }
    
    $db = getDB();
    
    switch($data['action']) {
        case 'vote':
            if (!isset($data['user_id']) || !isset($data['vote_type'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }
            
            // Verificar si ya existe el voto
            $stmt = $db->prepare("SELECT id, vote_type FROM post_votes WHERE user_id = ? AND post_id = ?");
            $stmt->execute([$data['user_id'], $data['post_id']]);
            $existingVote = $stmt->fetch();
            
            if ($existingVote) {
                if ($existingVote['vote_type'] === $data['vote_type']) {
                    // Quitar voto
                    $stmt = $db->prepare("DELETE FROM post_votes WHERE id = ?");
                    $stmt->execute([$existingVote['id']]);
                } else {
                    // Cambiar voto
                    $stmt = $db->prepare("UPDATE post_votes SET vote_type = ? WHERE id = ?");
                    $stmt->execute([$data['vote_type'], $existingVote['id']]);
                }
            } else {
                // Nuevo voto
                $stmt = $db->prepare("INSERT INTO post_votes (user_id, post_id, vote_type) VALUES (?, ?, ?)");
                $stmt->execute([$data['user_id'], $data['post_id'], $data['vote_type']]);
            }
            
            // Obtener conteo de votos
            $stmt = $db->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM post_votes WHERE post_id = ? AND vote_type = 'up') - 
                    (SELECT COUNT(*) FROM post_votes WHERE post_id = ? AND vote_type = 'down') as votes
            ");
            $stmt->execute([$data['post_id'], $data['post_id']]);
            $votes = $stmt->fetch()['votes'];
            
            echo json_encode(['success' => true, 'votes' => $votes]);
            break;
            
        case 'get':
            // Obtener comentarios de un post
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
            
        case 'add':
            if (!isset($data['user_id']) || !isset($data['content'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$data['post_id'], $data['user_id'], $data['content']]);
            
            $commentId = $db->lastInsertId();
            
            echo json_encode(['success' => true, 'comment_id' => $commentId]);
            break;
            
        case 'vote_comment':
            if (!isset($data['comment_id']) || !isset($data['user_id']) || !isset($data['vote_type'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }
            
            $stmt = $db->prepare("SELECT id, vote_type FROM comment_votes WHERE user_id = ? AND comment_id = ?");
            $stmt->execute([$data['user_id'], $data['comment_id']]);
            $existingVote = $stmt->fetch();
            
            if ($existingVote) {
                if ($existingVote['vote_type'] === $data['vote_type']) {
                    $stmt = $db->prepare("DELETE FROM comment_votes WHERE id = ?");
                    $stmt->execute([$existingVote['id']]);
                } else {
                    $stmt = $db->prepare("UPDATE comment_votes SET vote_type = ? WHERE id = ?");
                    $stmt->execute([$data['vote_type'], $existingVote['id']]);
                }
            } else {
                $stmt = $db->prepare("INSERT INTO comment_votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)");
                $stmt->execute([$data['user_id'], $data['comment_id'], $data['vote_type']]);
            }
            
            $stmt = $db->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM comment_votes WHERE comment_id = ? AND vote_type = 'up') - 
                    (SELECT COUNT(*) FROM comment_votes WHERE comment_id = ? AND vote_type = 'down') as votes
            ");
            $stmt->execute([$data['comment_id'], $data['comment_id']]);
            $votes = $stmt->fetch()['votes'];
            
            echo json_encode(['success' => true, 'votes' => $votes]);
            break;
            
        case 'delete':
            if (!isset($data['comment_id']) || !isset($data['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
            $stmt->execute([$data['comment_id'], $data['user_id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}