<?php

class Connection
{
    // --- Konfigurasi Database ---
    private $host = '127.0.0.1';
    private $db_name = 'todolist';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    // -----------------------------

    private static ?Connection $instance = null;
    private ?PDO $conn = null;

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Terjadi kesalahan saat menghubungkan ke database.");
        }
    }


    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->conn;
    }


    private function __clone() {}


    public function __wakeup() {}


    public static function testConnection(): bool
    {
        try {
            self::getConnection()->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            error_log("Test connection failed: " . $e->getMessage());
            return false;
        }
    }
}
