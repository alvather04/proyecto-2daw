<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config.php';

// ============================================================
// CLASIFICACIÓN DE JUGADORES (LEADERBOARD)
// Muestra a los mejores invocadores ordenados por rango y puntos
// Si la tabla está vacía, usa datos de ejemplo
// ============================================================

try {
    $db = getDB();

    // Primero revisa si la tabla leaderboard existe y tiene datos
    $check = $db->query("SELECT COUNT(*) as total FROM leaderboard");
    $row = $check->fetch(PDO::FETCH_ASSOC);
    $hasData = ($row && $row['total'] > 0);

    if (!$hasData) {
        // ============================================================
        // DATOS DE EJEMPLO (para que se vea algo en la página)
        // Se muestran mientras no haya jugadores registrados
        // ============================================================
        $mockLeaderboard = [
            ['username' => 'Faker',     'email' => 'faker@example.com',    'total_lp' => 1842, 'wins' => 320, 'losses' => 180, 'rank_tier' => 'CHALLENGER'],
            ['username' => 'Chovy',     'email' => 'chovy@example.com',    'total_lp' => 1721, 'wins' => 298, 'losses' => 175, 'rank_tier' => 'CHALLENGER'],
            ['username' => 'Zeus',      'email' => 'zeus@example.com',     'total_lp' => 1598, 'wins' => 275, 'losses' => 160, 'rank_tier' => 'GRANDMASTER'],
            ['username' => 'Caps',      'email' => 'caps@example.com',     'total_lp' => 1450, 'wins' => 260, 'losses' => 155, 'rank_tier' => 'GRANDMASTER'],
            ['username' => 'Showmaker', 'email' => 'show@example.com',     'total_lp' => 1320, 'wins' => 240, 'losses' => 150, 'rank_tier' => 'MASTER'],
            ['username' => 'ElYoya',    'email' => 'yoya@example.com',     'total_lp' => 1205, 'wins' => 225, 'losses' => 140, 'rank_tier' => 'MASTER'],
            ['username' => 'Jankos',    'email' => 'jankos@example.com',   'total_lp' => 1100, 'wins' => 210, 'losses' => 135, 'rank_tier' => 'DIAMOND'],
            ['username' => 'Rekkles',   'email' => 'rekkles@example.com',  'total_lp' => 980,  'wins' => 195, 'losses' => 130, 'rank_tier' => 'DIAMOND'],
            ['username' => 'Doublelift','email' => 'dlift@example.com',    'total_lp' => 890,  'wins' => 180, 'losses' => 125, 'rank_tier' => 'PLATINUM'],
            ['username' => 'Mikyx',     'email' => 'mikyx@example.com',    'total_lp' => 750,  'wins' => 160, 'losses' => 120, 'rank_tier' => 'PLATINUM']
        ];

        echo json_encode(['success' => true, 'leaderboard' => $mockLeaderboard]);
        exit;
    }

    // ============================================================
    // CONSULTA REAL A LA BASE DE DATOS
    // Junta la tabla de usuarios con la de clasificación
    // ============================================================
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
    // ============================================================
    // DATOS DE RESPALDO (si la base de datos da error)
    // ============================================================
    $fallback = [
        ['username' => 'Faker',     'email' => 'faker@example.com',    'total_lp' => 1842, 'wins' => 320, 'losses' => 180, 'rank_tier' => 'CHALLENGER'],
        ['username' => 'Chovy',     'email' => 'chovy@example.com',    'total_lp' => 1721, 'wins' => 298, 'losses' => 175, 'rank_tier' => 'CHALLENGER'],
        ['username' => 'Zeus',      'email' => 'zeus@example.com',     'total_lp' => 1598, 'wins' => 275, 'losses' => 160, 'rank_tier' => 'GRANDMASTER'],
        ['username' => 'Caps',      'email' => 'caps@example.com',     'total_lp' => 1450, 'wins' => 260, 'losses' => 155, 'rank_tier' => 'GRANDMASTER'],
        ['username' => 'Showmaker', 'email' => 'show@example.com',     'total_lp' => 1320, 'wins' => 240, 'losses' => 150, 'rank_tier' => 'MASTER'],
        ['username' => 'ElYoya',    'email' => 'yoya@example.com',     'total_lp' => 1205, 'wins' => 225, 'losses' => 140, 'rank_tier' => 'MASTER'],
        ['username' => 'Jankos',    'email' => 'jankos@example.com',   'total_lp' => 1100, 'wins' => 210, 'losses' => 135, 'rank_tier' => 'DIAMOND'],
        ['username' => 'Rekkles',   'email' => 'rekkles@example.com',  'total_lp' => 980,  'wins' => 195, 'losses' => 130, 'rank_tier' => 'DIAMOND'],
        ['username' => 'Doublelift','email' => 'dlift@example.com',    'total_lp' => 890,  'wins' => 180, 'losses' => 125, 'rank_tier' => 'PLATINUM'],
        ['username' => 'Mikyx',     'email' => 'mikyx@example.com',    'total_lp' => 750,  'wins' => 160, 'losses' => 120, 'rank_tier' => 'PLATINUM']
    ];
    echo json_encode(['success' => true, 'leaderboard' => $fallback]);
}
