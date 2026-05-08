<?php

$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE DATABASE IF NOT EXISTS nexus_hub";
    $conn->exec($sql);
    echo "Database created successfully<br>";
    
    $conn->exec("USE nexus_hub");
    
    $sql = "CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        response TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at)
    )";
    $conn->exec($sql);
    echo "Table 'chats' created successfully<br>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
echo "<br><strong>Setup completo!</strong><br>";
echo "<a href='../index.html'>Volver al inicio</a>";