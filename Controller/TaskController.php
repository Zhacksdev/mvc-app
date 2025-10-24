<?php
require_once __DIR__ . '/../Models/TaskModel.php';

class TaskController
{
    private Task $taskModel;
    private int $userId;
    private string $userName;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit;
        }

        $this->taskModel = new Task();
        $this->userId = (int) $_SESSION['user_id'];
        $this->userName = $_SESSION['user_nama'] ?? 'User';
    }

    /** ===============================
     * 🔹 Halaman utama daftar tugas
     * =============================== */
    public function index(): void
    {
        $filter = $_GET['filter'] ?? 'all';

        switch ($filter) {
            case 'pending':
            case 'completed':
                $tasks = $this->taskModel->getFilteredByUser($this->userId, $filter);
                break;
            default:
                $tasks = $this->taskModel->getAllByUser($this->userId);
                break;
        }

        $userName = $this->userName;
        require __DIR__ . '/../View/task/task.php';
    }

    /** ===============================
     * 🔹 Tambah tugas baru
     * =============================== */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=task');
            exit;
        }

        $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));

        if ($deskripsi === '') {
            $_SESSION['task_error'] = 'Deskripsi tugas tidak boleh kosong.';
        } else {
            $this->taskModel->create($this->userId, $deskripsi);
            $_SESSION['task_success'] = 'Tugas berhasil ditambahkan.';
        }

        header('Location: index.php?route=task');
        exit;
    }

    /** ===============================
     * 🔹 Form edit tugas
     * =============================== */
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['task_error'] = 'ID tugas tidak valid.';
            header('Location: index.php?route=task');
            exit;
        }

        $task = $this->taskModel->findById($id, $this->userId);

        if (!$task) {
            $_SESSION['task_error'] = 'Tugas tidak ditemukan atau bukan milik Anda.';
            header('Location: index.php?route=task');
            exit;
        }

        require __DIR__ . '/../View/task/edit_task.php';
    }

    /** ===============================
     * 🔹 Update tugas
     * =============================== */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=task');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $status = $_POST['status'] ?? 'pending';

        if ($id <= 0 || $deskripsi === '') {
            $_SESSION['task_error'] = 'Deskripsi tidak boleh kosong.';
            header("Location: index.php?route=edit-task&id={$id}");
            exit;
        }

        $data = [
            'deskripsi' => $deskripsi,
            'status' => $status,
        ];

        $this->taskModel->update($id, $this->userId, $data);
        $_SESSION['task_success'] = 'Tugas berhasil diperbarui.';

        header('Location: index.php?route=task');
        exit;
    }

    /** ===============================
     * 🔹 Ubah status tugas
     * =============================== */
    public function toggle(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->taskModel->toggleStatus($id, $this->userId);
            $_SESSION['task_success'] = 'Status tugas berhasil diperbarui.';
        } else {
            $_SESSION['task_error'] = 'ID tugas tidak valid.';
        }

        header('Location: index.php?route=task');
        exit;
    }

    /** ===============================
     * 🔹 Hapus tugas
     * =============================== */
    public function destroy(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->taskModel->delete($id, $this->userId);
            $_SESSION['task_success'] = 'Tugas berhasil dihapus.';
        } else {
            $_SESSION['task_error'] = 'ID tugas tidak valid.';
        }

        header('Location: index.php?route=task');
        exit;
    }
}
