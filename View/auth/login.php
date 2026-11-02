<?php
// Ingat, session_start() sudah harus dipanggil di index.php (pintu masuk utama)
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - To-Do List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>

<body>

    <div class="card shadow-sm login-card">
        <div class="card-body p-4 p-md-5">
            <h3 class="card-title text-center mb-4">Login Akun</h3>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- ✅ Form diarahkan langsung ke AuthController -->
            <form action="index.php?route=login" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
            </form>

            <hr class="my-4">

            <div class="text-center text-muted">
                <small>
                    Hint (dari Seeder):<br>
                    User: <code>john.doe</code> / Pass: <code>password123</code><br>
                </small>
            </div>
        </div>
    </div>

</body>

</html>