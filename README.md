Real Time Chat App

Aplikasi chat realtime sederhana menggunakan Laravel 12.  
Project ini memiliki fitur private chat dan group chat dengan realtime message menggunakan Laravel Reverb.

Fitur
- Login dan Register
- Private Chat
- Group Chat
- Realtime Message
- Status User Online
- Tampilan sederhana menggunakan Blade dan Tailwind CSS

Teknologi Yang Digunakan

- Laravel 12
- Laravel Breeze
- Laravel Reverb
- Laravel Echo
- Blade
- Tailwind CSS
- MySQL

Cara Menjalankan Project
1. Clone Repository

Download project dari GitHub:

```bash
git clone https://github.com/sintiadewianggraini04/realtime-chat.git
```

Masuk ke folder project:

```bash
cd real-time-chat
```

 2. Install Semua Dependency

Install dependency Laravel:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```


3. Buat File .env

Copy file `.env.example` menjadi `.env`

Jika menggunakan Windows:

```bash
copy .env.example .env
```

Jika menggunakan Linux/Mac:

```bash
cp .env.example .env
```

4. Generate APP_KEY

Jalankan perintah berikut:

```bash
php artisan key:generate
```

5. Konfigurasi Database

Buka file `.env`

Cari bagian berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Ubah menjadi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realtime_chat
DB_USERNAME=root
DB_PASSWORD=
```

6. Buat Database

Buka phpMyAdmin lalu buat database baru dengan nama:

```text
realtime_chat
```

7. Jalankan Migration

Jalankan perintah:

```bash
php artisan migrate
```

Perintah ini digunakan untuk membuat tabel database secara otomatis.

8. Jalankan Laravel Reverb

Buka terminal baru lalu jalankan:

```bash
php artisan reverb:start
```

Perintah ini digunakan untuk menjalankan realtime websocket server.

9. Jalankan Vite

Buka terminal baru lalu jalankan:

```bash
npm run dev
```

Perintah ini digunakan untuk menjalankan asset frontend seperti CSS dan JavaScript.

---
10. Jalankan Laravel Server

Buka terminal baru lalu jalankan:

```bash
php artisan serve
```

Jika berhasil akan muncul:

```text
http://127.0.0.1:8000
```

Buka link tersebut di browser.

---

Cara Menggunakan Aplikasi
Register Akun

- Buka aplikasi
- Klik Register
- Isi nama, email, dan password
- Login ke aplikasi

---
Private Chat

- Pilih user yang tersedia
- Kirim pesan
- Pesan akan muncul realtime tanpa refresh

---
Group Chat

- Buat group chat
- Tambahkan member group
- Kirim pesan ke dalam group