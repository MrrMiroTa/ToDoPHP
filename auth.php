<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // REGISTER
    if ($action === 'register') {
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        echo json_encode(['success' => true]);
        exit;
    }

    // LOGIN
    if ($action === 'login') {
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
        exit;
    }

    // LOGOUT
    if ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid action']);