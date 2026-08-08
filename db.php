<?php
// Set PHP timezone
date_default_timezone_set('Asia/Phnom_Penh'); // Change to your local timezone if different

$host = 'localhost';
$dbname = 'todo_db';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Force MySQL connection to match PHP timezone offset
    $offset = (new DateTime())->format('P');
    $pdo->exec("SET time_zone = '$offset'");

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}