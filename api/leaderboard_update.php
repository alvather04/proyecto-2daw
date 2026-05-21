<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['user_id'] ?? null;
$rankTier = $input['rank_tier'] ?? 'UNRANKED';
$rankDivision = $input['rank_division'] ?? 'I';
$lp = $input['lp'] ?? 0;
$wins = $input['wins'] ?? 0;
$losses = $input['losses'] ?? 0;
$queueType = $input['queue_type'] ?? 'RANKED_SOLO_5x5';

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta user_id']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO leaderboard (user_id, rank_tier, rank_division, lp, wins, losses, queue_type, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            rank_tier = VALUES(rank_tier),
            rank_division = VALUES(rank_division),
            lp = VALUES(lp),
            wins = VALUES(wins),
            losses = VALUES(losses),
            queue_type = VALUES(queue_type),
            updated_at = NOW()
    ");
    $stmt->execute([$userId, $rankTier, $rankDivision, $lp, $wins, $losses, $queueType]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar leaderboard']);
}
