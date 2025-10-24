<?php
session_start();

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
// 🔹 ROUTER UTAMA
// ============================================================
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
    // 🔸 TASK: TOGGLE STATUS
    // --------------------------------------------------------
    case 'task_toggle':
        requireLogin();
        (new TaskController())->toggle();
        break;

    // --------------------------------------------------------
    // 🔸 TASK: HAPUS
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
