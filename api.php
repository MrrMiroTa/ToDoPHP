<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized user']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// GET TASKS FOR LOGGED-IN USER
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_tasks') {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC, id DESC");
    $stmt->execute([$userId]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // ADD TASK
    if ($action === 'add') {
        $task = trim($data['task'] ?? '');
        $priority = in_array($data['priority'] ?? '', ['low', 'medium', 'high']) ? $data['priority'] : 'medium';
        $dueDate = !empty($data['due_date']) ? $data['due_date'] : null;

        if (!empty($task)) {
            // created_at is automatically stamped by MySQL NOW() in server local time
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task_name, priority, due_date, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$userId, $task, $priority, $dueDate]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task description required.']);
        }
        exit;
    }

    // TOGGLE STATUS WITH TIMESTAMP
    if ($action === 'toggle') {
        $id = $data['id'] ?? 0;
        $status = $data['status'] === 'completed' ? 'completed' : 'pending';
        
        // If task is completed, record current exact local timestamp, otherwise clear it
        $completedAt = ($status === 'completed') ? date('Y-m-d H:i:s') : null;

        $stmt = $pdo->prepare("UPDATE tasks SET status = ?, completed_at = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$status, $completedAt, $id, $userId]);
        echo json_encode(['success' => true]);
        exit;
    }

    // DELETE TASK
    if ($action === 'delete') {
        $id = $data['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid action']);