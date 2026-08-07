const authSection = document.getElementById('auth-section');
const todoSection = document.getElementById('todo-section');
const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');
const authMessage = document.getElementById('auth-message');
const todoForm = document.getElementById('todo-form');
const input = document.getElementById('todo-input');
const list = document.getElementById('todo-list');
const userGreeting = document.getElementById('user-greeting');
const logoutBtn = document.getElementById('logout-btn');

document.getElementById('show-register').addEventListener('click', () => {
    loginForm.style.display = 'none';
    registerForm.style.display = 'flex';
    authMessage.textContent = '';
});

document.getElementById('show-login').addEventListener('click', () => {
    registerForm.style.display = 'none';
    loginForm.style.display = 'flex';
    authMessage.textContent = '';
});

async function postJson(url, data) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const text = await res.text();
        let dataParsed;
        try {
            dataParsed = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON response:', text);
            return { success: false, message: 'Server error' };
        }
        return dataParsed;
    } catch (e) {
        console.error('Fetch error:', e);
        return { success: false, message: 'Network error' };
    }
}

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    authMessage.textContent = '';
    const username = document.getElementById('login-username').value.trim();
    const password = document.getElementById('login-password').value;
    const data = await postJson('api.php?action=login', { username, password });
    if (data.success) {
        showTodoSection(data.username);
    } else {
        authMessage.textContent = data.message || 'Login failed';
    }
});

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    authMessage.textContent = '';
    const username = document.getElementById('register-username').value.trim();
    const password = document.getElementById('register-password').value;
    const data = await postJson('api.php?action=register', { username, password });
    if (data.success) {
        authMessage.style.color = '#27ae60';
        authMessage.textContent = 'Registration successful! Please login.';
        registerForm.style.display = 'none';
        loginForm.style.display = 'flex';
    } else {
        authMessage.style.color = '#e74c3c';
        authMessage.textContent = data.message || 'Registration failed';
    }
});

logoutBtn.addEventListener('click', async () => {
    await postJson('api.php?action=logout', {});
    showAuthSection();
});

function showAuthSection() {
    authSection.style.display = 'block';
    todoSection.style.display = 'none';
    loginForm.reset();
    registerForm.reset();
    authMessage.textContent = '';
    authMessage.style.color = '#e74c3c';
}

function showTodoSection(username) {
    authSection.style.display = 'none';
    todoSection.style.display = 'block';
    userGreeting.textContent = `Hello, ${username}`;
    loadTasks();
}

function renderTasks(tasks) {
    list.innerHTML = '';
    if (tasks.length === 0) {
        list.innerHTML = '<li class="empty-state">No tasks yet. Add one above!</li>';
        return;
    }
    tasks.forEach(task => {
        const li = document.createElement('li');
        li.className = `todo-item ${task.completed ? 'completed' : ''}`;
        li.dataset.id = task.id;

        const created = task.created_at ? new Date(task.created_at.replace(' ', 'T') + 'Z').toLocaleString() : '';
        const completed = task.completed_at ? `Completed: ${new Date(task.completed_at.replace(' ', 'T') + 'Z').toLocaleString()}` : '';

        li.innerHTML = `
            <div class="info">
                <div class="content">
                    <input type="checkbox" ${task.completed ? 'checked' : ''}>
                    <span class="text">${escapeHtml(task.title)}</span>
                </div>
                <div class="meta">Added: ${created} ${completed ? '| ' + completed : ''}</div>
            </div>
            <button class="delete-btn">Delete</button>
        `;

        const checkbox = li.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', () => toggleTask(task.id, !task.completed));

        const deleteBtn = li.querySelector('.delete-btn');
        deleteBtn.addEventListener('click', () => deleteTask(task.id));

        list.appendChild(li);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function checkSession() {
    try {
        const res = await fetch('api.php?action=session');
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid session JSON:', text);
            showAuthSection();
            return;
        }
        if (data.success && data.loggedIn) {
            showTodoSection(data.username);
        } else {
            showAuthSection();
        }
    } catch (e) {
        console.error('Session check error:', e);
        showAuthSection();
    }
}

async function loadTasks() {
    try {
        const res = await fetch('api.php?action=read');
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid tasks JSON:', text);
            return;
        }
        if (data.success) {
            renderTasks(data.tasks);
        } else {
            authMessage.textContent = data.message || 'Failed to load tasks';
            authMessage.style.color = '#e74c3c';
        }
    } catch (e) {
        console.error('Load tasks error:', e);
    }
}

async function addTask(title) {
    const data = await postJson('api.php?action=create', { title });
    if (data.success) {
        loadTasks();
    }
}

async function toggleTask(id, completed) {
    const data = await postJson('api.php?action=update', { id, completed });
    if (data.success) {
        loadTasks();
    }
}

async function deleteTask(id) {
    const data = await postJson('api.php?action=delete', { id });
    if (data.success) {
        loadTasks();
    }
}

todoForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const title = input.value.trim();
    if (title) {
        addTask(title);
        input.value = '';
    }
});

checkSession();