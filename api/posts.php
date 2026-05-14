<?php
header('Content-Type: application/json');
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    switch($method) {
        case 'GET':
            // Obtener todos los posts
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
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['user_id']) || !isset($data['title']) || !isset($data['description']) || !isset($data['tag'])) {
                echo json_encode(['success' => false, 'error' => 'Faltan datos']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO posts (user_id, title, description, tag, image, votes) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$data['user_id'], $data['title'], $data['description'], $data['tag'], $data['image'] ?? '']);
            
            $postId = $db->lastInsertId();
            
            // Auto-votar el post
            $stmt = $db->prepare("INSERT INTO post_votes (user_id, post_id, vote_type) VALUES (?, ?, 'up')");
            $stmt->execute([$data['user_id'], $postId]);
            
            echo json_encode(['success' => true, 'post_id' => $postId]);
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            $userId = $_GET['user_id'] ?? 0;
            
            $stmt = $db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            
            echo json_encode(['success' => true]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}