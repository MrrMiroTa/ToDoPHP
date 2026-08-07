<?php
$host = 'localhost';
$dbname = 'todo_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET time_zone = '+00:00'");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$columns = $pdo->query("SHOW COLUMNS FROM tasks")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('user_id', $columns)) {
    $pdo->exec("ALTER TABLE tasks ADD COLUMN user_id INT NOT NULL AFTER id");
    $pdo->exec("ALTER TABLE tasks ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
}
if (!in_array('completed_at', $columns)) {
    $pdo->exec("ALTER TABLE tasks ADD COLUMN completed_at TIMESTAMP NULL DEFAULT NULL AFTER created_at");
}
?>