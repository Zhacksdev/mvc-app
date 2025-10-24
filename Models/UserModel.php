<?php

require_once __DIR__ . '/../Database/Connection.php';

class User
{
    private string $table = 'users';
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    // Fungsi Cari user dengan Username
    public function findByUsername(string $username): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => htmlspecialchars(trim($username))]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("User::findByUsername → " . $e->getMessage());
            return null;
        }
    }


    // Fungsi Tambah User
    public function create(string $nama, string $username, string $password): bool
    {
        try {
            if ($this->findByUsername($username)) {
                // Username sudah ada
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (nama, username, password)
                VALUES (:nama, :username, :password)
            ");
            return $stmt->execute([
                ':nama'      => htmlspecialchars(trim($nama)),
                ':username'  => htmlspecialchars(trim($username)),
                ':password'  => $hashedPassword
            ]);
        } catch (PDOException $e) {
            error_log("User::create → " . $e->getMessage());
            return false;
        }
    }

    // Fungsi Ambil Data User dengan ID
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("User::findById → " . $e->getMessage());
            return null;
        }
    }

    // Fungsi Verifikasi Password
    public function verifyPassword(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
