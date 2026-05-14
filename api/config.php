<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'nexus_hub');
define('DB_USER', 'root');
define('DB_PASS', '');

define('GEMINI_API_KEY', 'AIzaSyBKVj7bvVfswJsYWfKw_2a3PWQOOtN28vI');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');
define('RIOT_API_KEY', 'RGAPI-744d55bb-f07d-4643-8d86-23cfbe0d40fc');

function getDB() {
    try {
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
}

function callGeminiAPI($message) {
    $url = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;
    
$contextPrompt = "Eres un asistente experto en League of Legends. Responde SIEMPRE de forma CONCISA y DIRECTA.
    
    IMPORTANTE: Cuando menciones objetos o runas, DEBES usar estas etiquetas EXACTAS sin excepción:
    - Para objetos: [[item:NOMBRE]] 
    - Para runas: [[rune:NOMBRE]]
    
    ESTRUCTURA DE RUNAS (OBLIGATORIO):
    - 1 KEYSTONE (runa principal): ej. Conqueror, Electrocute, Arcane Comet
    - 3 RUNAS PRIMARIAS del mismo árbol: ej. Triumph, Legend Alacrity, Last Stand
    - 2 RUNAS SECUNDARIAS: ej. Bone Plating, Overgrowth
    
    LISTA DE NOMBRES VALIDOS:
    OBJETOS: Espada Amplificadora, Daga, Botas de Lucidez, Zhonyas, Rabadon, Sombrero de Morello, Velo del Hakim, Tormento de Liandry, Práctica del Vacío, Filo del Infinito, Guantelete del Inmortal, Rocafuerte, Armadura de Warmog, Espada del Rey Exiliado, Cuchilla Obsidiana, Hilibrand, Eco de Kole, Malla, Guantelete de Hechicero, Botas de Mercurio, Botas de Placas, Cuchilla Ardiente, Cortafuegos
    
    RUNAS KEYSTONE: Electrocute, Predator, Hail of Blades, Press the Attack, Lethal Tempo, Fleet Footwork, Conqueror, Grasp, Aftershock, Guardian, Glacial Augment, Kleptomancy, Unsealed Spellbook, Arcane Comet, Aery, Phase Rush
    
    RUNAS PRIMARIAS/SECUNDARIAS: Triumph, Legend Alacrity, Legend Haste, Legend Bloodline, Coup de Grace, Cut Down, Last Stand, Manaflow Band, Transcendencia, Absolute Focus, Scorch, Waterwalking, Gathering Storm, Celerity, Nullifying Orb, Cheap Shot, Taste of Blood, Sudden Impact, Zombie Ward, Ghost Poro, Eyeball Collection, Treasure Hunter, Relentless Hunter, Ultimate Hunter, Font of Life, Demolish, Shield Bash, Conditioning, Second Wind, Bone Plating, Overgrowth, Revitalize, Unflinching, Perfect Timing, Future's Market, Minion Dematerializer, Biscuit Delivery, Cosmic Insight, Approach Velocity, Jack Of All Trades
    
    REGLAS PARA MATCHUPS:
    - Cuando preguntes por matchups favorables o desfavorables, SOLO menciona los nombres de los campeones enemigos
    - NO uses etiquetas [[item:]] ni [[rune:]] en respuestas de matchups
    - Ejemplo: \"Matchups favorables para Garen: Darius, Nasus, Yorick\" (sin objetos)
    
    Ejemplo de respuesta CORRECTA para BUILD:
    Build para Garen:
    Objetos: [[item:Espada Amplificadora]] [[item:Daga]] [[item:Filo del Infinito]] [[item:Botas de Mercurio]] [[item:Espada del Rey Exiliado]] [[item:Guantelete del Inmortal]]
    
    Runas:
    [[rune:Conqueror]] [[rune:Triumph]] [[rune:Legend Alacrity]] [[rune:Coup de Grace]]
    Secundarias: [[rune:Bone Plating]] [[rune:Overgrowth]]
    
    Ejemplo de respuesta CORRECTA para MATCHUPS:
    Matchups favorables para Garen: Darius, Fiora, Yorick, Nasus, Sett
    Matchups desfavorables para Garen: Quinn, Vayne, Teemo, Gankplank, Kayle
    
    IMPORTANTE: 
    - SIEMPRE dame 6 OBJETOS en la build
    - SIEMPRE dame 6 RUNAS (1 keystone + 3 primarias + 2 secundarias)
    - Para matchups: SOLO nombres de campeones, sin objetos ni runas
    - NO salgas de la estructura.";
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $contextPrompt . "\n\nUsuario: " . $message]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.9,
            'maxOutputTokens' => 4096
        ]
    ];
    
    $streamOptions = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true
        ]
    ];
    
    $streamContext = stream_context_create($streamOptions);
    $response = file_get_contents($url, false, $streamContext);
    
    if ($response === false) {
        return "Lo siento, hubo un error al conectar con la IA. Intenta de nuevo.";
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return $result['candidates'][0]['content']['parts'][0]['text'];
    } elseif (isset($result['error'])) {
        return "Error: " . $result['error']['message'];
    }
    
    return "No pude obtener una respuesta. Intenta de nuevo.";
}