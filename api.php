<?php
session_start();
date_default_timezone_set('UTC');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $_POST['action'] ?? $input['action'] ?? '';

switch ($action) {
    case 'register':
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        if ($username === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Username and password are required']);
            break;
        }
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            break;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo json_encode(['success' => true]);
        break;
    case 'login':
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(['success' => true, 'username' => $user['username']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
        break;
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;
    case 'session':
        echo json_encode(['success' => true, 'loggedIn' => isset($_SESSION['user_id']), 'username' => $_SESSION['username'] ?? null]);
        break;
    case 'read':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            break;
        }
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'tasks' => $tasks]);
        break;
    case 'create':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            break;
        }
        $title = trim($input['title'] ?? '');
        if ($title === '') {
            echo json_encode(['success' => false, 'message' => 'Title is required']);
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;
    case 'update':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            break;
        }
        $id = $input['id'] ?? 0;
        $completed = $input['completed'] ?? 0;
        $completedAt = $completed ? date('Y-m-d H:i:s') : null;
        $stmt = $pdo->prepare("UPDATE tasks SET completed = ?, completed_at = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$completed, $completedAt, $id, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        break;
    case 'delete':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            break;
        }
        $id = $input['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
