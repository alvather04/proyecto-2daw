<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config.php';

$region = $_GET['region'] ?? 'la1';

$regionMap = [
    'na1' => 'na1', 'euw1' => 'euw1', 'eun1' => 'eun1',
    'kr' => 'kr', 'br1' => 'br1', 'la1' => 'la1',
    'la2' => 'la2', 'jp1' => 'jp1', 'ru' => 'ru',
    'oc1' => 'oc1', 'sg2' => 'sg2', 'tw2' => 'tw2', 'tr1' => 'tr1'
];
$platform = $regionMap[$region] ?? 'la1';

$url = "https://{$platform}.api.riotgames.com/lol/platform/v3/champion-rotations?api_key=" . RIOT_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    echo $response;
} else {
    http_response_code($httpCode);
    echo json_encode(['error' => 'No se pudo obtener la rotación', 'code' => $httpCode]);
}
