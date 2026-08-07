<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div id="auth-section">
            <h1>Todo App</h1>

            <form id="login-form" class="auth-form">
                <input type="text" id="login-username" placeholder="Username" required>
                <input type="password" id="login-password" placeholder="Password" required>
                <button type="submit">Login</button>
                <button type="button" id="show-register">Create an account</button>
            </form>

            <form id="register-form" class="auth-form" style="display:none;">
                <input type="text" id="register-username" placeholder="Choose a username" required>
                <input type="password" id="register-password" placeholder="Choose a password" required>
                <button type="submit">Register</button>
                <button type="button" id="show-login">Back to Login</button>
            </form>

            <p id="auth-message"></p>
        </div>

        <div id="todo-section" style="display:none;">
            <div class="header">
                <h1>Todo List</h1>
                <div class="header-right">
                    <span id="user-greeting"></span>
                    <button id="logout-btn">Logout</button>
                </div>
            </div>
            <form id="todo-form">
                <input type="text" id="todo-input" placeholder="Add a new task..." required>
                <button type="submit">Add Task</button>
            </form>
            <ul id="todo-list"></ul>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
