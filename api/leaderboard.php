<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config.php';

try {
    $db = getDB();
    $stmt = $db->query("
        SELECT u.username, u.email, 
            COALESCE(lp.total_lp, 0) as total_lp,
            COALESCE(lp.wins, 0) as wins,
            COALESCE(lp.losses, 0) as losses,
            COALESCE(lp.rank_tier, 'UNRANKED') as rank_tier
        FROM users u
        LEFT JOIN (
            SELECT 
                l.user_id,
                MAX(CASE WHEN l.queue_type = 'RANKED_SOLO_5x5' THEN l.lp ELSE 0 END) as total_lp,
                SUM(CASE WHEN l.queue_type = 'RANKED_SOLO_5x5' THEN l.wins ELSE 0 END) as wins,
                SUM(CASE WHEN l.queue_type = 'RANKED_SOLO_5x5' THEN l.losses ELSE 0 END) as losses,
                MAX(l.rank_tier) as rank_tier
            FROM leaderboard l
            GROUP BY l.user_id
        ) lp ON u.id = lp.user_id
        ORDER BY 
            CASE 
                WHEN lp.rank_tier = 'CHALLENGER' THEN 1
                WHEN lp.rank_tier = 'GRANDMASTER' THEN 2
                WHEN lp.rank_tier = 'MASTER' THEN 3
                WHEN lp.rank_tier = 'DIAMOND' THEN 4
                WHEN lp.rank_tier = 'EMERALD' THEN 5
                WHEN lp.rank_tier = 'PLATINUM' THEN 6
                WHEN lp.rank_tier = 'GOLD' THEN 7
                WHEN lp.rank_tier = 'SILVER' THEN 8
                WHEN lp.rank_tier = 'BRONZE' THEN 9
                WHEN lp.rank_tier = 'IRON' THEN 10
                ELSE 11
            END ASC,
            lp.total_lp DESC
        LIMIT 50
    ");
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar leaderboard']);
}
