<?php
// Pastikan variabel $task dikirim dari controller
if (!isset($task)) {
    echo "<p class='text-danger text-center mt-5'>Data tugas tidak ditemukan.</p>";
    exit;
}

// 🔹 Pastikan $task selalu berupa array
if (is_object($task)) {
    // Ubah object (misal dari PDO::FETCH_OBJ) jadi array associative
    $task = [
        'id' => $task->id ?? null,
        'deskripsi_tugas' => $task->deskripsi_tugas ?? '',
        'status' => $task->status ?? 'pending',
    ];
}

// Validasi basic kalau data kosong/null
if (empty($task) || !isset($task['id'])) {
    echo "<p class='text-danger text-center mt-5'>Data tugas tidak valid.</p>";
    exit;
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Tugas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 0.75rem;
        }
    </style>
</head>

<body>

    <!-- 🔹 Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../../tasks">
                <i class="bi bi-card-checklist me-2"></i> To-Do List
            </a>
        </div>
    </nav>

    <!-- 🔹 Form Edit -->
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Edit Tugas
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- 🔹 Action menyesuaikan routing -->
                        <form action="index.php?route=task_update" method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']); ?>">
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Tugas:</label>
                                <textarea
                                    class="form-control"
                                    id="deskripsi"
                                    name="deskripsi"
                                    rows="3"
                                    required><?php echo htmlspecialchars($task['deskripsi_tugas']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Status:</label>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="status"
                                        id="status_pending"
                                        value="pending"
                                        <?php echo ($task['status'] === 'pending') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_pending">Pending</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="status"
                                        id="status_completed"
                                        value="completed"
                                        <?php echo ($task['status'] === 'completed') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_completed">Completed</label>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <a href="../../tasks" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save-fill"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>