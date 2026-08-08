<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Task Flow</title>
    
    <!-- Modern Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --font-main: 'Inter', system-ui, -apple-system, sans-serif;
            --bg-canvas: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary-accent: #4f46e5;
            --primary-hover: #4338ca;
            --border-light: #e2e8f0;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
        }

        .app-card {
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }

        .form-control-custom, .form-select-custom {
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .btn-custom-primary {
            background-color: var(--primary-accent);
            color: #ffffff;
            border-radius: 10px;
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-custom-primary:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
        }

        .filter-btn {
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            color: var(--text-muted);
            border: none;
            background: transparent;
            transition: all 0.15s ease;
        }

        .filter-btn.active {
            background-color: #e0e7ff;
            color: var(--primary-accent);
        }

        .task-item {
            border-bottom: 1px solid var(--border-light);
            padding: 0.85rem 0.5rem;
            transition: background 0.15s ease;
        }

        .task-item:last-child {
            border-bottom: none;
        }

        .task-item:hover {
            background-color: #f8fafc;
        }

        .task-checkbox {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            accent-color: var(--primary-accent);
        }

        .task-text {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
        }

        .task-text.completed {
            text-decoration: line-through;
            color: var(--text-muted);
        }

        /* Priority Badges */
        .badge-priority {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            text-transform: capitalize;
        }

        .badge-priority-high { background-color: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
        .badge-priority-medium { background-color: #fffbebf; color: #d97706; border: 1px solid #fde68a; }
        .badge-priority-low { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        /* Timestamps Meta */
        .task-meta-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .task-meta-time.completed-time {
            color: #16a34a;
            font-weight: 500;
        }

        .task-meta-time.overdue {
            color: #dc2626;
            font-weight: 600;
        }

        .action-icon {
            color: #94a3b8;
            transition: color 0.15s ease;
            cursor: pointer;
        }

        .action-icon:hover {
            color: #ef4444;
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar glass-nav sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 text-dark fw-bold" href="#">
            <span class="d-inline-flex align-items-center justify-content-center text-white rounded-3 p-2" style="width: 32px; height: 32px; background-color: var(--primary-accent);">
                <i class="bi bi-check-lg"></i>
            </span>
            <span>Task Flow</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary small">Signed in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <button onclick="logout()" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Logout</button>
        </div>
    </div>
</nav>

<!-- Main Container -->
<main class="container py-5" style="max-width: 760px;">
    
    <div class="app-card p-4 p-md-5">
        
        <!-- Add Task Form -->
        <form id="addTaskForm" class="row g-2 mb-4">
            <div class="col-12 col-md-5">
                <input type="text" id="taskInput" class="form-control form-control-custom" placeholder="What needs to be done?" autocomplete="off" required>
            </div>
            <div class="col-6 col-md-3">
                <select id="priorityInput" class="form-select form-select-custom">
                    <option value="low">Low Priority</option>
                    <option value="medium" selected>Medium Priority</option>
                    <option value="high">High Priority</option>
                </select>
            </div>
            <div class="col-6 col-md-2.5">
                <input type="date" id="dueDateInput" class="form-control form-control-custom">
            </div>
            <div class="col-12 col-md-auto d-grid">
                <button type="submit" class="btn btn-custom-primary d-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-plus-lg"></i> Add
                </button>
            </div>
        </form>

        <!-- Filters & Counter -->
        <div class="d-flex align-items-center justify-content-between pb-3 mb-2 border-bottom">
            <div class="d-flex gap-1">
                <button class="filter-btn active" onclick="setFilter('all', this)">All</button>
                <button class="filter-btn" onclick="setFilter('pending', this)">Pending</button>
                <button class="filter-btn" onclick="setFilter('completed', this)">Completed</button>
            </div>
            <span id="taskCounter" class="small text-muted">0 tasks</span>
        </div>

        <!-- Task List Container -->
        <div id="taskList" class="d-flex flex-column"></div>

    </div>

</main>

<script>
let currentFilter = 'all';
let allTasks = [];

document.addEventListener('DOMContentLoaded', () => {
    const taskInput = document.getElementById('taskInput');
    const priorityInput = document.getElementById('priorityInput');
    const dueDateInput = document.getElementById('dueDateInput');
    const taskList = document.getElementById('taskList');
    const addTaskForm = document.getElementById('addTaskForm');

    async function loadTasks() {
        try {
            const response = await fetch('api.php?action=get_tasks');
            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }
            allTasks = await response.json();
            renderTasks();
        } catch (err) {
            console.error("Error fetching tasks:", err);
        }
    }

    window.setFilter = (filter, element) => {
        currentFilter = filter;
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        renderTasks();
    };

    function formatDateTime(dateTimeStr) {
        if (!dateTimeStr) return '';
        const d = new Date(dateTimeStr);
        return d.toLocaleString([], { 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    function renderTasks() {
        taskList.innerHTML = '';
        
        const filteredTasks = allTasks.filter(task => {
            if (currentFilter === 'pending') return task.status === 'pending';
            if (currentFilter === 'completed') return task.status === 'completed';
            return true;
        });

        document.getElementById('taskCounter').textContent = `${filteredTasks.length} ${filteredTasks.length === 1 ? 'task' : 'tasks'}`;

        if (filteredTasks.length === 0) {
            taskList.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="mb-0 small">No tasks found.</p>
                </div>
            `;
            return;
        }

        const todayStr = new Date().toISOString().split('T')[0];

        filteredTasks.forEach(task => {
            const isCompleted = task.status === 'completed';
            const isOverdue = task.due_date && task.due_date < todayStr && !isCompleted;

            const item = document.createElement('div');
            item.className = 'task-item d-flex align-items-center justify-content-between';
            item.innerHTML = `
                <div class="d-flex align-items-center gap-3 pe-3">
                    <input type="checkbox" class="task-checkbox" ${isCompleted ? 'checked' : ''} 
                        onchange="toggleTask(${task.id}, '${isCompleted ? 'pending' : 'completed'}')">
                    
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="task-text ${isCompleted ? 'completed' : ''}">${escapeHtml(task.task_name)}</span>
                            <span class="badge-priority badge-priority-${task.priority}">${task.priority}</span>
                        </div>
                        
                        <div class="d-flex flex-wrap align-items-center gap-2 task-meta-time">
                            <!-- Created Time -->
                            <span>
                                <i class="bi bi-plus-circle me-1"></i>Created: ${formatDateTime(task.created_at)}
                            </span>

                            <!-- Completed Time -->
                            ${isCompleted && task.completed_at ? `
                                <span class="completed-time">
                                    • <i class="bi bi-check-circle me-1"></i>Ended: ${formatDateTime(task.completed_at)}
                                </span>
                            ` : ''}

                            <!-- Due Date -->
                            ${task.due_date ? `
                                <span class="${isOverdue ? 'overdue' : ''}">
                                    • <i class="bi bi-calendar3 me-1"></i>${isOverdue ? 'Overdue: ' : 'Due: '}${task.due_date}
                                </span>
                            ` : ''}
                        </div>
                    </div>
                </div>

                <button class="btn btn-link p-0 action-icon border-0" onclick="deleteTask(${task.id})">
                    <i class="bi bi-trash3 fs-6"></i>
                </button>
            `;
            taskList.appendChild(item);
        });
    }

    addTaskForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const taskName = taskInput.value.trim();
        if (!taskName) return;

        const newTask = {
            task: taskName,
            priority: priorityInput.value,
            due_date: dueDateInput.value
        };

        taskInput.value = '';
        dueDateInput.value = '';

        await fetch('api.php?action=add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(newTask)
        });
        
        loadTasks();
    });

    window.toggleTask = async (id, status) => {
        await fetch('api.php?action=toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        });
        loadTasks();
    };

    window.deleteTask = async (id) => {
        allTasks = allTasks.filter(t => t.id !== id);
        renderTasks();

        await fetch('api.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
    };

    window.logout = async () => {
        await fetch('auth.php?action=logout', { method: 'POST' });
        window.location.href = 'login.php';
    };

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, match => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[match]));
    }

    loadTasks();
});
</script>
</body>
</html>