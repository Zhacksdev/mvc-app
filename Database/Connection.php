<?php

class Connection
{
    // --- Konfigurasi Database ---
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $charset = 'utf8mb4';
    // -----------------------------

    private static ?Connection $instance = null;
    private ?PDO $conn = null;

    private function __construct()
    {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->db_name = getenv('DB_NAME') ?: 'todolist';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Tambahkan opsi SSL saat DB_HOST terisi (MySQL Azure mewajibkan koneksi SSL)
        if (!empty(getenv('DB_HOST'))) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

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
