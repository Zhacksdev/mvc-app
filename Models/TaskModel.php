<?php

require_once __DIR__ . '/../Database/Connection.php';

class Task
{
    private string $table = 'tasks';
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    /* Fungsi Ambil Task */
    public function getAllByUser(int $user_id): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY created_at DESC
            ");
            $stmt->execute([':user_id' => $user_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return is_array($result) ? $result : [];
        } catch (PDOException $e) {
            error_log("Task::getAllByUser → " . $e->getMessage());
            return [];
        }
    }

    /* Fungsi Ambil Task dengan Filter Status*/
    public function getFilteredByUser(int $user_id, string $status): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table}
                WHERE user_id = :user_id AND status = :status
                ORDER BY created_at DESC
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':status'  => $status
            ]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return is_array($result) ? $result : [];
        } catch (PDOException $e) {
            error_log("Task::getFilteredByUser → " . $e->getMessage());
            return [];
        }
    }

    /* Fungsi ammbil task berdasarkan ID User */
    public function findById(int $id, int $user_id): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table}
                WHERE id = :id AND user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute([
                ':id'      => $id,
                ':user_id' => $user_id
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log("Task::findById → " . $e->getMessage());
            return null;
        }
    }

    /* Fungsi Create Task baru */
    public function create(int $user_id, string $deskripsi): bool
    {
        try {
            $deskripsi = trim($deskripsi);
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (user_id, deskripsi_tugas, status)
                VALUES (:user_id, :deskripsi, 'pending')
            ");
            return $stmt->execute([
                ':user_id'   => $user_id,
                ':deskripsi' => $deskripsi
            ]);
        } catch (PDOException $e) {
            error_log("Task::create → " . $e->getMessage());
            return false;
        }
    }

    /* Fungsi Update task */
    public function update(int $id, int $user_id, array $data): bool
    {
        try {
            $deskripsi = trim($data['deskripsi'] ?? '');
            $status = $data['status'] ?? 'pending';

            // validasi status agar sesuai ENUM
            if (!in_array($status, ['pending', 'completed'])) {
                $status = 'pending';
            }

            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET deskripsi_tugas = :deskripsi, status = :status, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                ':deskripsi' => $deskripsi,
                ':status'    => $status,
                ':id'        => $id,
                ':user_id'   => $user_id
            ]);
        } catch (PDOException $e) {
            error_log("Task::update → " . $e->getMessage());
            return false;
        }
    }

    /* Fungsi untuk Ubah Status Task */
    public function toggleStatus(int $id, int $user_id): bool
    {
        try {
            $task = $this->findById($id, $user_id);
            if (!$task) return false;

            $newStatus = ($task['status'] === 'completed') ? 'pending' : 'completed';
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET status = :status, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                ':status'   => $newStatus,
                ':id'       => $id,
                ':user_id'  => $user_id
            ]);
        } catch (PDOException $e) {
            error_log("Task::toggleStatus → " . $e->getMessage());
            return false;
        }
    }

    /* Fungsi Hapus task */
    public function delete(int $id, int $user_id): bool
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table}
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id'       => $id,
                ':user_id'  => $user_id
            ]);
        } catch (PDOException $e) {
            error_log("Task::delete → " . $e->getMessage());
            return false;
        }
    }
}
