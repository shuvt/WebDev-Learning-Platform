<?php
require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASS);
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    if ($e->getCode() == 1049) {
        try {
            $temp_dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
            $temp_db = new PDO($temp_dsn, DB_USER, DB_PASS);
            
            $temp_db->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $db = new PDO($dsn, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e2) {
            die("Ошибка создания базы данных: " . $e2->getMessage());
        }
    } else {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

try {
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO users (username, email, password, role) VALUES 
                  ('admin', 'admin@example.com', '$hashed_password', 'admin')");
    } else {
    }
    
} catch(Exception $e) {
    echo "Ошибка создания таблицы: " . $e->getMessage() . "<br>";
}
?>