<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'nexus_hub');
define('DB_USER', 'root');
define('DB_PASS', '');

define('GEMINI_API_KEY', 'AIzaSyBrl40LbaJwa47Zut_P7zxLLZdECFSuM6s');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');

function getDB() {
    try {
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
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
    
    $context = "Eres un asistente especializado en League of Legends. 
    Conoces todo sobre campeones, objetos, runas, estrategias, parches y competitivo.
    Respondes en español de forma amigable y útil.
    Si te preguntan sobre otros temas, puedes responder pero siempre intentando relacionarlo con gaming.";
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $context . "\n\nUsuario: " . $message]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.9,
            'maxOutputTokens' => 1024
        ]
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
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

session_start();

function getUserId() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 'guest_' . uniqid();
    }
    return $_SESSION['user_id'];
}