# 🚀 Laravel Untuk Pemula (Super Singkat)

> **Tutorial Laravel paling mudah untuk pemula yang BARU PERTAMA KALI**  
> Dari install alat → bikin project → buka di VS Code

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)


---

## 📋 Daftar Isi

- [Install Alat (WAJIB)](#1️⃣-install-alat-wajib)
- [Buat Project Laravel](#2️⃣-buat-project-laravel)
- [Jalankan Laravel](#3️⃣-jalankan-laravel)
- [Buka di VS Code](#4️⃣-buka-di-vs-code)
- [Yang Perlu Diingat](#5️⃣-yang-perlu-diingat-pemula)
- [Troubleshooting](#-troubleshooting)
- [Langkah Selanjutnya](#-langkah-selanjutnya)

---

## 1️⃣ Install Alat (WAJIB)

Install 3 alat ini saja:

### 1. XAMPP (PHP + MySQL)
- 📥 Download: [https://www.apachefriends.org](https://www.apachefriends.org)
- ✅ Install seperti biasa
- 🎯 Pilih komponen: Apache + MySQL + PHP

### 2. Composer
- 📥 Download: [https://getcomposer.org/download/](https://getcomposer.org/download/)
- ✅ Install dengan default settings
- 🎯 Pastikan centang "Add to PATH"

### 3. VS Code
- 📥 Download: [https://code.visualstudio.com/](https://code.visualstudio.com/)
- ✅ Install seperti biasa
- 💡 Recommended Extensions:
  - Laravel Blade Snippets
  - PHP Intelephense

---

### ✔️ Cek Instalasi

Buka **CMD** atau **PowerShell**, ketik:

```bash
php -v
```

```bash
composer -V
```

**Kalau muncul versi → OK ✅**

Contoh output yang benar:
```
PHP 8.2.12 (cli) ...
Composer version 2.6.5 ...
```

---

## 2️⃣ Buat Project Laravel

### Langkah-langkah:

**1. Buka CMD / PowerShell**

**2. Masuk ke folder bebas:**

```bash
cd C:\laravel
```

> 💡 Kalau folder `laravel` belum ada, buat dulu:
> ```bash
> mkdir C:\laravel
> cd C:\laravel
> ```

**3. Bikin project Laravel:**

```bash
composer create-project laravel/laravel belajar
```

⏳ **Tunggu 2-5 menit** (download dependencies)

**4. Masuk ke folder project:**

```bash
cd belajar
```

---

## 3️⃣ Jalankan Laravel

Di dalam folder project, jalankan:

```bash
php artisan serve
```

**Output yang muncul:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

### 🌐 Buka di Browser:

```
http://127.0.0.1:8000
```

**Muncul halaman Laravel → BERHASIL 🎉**

> 💡 **Cara Stop Server:**  
> Tekan `Ctrl + C` di CMD/PowerShell

---

## 4️⃣ Buka di VS Code

**Di dalam folder project, ketik:**

```bash
code .
```

**Atau:**
- Buka VS Code
- File → Open Folder
- Pilih folder `belajar`

**Selesai ✅**  
Sekarang kamu sudah siap ngoding Laravel!

---

## 5️⃣ Yang Perlu Diingat Pemula

### 🔧 Command Penting

| Command | Fungsi |
|---------|--------|
| `php artisan serve` | Jalankan server Laravel |
| `Ctrl + C` | Stop server |
| `php artisan migrate` | Jalankan migrasi database |
| `php artisan make:controller NamaController` | Buat controller baru |
| `php artisan make:model NamaModel` | Buat model baru |

### 📁 Folder Penting

```
belajar/
├── routes/
│   └── web.php          ← Tempat buat route/URL
├── resources/
│   └── views/           ← Tempat file HTML/Blade
├── app/
│   └── Http/
│       └── Controllers/ ← Tempat logic aplikasi
└── .env                 ← Konfigurasi database, dll
```

### 🗂️ File yang Sering Dipakai

- `routes/web.php` - Mendefinisikan URL aplikasi
- `resources/views` - File tampilan (HTML)
- `app/Http/Controllers` - Logic aplikasi
- `.env` - Setting database dan konfigurasi

---

## 🛠 Troubleshooting

### ❌ Error: `php` is not recognized

**Solusi:**
- Install ulang XAMPP
- Atau tambahkan PHP ke PATH:
  ```
  C:\xampp\php
  ```

### ❌ Error: `composer` is not recognized

**Solusi:**
- Install ulang Composer
- Restart CMD/PowerShell setelah install

### ❌ Port 8000 sudah dipakai

**Solusi:**
```bash
php artisan serve --port=8001
```

Lalu buka: `http://127.0.0.1:8001`

### ❌ Error saat `composer create-project`

**Solusi:**
- Cek koneksi internet
- Atau jalankan:
  ```bash
  composer global update
  ```

---

## 🎯 Langkah Selanjutnya

Setelah instalasi berhasil, kamu bisa lanjut belajar:

### 1️⃣ Membuat Route Pertama

Edit file `routes/web.php`:

```php
Route::get('/halo', function () {
    return 'Halo Laravel!';
});
```

Buka: `http://127.0.0.1:8000/halo`

---

### 2️⃣ Membuat View Pertama

**Buat file:** `resources/views/welcome-saya.blade.php`

```html
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Saya</title>
</head>
<body>
    <h1>Selamat Datang di Laravel!</h1>
    <p>Ini halaman pertama saya</p>
</body>
</html>
```

**Update route** di `routes/web.php`:

```php
Route::get('/welcome', function () {
    return view('welcome-saya');
});
```

Buka: `http://127.0.0.1:8000/welcome`

---

### 3️⃣ Membuat Controller Pertama

**Buat controller:**

```bash
php artisan make:controller HaloController
```

**Edit file** `app/Http/Controllers/HaloController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HaloController extends Controller
{
    public function index()
    {
        return view('welcome-saya');
    }
    
    public function tampilNama($nama)
    {
        return "Halo, " . $nama;
    }
}
```

**Update route:**

```php
use App\Http\Controllers\HaloController;

Route::get('/controller', [HaloController::class, 'index']);
Route::get('/nama/{nama}', [HaloController::class, 'tampilNama']);
```

Buka: `http://127.0.0.1:8000/nama/Budi`

---

## 📚 Resource Belajar

- 📖 [Dokumentasi Laravel](https://laravel.com/docs)
- 🎥 [Laravel dari Nol (YouTube)](https://www.youtube.com/results?search_query=laravel+indonesia)
- 💬 [Laravel Indonesia (Facebook Group)](https://www.facebook.com/groups/laravel/)
- 🌐 [Stackoverflow](https://stackoverflow.com/questions/tagged/laravel)

---

## 🤝 Kontribusi

Kalau ada yang mau ditambahkan atau diperbaiki:
1. Fork repository ini
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

---

## 📝 Lisensi

Project ini menggunakan lisensi **MIT License** - lihat file [LICENSE](LICENSE) untuk detail.

---

## 👨‍💻 Author

Dibuat dengan ❤️ untuk membantu pemula belajar Laravel

---

## ⭐ Support

Kalau tutorial ini membantu, kasih **Star** ya! 🌟

**Happy Coding! 🚀**

---

### 📌 Catatan Tambahan

> 💡 **Tips:**
> - Selalu jalankan `php artisan serve` sebelum ngoding
> - Jangan lupa stop server dengan `Ctrl + C` kalau sudah selesai
> - Backup code secara berkala
> - Join komunitas Laravel Indonesia untuk tanya-tanya

> ⚠️ **Perhatian:**
> - Tutorial ini menggunakan Laravel versi terbaru
> - Pastikan PHP minimal versi 8.2
> - Gunakan XAMPP versi terbaru

---

**Selamat belajar Laravel! 🎉**
