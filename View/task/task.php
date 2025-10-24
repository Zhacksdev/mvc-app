<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Variabel dari controller
$userName = $userName ?? 'User';
$tasks = $tasks ?? [];
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .form-delete {
            display: inline-block;
            margin: 0;
        }
    </style>
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php?route=task">
                <i class="bi bi-card-checklist"></i> To-Do List
            </a>
            <span class="navbar-text">
                Halo, <strong><?= htmlspecialchars($userName); ?></strong>!
                <a href="index.php?route=logout" class="btn btn-outline-danger btn-sm ms-2">Logout</a>
            </span>
        </div>
    </nav>

    <main class="container my-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Daftar Tugas Anda</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="bi bi-plus-lg"></i> Tambah Tugas
            </button>
        </div>

        <!-- Alert message -->
        <?php if (isset($_SESSION['task_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['task_error']);
                unset($_SESSION['task_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif (isset($_SESSION['task_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['task_success']);
                unset($_SESSION['task_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%">No.</th>
                                <th style="width:55%">Tugas</th>
                                <th style="width:15%">Status</th>
                                <th style="width:25%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tasks)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">
                                        Belum ada tugas. Yuk tambahkan!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tasks as $i => $task): ?>
                                    <tr>
                                        <td><?= $i + 1; ?></td>
                                        <td><?= htmlspecialchars($task['deskripsi_tugas']); ?></td>
                                        <td>
                                            <?php if ($task['status'] === 'completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?route=task_toggle&id=<?= $task['id']; ?>" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>
                                            <a href="index.php?route=task_edit&id=<?= $task['id']; ?>"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-fill"></i> Edit
                                            </a>
                                            <form action="index.php?route=task_delete&id=<?= $task['id']; ?>"
                                                method="POST" class="form-delete"
                                                onsubmit="return confirm('Yakin hapus tugas ini?');">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash-fill"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?route=task_store" method="POST">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Tambah Tugas Baru</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Tugas:</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                placeholder="Tulis deskripsi tugas..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>