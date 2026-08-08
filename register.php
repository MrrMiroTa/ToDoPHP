<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Task App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-sm border-0 rounded-3" style="width: 100%; max-width: 400px;">
    <div class="card-body p-4">
        <h4 class="text-center mb-4 text-primary fw-bold">Create Account</h4>
        
        <div id="alertBox" class="alert alert-danger d-none"></div>

        <form id="registerForm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Sign Up</button>
        </form>

        <p class="text-center mt-3 mb-0 text-muted">
            Already have an account? <a href="login.php" class="text-decoration-none">Login</a>
        </p>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const alertBox = document.getElementById('alertBox');
    alertBox.classList.add('d-none');

    const res = await fetch('auth.php?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            username: document.getElementById('username').value,
            password: document.getElementById('password').value
        })
    });

    const data = await res.json();
    if (data.success) {
        window.location.href = 'login.php?registered=1';
    } else {
        alertBox.textContent = data.message;
        alertBox.classList.remove('d-none');
    }
});
</script>
</body>
</html>