<?php

// ============================================================
// PERFIL DE INVOCADOR (Riot Games)
// Busca datos de un jugador de League of Legends usando
// su nombre de invocador y etiqueta (gameName#tagLine)
// Recibe por GET: gameName, tagLine, region
// Devuelve: { jugador, clasificaciones }
// ============================================================

// ============================================================
// NOMBRES DE LAS COLAS DE JUEGO
// Convierte los códigos a nombres que todos entienden
// ============================================================
$nombresColas = [
    "RANKED_SOLO_5x5" => "Clasificatoria Solo/Dúo",
    "RANKED_FLEX_SR"   => "Clasificatoria Flexible",
    "CHERRY"           => "Arena"
];

// Configuración para ver errores (solo para pruebas)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once 'config.php';

// ============================================================
// 1. REVISAR QUE LLEGUEN LOS DATOS NECESARIOS
// ============================================================
if (!isset($_GET['gameName']) || !isset($_GET["tagLine"]) || !isset($_GET["region"])) {
    http_response_code(400);
    echo json_encode(["error" => "Falta el parámetro gameName/tagLine/region"]);
    exit;
}

$gameName = $_GET["gameName"];
$tagLine = $_GET["tagLine"];
$region = $_GET["region"];

// ============================================================
// 2. BUSCAR LA CUENTA EN RIOT GAMES
// ============================================================
// Pregunta a Riot si existe ese jugador
$url_base = "https://americas.api.riotgames.com";

$ch = curl_init("$url_base/riot/account/v1/accounts/by-riot-id/$gameName/$tagLine");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Riot-Token: " . RIOT_API_KEY,
    "Accept-Language: es-ES,es;q=0.9"
]);

$responseAccount = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Si no se encontró la cuenta
if ($httpCode !== 200 || $curlError) {
    http_response_code($httpCode ?: 500);
    echo json_encode([
        "error" => "Invocador no encontrado",
        "details" => $curlError ?: "El invocador $gameName#$tagLine no existe",
        "http_code" => $httpCode
    ]);
    exit;
}

// Lee los datos que devolvió Riot
$accountData = json_decode($responseAccount, true);
if (!isset($accountData['puuid'])) {
    http_response_code(404);
    echo json_encode(["error" => "Invocador no encontrado - datos inválidos"]);
    exit;
}

$puuid = $accountData['puuid'];

// ============================================================
// 3. BUSCAR LAS CLASIFICACIONES DEL JUGADOR
// ============================================================
// Pregunta a Riot en qué rango está el jugador
$url_base = "https://$region.api.riotgames.com";

$ch = curl_init("$url_base/lol/league/v4/entries/by-puuid/$puuid");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Riot-Token: " . RIOT_API_KEY,
    "Accept-Language: es-ES,es;q=0.9"
]);

$responseLeague = curl_exec($ch);
$leagueHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Organiza las clasificaciones
$ligasProcesadas = [];

if ($leagueHttpCode === 200) {
    $leagueData = json_decode($responseLeague, true);
    if (is_array($leagueData)) {
        foreach ($leagueData as $liga) {
            $tipo = $liga['queueType'];
            $nombreAmigable = $nombresColas[$tipo] ?? $tipo;
            $totalPartidas = $liga['wins'] + $liga['losses'];
            $winrate = ($totalPartidas > 0) ? round(($liga['wins'] / $totalPartidas) * 100, 2) . "%" : "0%";

            $ligasProcesadas[] = [
                "tipo_cola" => $nombreAmigable,
                "rango" => $liga["tier"],
                "division" => $liga['rank'],
                "puntos" => $liga['leaguePoints'],
                "stats" => [
                    "victorias" => $liga["wins"],
                    "derrotas" => $liga['losses'],
                    "total" => $totalPartidas,
                    "winrate" => $winrate
                ],
                "en_racha" => $liga['hotStreak'] ? "Si" : "No"
            ];
        }
    }
}

// ============================================================
// 4. ARMAR LA RESPUESTA Y ENVIARLA
// ============================================================
$customResponse = [
    "jugador" => [
        "nombre" => $gameName,
        "tag" => $tagLine,
        "puuid" => $puuid
    ],
    "clasificaciones" => $ligasProcesadas
];

header('Content-Type: application/json');
echo json_encode($customResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
