<?php
require_once __DIR__ . '/../Models/UserModel.php';

class AuthController
{
    public function showLoginForm(): void
    {
        require_once __DIR__ . '/../View/auth/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Username dan password wajib diisi.';
            header('Location: index.php?route=login');
            exit;
        }

        try {
            $userModel = new User();
            $user = $userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID untuk keamanan
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                unset($_SESSION['error']);

                header('Location: index.php?route=task');
            } else {
                $_SESSION['error'] = 'Username atau password salah.';
                header('Location: index.php?route=login');
            }
        } catch (Throwable $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
            header('Location: index.php?route=login');
        }
        exit;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION = [];
        session_unset();
        session_destroy();

        // Hapus cookie session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        header('Location: index.php?route=login');
        exit;
    }
}
