<?php

// ============================================================
// INSTALACIÓN DE LA BASE DE DATOS
// Crea la base de datos 'nexus_hub' y todas las tablas necesarias
// Solo hay que ejecutar esto una vez
// ============================================================

// Datos para conectarse a MySQL
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Conecta al servidor de bases de datos
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ============================================================
    // CREAR LA BASE DE DATOS
    // ============================================================
    $sql = "CREATE DATABASE IF NOT EXISTS nexus_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    echo "Base de datos 'nexus_hub' creada/verificada<br>";

    // Empieza a usar la base de datos
    $conn->exec("USE nexus_hub");

    // ============================================================
    // TABLA: users (usuarios)
    // Guarda la información de cada persona registrada
    // id, nombre de usuario, correo, contraseña (encriptada), avatar, fecha de registro, último ingreso
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        avatar VARCHAR(255) DEFAULT 'https://ddragon.leagueoflegends.com/cdn/14.1.1/img/profileicon/1.png',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        last_login DATETIME DEFAULT NULL
    )";
    $conn->exec($sql);
    echo "Tabla 'users' creada/verificada<br>";

    // ============================================================
    // TABLA: posts (publicaciones del foro)
    // Cada publicación tiene: título, descripción, imagen, etiqueta, votos
    // Está relacionada con el usuario que la creó
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        image VARCHAR(500) DEFAULT '',
        tag ENUM('Estrategia', 'Campeones', 'Parches', 'Competitivo', 'General') DEFAULT 'General',
        votes INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at),
        INDEX idx_tag (tag)
    )";
    $conn->exec($sql);
    echo "Tabla 'posts' creada/verificada<br>";

    // ============================================================
    // TABLA: post_votes (votos de publicaciones)
    // Cada usuario puede votar una vez por publicación (arriba o abajo)
    // No se permiten votos repetidos
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS post_votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        post_id INT NOT NULL,
        vote_type ENUM('up', 'down') NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_post_vote (user_id, post_id)
    )";
    $conn->exec($sql);
    echo "Tabla 'post_votes' creada/verificada<br>";

    // ============================================================
    // TABLA: comments (comentarios)
    // Los comentarios que se escriben en las publicaciones
    // Cada uno pertenece a una publicación y a un usuario
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        votes INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_post_id (post_id)
    )";
    $conn->exec($sql);
    echo "Tabla 'comments' creada/verificada<br>";

    // ============================================================
    // TABLA: comment_votes (votos de comentarios)
    // Cada usuario puede votar una vez por comentario
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS comment_votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        comment_id INT NOT NULL,
        vote_type ENUM('up', 'down') NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_comment_vote (user_id, comment_id)
    )";
    $conn->exec($sql);
    echo "Tabla 'comment_votes' creada/verificada<br>";

    // ============================================================
    // TABLA: contact_messages (mensajes de contacto)
    // Guarda los mensajes del formulario de contacto
    // Si el usuario está registrado, guarda su ID también
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_created_at (created_at)
    )";
    $conn->exec($sql);
    echo "Tabla 'contact_messages' creada/verificada<br>";

    // ============================================================
    // TABLA: chats (conversaciones con la IA)
    // Guarda lo que el usuario preguntó y lo que respondió Gemini
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        response TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id)
    )";
    $conn->exec($sql);
    echo "Tabla 'chats' creada/verificada<br>";

    // ============================================================
    // TABLA: saved_posts (publicaciones guardadas)
    // Guarda las publicaciones que cada usuario marcó como favoritas
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS saved_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        post_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_saved (user_id, post_id)
    )";
    $conn->exec($sql);
    echo "Tabla 'saved_posts' creada/verificada<br>";

    // ============================================================
    // TABLA: leaderboard (clasificación)
    // Guarda el rango y estadísticas de cada usuario
    // ============================================================
    $sql = "CREATE TABLE IF NOT EXISTS leaderboard (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        rank_tier VARCHAR(20) DEFAULT 'UNRANKED',
        rank_division VARCHAR(5) DEFAULT 'I',
        lp INT DEFAULT 0,
        wins INT DEFAULT 0,
        losses INT DEFAULT 0,
        queue_type VARCHAR(30) DEFAULT 'RANKED_SOLO_5x5',
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "Tabla 'leaderboard' creada/verificada<br>";

    echo "<br><strong>✓ Setup completo!</strong><br>";
    echo "<a href='../index.html'>Volver al inicio</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
