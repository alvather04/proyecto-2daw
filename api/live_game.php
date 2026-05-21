<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config.php';

$gameName = $_GET['gameName'] ?? '';
$tagLine = $_GET['tagLine'] ?? '';
$region = $_GET['region'] ?? 'la1';

if (!$gameName || !$tagLine) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros gameName o tagLine']);
    exit;
}

$regionMap = [
    'na1' => 'na1', 'euw1' => 'euw1', 'eun1' => 'eun1',
    'kr' => 'kr', 'br1' => 'br1', 'la1' => 'la1',
    'la2' => 'la2', 'jp1' => 'jp1', 'ru' => 'ru',
    'oc1' => 'oc1', 'sg2' => 'sg2', 'tw2' => 'tw2', 'tr1' => 'tr1'
];
$platform = $regionMap[$region] ?? 'la1';

$regionRouting = [
    'na1' => 'americas', 'euw1' => 'europe', 'eun1' => 'europe',
    'kr' => 'asia', 'br1' => 'americas', 'la1' => 'americas',
    'la2' => 'americas', 'jp1' => 'asia', 'ru' => 'europe',
    'oc1' => 'sea', 'sg2' => 'sea', 'tw2' => 'sea', 'tr1' => 'europe'
];
$routing = $regionRouting[$region] ?? 'americas';

$accountUrl = "https://{$routing}.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . urlencode($gameName) . "/" . urlencode($tagLine) . "?api_key=" . RIOT_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $accountUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$accountResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(404);
    echo json_encode(['error' => 'Invocador no encontrado']);
    exit;
}

$accountData = json_decode($accountResponse, true);
$puuid = $accountData['puuid'];

$summonerUrl = "https://{$platform}.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/{$puuid}?api_key=" . RIOT_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $summonerUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$summonerResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(404);
    echo json_encode(['error' => 'Invocador no encontrado en el juego']);
    exit;
}

$summonerData = json_decode($summonerResponse, true);
$summonerId = $summonerData['id'];

$liveUrl = "https://{$platform}.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/{$summonerId}?api_key=" . RIOT_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $liveUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$liveResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 404) {
    echo json_encode(['inGame' => false, 'message' => 'El invocador no está en partida']);
} elseif ($httpCode === 200 && $liveResponse) {
    $gameData = json_decode($liveResponse, true);
    $gameData['inGame'] = true;
    $gameData['summonerInfo'] = $summonerData;
    echo json_encode($gameData);
} else {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Error al obtener partida', 'code' => $httpCode]);
}
