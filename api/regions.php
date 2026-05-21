<?php

// ============================================================
// REGIONES DE LEAGUE OF LEGENDS
// Devuelve la lista de regiones donde se puede buscar un jugador
// ============================================================

header('Content-Type: application/json');

// Lista de regiones que Riot Games tiene para el juego
echo json_encode([
    "regiones" => [
        "euw1",  // Europa Occidental
        "br1",   // Brasil
        "eun1",  // Europa del Norte y Este
        "jp1",   // Japón
        "kr",    // Corea
        "la1",   // América Latina Norte
        "la2",   // América Latina Sur
        "me1",   // Medio Oriente
        "na1",   // América del Norte
        "oc1",   // Oceanía
        "ru",    // Rusia
        "sg2",   // Singapur
        "tr1",   // Turquía
        "tw2",   // Taiwán
        "vn2"    // Vietnam
    ]
]);
