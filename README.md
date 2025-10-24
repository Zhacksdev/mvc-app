# 🗂️ PHP To-Do List — Implementasi Arsitektur VMC

Proyek ini adalah contoh implementasi **VMC (View-Model-Controller)** sederhana menggunakan **PHP Native** dan **Bootstrap**.  
Aplikasi ini memiliki fitur utama **autentikasi login pengguna** serta **pengelolaan daftar tugas (To-Do List)**.

---

## 🧩 Arsitektur VMC

Struktur proyek ini mengikuti pola **VMC (View-Model-Controller)**:
- **Model** → Berisi logika database (misalnya `User.php` dan `Task.php`)
- **Controller** → Menangani request dari user dan berinteraksi dengan Model & View
- **View** → Menampilkan halaman antarmuka (HTML + Bootstrap)

```plaintext
app/
├── Controllers/
│   ├── AuthController.php
│   └── TaskController.php
├── Models/
│   ├── UserModel.php
│   └── TaskModel.php
├── Views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── tasks/
│   │   ├── index.php
│   │   └── form.php
│   └── layouts/
│       └── main.php
└── Database/
    └── Connection.php
```

---

## ⚙️ Fitur Utama

### 🔐 Autentikasi
- Login dengan username & password.
- Logout session pengguna.
- Validasi user melalui database.

### ✅ To-Do List
- Tambah task baru.
- Edit dan update task.
- Toggle status (pending ↔ completed).
- Hapus task.
- Filter berdasarkan status (pending / completed).

### 💅 Tampilan (View)
- Menggunakan **Bootstrap 5** agar tampilan modern dan responsif.
- UI sederhana dan intuitif.

---

## 🧠 Teknologi yang Digunakan

| Komponen | Teknologi |
|-----------|------------|
| Bahasa Pemrograman | PHP 8+ |
| Basis Data | MySQL / MariaDB |
| UI Framework | Bootstrap 5 |
| Arsitektur | VMC (View-Model-Controller) |
| ORM | Native PDO (tanpa framework) |

---

## 🛠️ Instalasi & Konfigurasi

### 1️⃣ Clone Repositori
```bash
git clone https://github.com/username/php-vmc-todolist.git
cd php-vmc-todolist
```

### 2️⃣ Buat Database
Buat database baru bernama `todolist`.

### 3️⃣ Jalankan Script Migrasi & Seeder
Jalankan file berikut untuk membuat tabel `users` dan `tasks` serta menambahkan data awal:
```bash
php database/migrate_and_seed.php
```

### 4️⃣ Konfigurasi Koneksi Database
Buka file:
```
app/Database/Connection.php
```
dan sesuaikan:
```php
private static string $host = '127.0.0.1';
private static string $dbname = 'todolist';
private static string $username = 'root';
private static string $password = '';
```

### 5️⃣ Jalankan di Localhost
Gunakan PHP built-in server:
```bash
php -S localhost:8000
```

Lalu buka di browser:
```
http://localhost:8000
```

---

## 👤 Akun Login Default (Seeder)
Setelah seeding, kamu bisa login dengan:

| Username | Password |
|-----------|-----------|
| `john.doe` | `password123` |
| `jane.doe` | `password123` |

---

## 📸 Preview Tampilan
> (Tambahkan screenshot halaman login & todo list jika ada)

---

## 📚 Catatan Tambahan

- Menggunakan `PDO` dengan prepared statements untuk keamanan dari SQL Injection.
- Struktur **VMC** memisahkan antara tampilan, logika bisnis, dan koneksi data untuk memudahkan pengembangan dan perawatan.
- Semua data user dan task disimpan di database MySQL.

---

## 🧑‍💻 Pengembang
Dibuat oleh **Zhacksdev**  
Sebagai implementasi sederhana konsep **VMC** dalam PHP Native.
