<?php

// ===================================================================
// KONFIGURASI DATABASE
// Membaca environment variable dengan fallback lokal
// ===================================================================
$dbHost  = getenv('DB_HOST') ?: '127.0.0.1';
$dbName  = getenv('DB_NAME') ?: 'todolist';
$dbUser  = getenv('DB_USER') ?: 'root';
$dbPass  = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

// ===================================================================
// PASSWORD UNTUK SEEDER
// Ini password yang akan dipakai untuk semua user dummy
// ===================================================================
$plainPassword = "password123";
$passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

// ===================================================================
// PROSES MIGRASI DAN SEEDING
// ===================================================================

// Set DSN (Data Source Name)
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Opsi SSL saat DB_HOST terisi (diperlukan untuk Azure Database for MySQL)
if (!empty(getenv('DB_HOST'))) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

try {
    // 1. Buat koneksi PDO
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    echo "Koneksi ke database '{$dbName}' berhasil! <br><hr>\n";

    // 2. MIGRASI: Definisikan SQL untuk membuat tabel
    $sqlMigration = "
        DROP TABLE IF EXISTS `tasks`;
        DROP TABLE IF EXISTS `users`;

        CREATE TABLE `users` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `username` VARCHAR(50) NOT NULL,
          `password` VARCHAR(255) NOT NULL,
          `nama` VARCHAR(100) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `tasks` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `user_id` INT(11) NOT NULL,
          `deskripsi_tugas` TEXT NOT NULL,
          `status` ENUM('pending','completed') NOT NULL DEFAULT 'pending',
          `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    // 3. Eksekusi skrip Migrasi
    $pdo->exec($sqlMigration);
    echo "<b>Migrasi berhasil:</b> Tabel 'users' dan 'tasks' sukses dibuat.<br>\n";

    // 4. SEEDING: Masukkan data 'users' menggunakan Prepared Statements
    $stmtUser = $pdo->prepare("INSERT INTO `users` (username, password, nama) VALUES (?, ?, ?)");
    
    $users = [
        ['john.doe', $passwordHash, 'John Doe'],
        ['jane.doe', $passwordHash, 'Jane Doe'],
        ['budi', $passwordHash, 'Budi Santoso']
    ];

    foreach ($users as $user) {
        $stmtUser->execute($user);
    }
    echo "<b>Seeding berhasil:</b> 3 user (john.doe, jane.doe, budi) ditambahkan. Password: '{$plainPassword}'<br>\n";
    
    // 5. SEEDING: Masukkan data 'tasks'
    // Kita bisa pakai exec() lagi untuk query multi-line
    $sqlSeedTasks = "
        INSERT INTO `tasks` (user_id, deskripsi_tugas, status, created_at) VALUES
        (1, 'Selesaikan migration & seeder database', 'completed', '2025-10-23 01:00:00'),
        (1, 'Buat Model User dan Task', 'completed', '2025-10-23 02:00:00'),
        (1, 'Buat AuthController untuk login/logout', 'pending', '2025-10-23 03:00:00'),
        (1, 'Kerjakan halaman utama TaskController', 'pending', '2025-10-23 04:00:00'),
        (2, 'Beli bahan makanan untuk makan malam', 'pending', '2025-10-23 05:00:00'),
        (2, 'Bayar tagihan listrik bulan ini', 'completed', '2025-10-23 06:00:00'),
        (2, 'Cuci motor', 'pending', '2025-10-23 07:00:00');
    ";
    
    $pdo->exec($sqlSeedTasks);
    echo "<b>Seeding berhasil:</b> Data tasks untuk user 'john.doe' dan 'jane.doe' ditambahkan.<br>\n";
    echo "<hr>✅ <b>SEMUA SELESAI!</b> Database kamu sudah siap digunakan.\n";

} catch (PDOException $e) {
    echo "Gagal melakukan migrasi/seeding: <br>" . $e->getMessage();
}
