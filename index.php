<?php

// ============================================================
// 🔹 SESI LOGIN & COOKIE KEAMANAN
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (!empty(getenv('DB_HOST')));

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// ============================================================
// 🔹 AUTOLOAD CONTROLLER
// ============================================================
require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/TaskController.php';

// ============================================================
// 🔹 DAPATKAN ROUTE (default = home)
// ============================================================
$route = $_GET['route'] ?? 'home';

// ============================================================
// 🔹 HELPER: CEK LOGIN
// ============================================================
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?route=login');
        exit;
    }
}

// ============================================================
// 🔹 ROUTER UTAMA DENGAN PENANGANAN ERROR
// ============================================================
try {
    switch ($route) {

        // --------------------------------------------------------
        // 🔸 LOGIN PAGE & ACTION
        // --------------------------------------------------------
        case 'login':
            $auth = new AuthController();

            // Jika sudah login → langsung ke halaman task
            if (!empty($_SESSION['user_id'])) {
                header('Location: index.php?route=task');
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $auth->login();
            } else {
                $auth->showLoginForm();
            }
            break;

        // --------------------------------------------------------
        // 🔸 LOGOUT
        // --------------------------------------------------------
        case 'logout':
            $auth = new AuthController();
            $auth->logout();
            break;

        // --------------------------------------------------------
        // 🔸 TASK: LIST SEMUA
        // --------------------------------------------------------
        case 'task':
            requireLogin();
            (new TaskController())->index();
            break;

        // --------------------------------------------------------
        // 🔸 TASK: TAMBAH
        // --------------------------------------------------------
        case 'task_store':
            requireLogin();
            (new TaskController())->store();
            break;

        // --------------------------------------------------------
        // 🔸 TASK: FORM EDIT
        // --------------------------------------------------------
        case 'task_edit':
            requireLogin();
            (new TaskController())->edit();
            break;

        // --------------------------------------------------------
        // 🔸 TASK: UPDATE
        // --------------------------------------------------------
        case 'task_update':
            requireLogin();
            (new TaskController())->update();
            break;

        // --------------------------------------------------------
        // 🔸 TASK: TOGGLE STATUS (POST)
        // --------------------------------------------------------
        case 'task_toggle':
            requireLogin();
            (new TaskController())->toggle();
            break;

        // --------------------------------------------------------
        // 🔸 TASK: HAPUS (POST)
        // --------------------------------------------------------
        case 'task_delete':
            requireLogin();
            (new TaskController())->destroy();
            break;

        // --------------------------------------------------------
        // 🔸 DEFAULT (HOME)
        // --------------------------------------------------------
        case 'home':
        default:
            if (!empty($_SESSION['user_id'])) {
                header('Location: index.php?route=task');
            } else {
                header('Location: index.php?route=login');
            }
            exit;
    }
} catch (Throwable $e) {
    error_log("Unhandled Application Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo "Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.";
}
