# 🗂️ PHP To-Do List — Implementasi Arsitektur MVC

Proyek ini adalah implementasi **MVC (Model-View-Controller)** menggunakan **PHP Native** dan **Bootstrap 5**.  
Aplikasi ini memiliki fitur utama **autentikasi login pengguna** serta **pengelolaan daftar tugas (To-Do List)** yang siap di-deploy ke **Microsoft Azure App Service**.

---

## 🧩 Arsitektur Aplikasi

Struktur direktori proyek mengikuti pola MVC:
- **Models** → Berisi logika data dan query database (`UserModel.php`, `TaskModel.php`)
- **Controller** → Menangani request dari user, validasi, dan menghubungkan Model dengan View (`AuthController.php`, `TaskController.php`)
- **View** → Menampilkan antarmuka pengguna berbasis HTML & Bootstrap (`View/auth/`, `View/task/`)
- **Database** → Koneksi singleton PDO dan skrip migrasi/seeding (`Connection.php`, `MigrateSeed.php`)

```plaintext
mvc-app/
├── Controller/
│   ├── AuthController.php
│   └── TaskController.php
├── Database/
│   ├── Connection.php
│   └── MigrateSeed.php
├── Models/
│   ├── TaskModel.php
│   └── UserModel.php
├── View/
│   ├── auth/
│   │   └── login.php
│   └── task/
│       ├── edit_task.php
│       └── task.php
├── .gitignore
├── index.php
└── README.md
```

---

## ⚙️ Fitur Utama

### 🔐 Autentikasi & Keamanan Sesi
- Login aman dengan verifikasi `password_hash` (`bcrypt`).
- Pengaturan cookie sesi (`HttpOnly`, `SameSite=Lax`, dan `Secure` saat menggunakan HTTPS/Azure).
- Regenerasi ID sesi setelah login berhasil untuk mencegah *session fixation*.
- Error handling generik ke pengguna dengan detail log tersimpan di `error_log`.
- Sanitasi input dan proteksi XSS pada output View (`htmlspecialchars`).

### ✅ Pengelolaan Tugas (To-Do List)
- Tambah tugas baru (metode `POST`).
- Edit dan perbarui deskripsi serta status tugas (metode `POST`).
- Toggle status tugas (Pending ↔ Completed) via metode `POST`.
- Hapus tugas via metode `POST` dengan dialog konfirmasi.
- Filter tugas berdasarkan status (`all`, `pending`, `completed`).

---

## 🧠 Teknologi yang Digunakan

| Komponen | Teknologi |
|-----------|------------|
| Bahasa Pemrograman | PHP 8+ |
| Basis Data | MySQL / MariaDB (Mendukung Azure Database for MySQL Flexible Server via SSL) |
| UI Framework | Bootstrap 5 & Bootstrap Icons |
| Pola Desain | MVC (Model-View-Controller) |
| Database Layer | Native PHP PDO (Prepared Statements) |

---

## 🛠️ Instalasi & Konfigurasi Lokal

### 1️⃣ Clone Repositori
```bash
git clone https://github.com/Zhacksdev/mvc-app.git
cd mvc-app
```

### 2️⃣ Siapkan Database Lokal
Buat database lokal bernama `todolist` di MySQL Anda.

### 3️⃣ Jalankan Script Migrasi & Seeder
Jalankan perintah berikut untuk membuat tabel `users` dan `tasks` (lengkap dengan `created_at` dan `updated_at`) serta menyisipkan data awal:
```bash
php Database/MigrateSeed.php
```
> ⚠️ **Peringatan:** File ini menjalankan perintah `DROP TABLE`. Jalankan **hanya sekali** saat inisialisasi awal database (termasuk pada database Azure), jangan dijalankan ulang setelah terdapat data penting.

### 4️⃣ Konfigurasi Database & Environment Variables
Aplikasi membaca kredensial database melalui environment variable, dengan fallback default untuk lingkungan lokal:

| Variabel Lingkungan | Nilai Default Lokal | Keterangan |
|---------------------|---------------------|------------|
| `DB_HOST` | `127.0.0.1` | Host database MySQL |
| `DB_NAME` | `todolist` | Nama database |
| `DB_USER` | `root` | Username database |
| `DB_PASS` | `(kosong)` | Password database |

### 5️⃣ Jalankan di Localhost
Gunakan PHP built-in web server:
```bash
php -S localhost:8000
```
Buka browser pada: `http://localhost:8000`

---

## ☁️ Deployment ke Microsoft Azure

### Konfigurasi Azure App Service
1. **Application Settings**: Pada portal Azure (**App Service** > **Settings** > **Configuration** > **Application settings**), tambahkan:
   - `DB_HOST` = `<nama-server-mysql>.mysql.database.azure.com`
   - `DB_NAME` = `todolist`
   - `DB_USER` = `<admin-username>`
   - `DB_PASS` = `<admin-password>`
2. **Koneksi SSL**: Saat `DB_HOST` diset, aplikasi secara otomatis mengaktifkan SSL dengan sertifikat CA (`/etc/ssl/certs/ca-certificates.crt`) yang kompatibel dengan Azure Database for MySQL.
3. **Repository GitHub**: Pastikan repository telah diubah status visibilitasnya menjadi **Private** di GitHub sebelum menghubungkan Azure Deployment Center.

---

## 🌐 URL Aplikasi Azure & Akun Pengujian

- **URL Aplikasi**: `https://<nama-aplikasi-anda>.azurewebsites.net`
- **Akun Uji (Seeder)**:
  - Username: `john.doe`
  - Password: `password123`
  - *(Alternatif)*: `jane.doe` / `password123`, `budi` / `password123`

---

## 📚 Catatan Keamanan & Arsitektur

- **Prepared Statements**: Seluruh query SQL menggunakan PDO prepared statements dengan parameter binding untuk mencegah SQL Injection.
- **XSS Protection**: Data teks disimpan murni di database dan di-*escape* menggunakan `htmlspecialchars()` saat ditampilkan pada View.
- **HTTP Methods**: Operasi yang mengubah data (store, update, toggle, delete) wajib menggunakan metode HTTP `POST` guna mencegah CSRF dan trigger tidak disengaja oleh link prefetching.
