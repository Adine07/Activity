# 🚀 Activity — Employee Activity Monitoring System

> Sistem monitoring aktivitas karyawan berbasis Laravel untuk membantu management dan boss memantau pekerjaan, progres, dan aktivitas harian karyawan secara realtime.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=for-the-badge&logo=laravel" />
  <img src="https://img.shields.io/badge/Livewire-3-purple?style=for-the-badge&logo=livewire" />
  <img src="https://img.shields.io/badge/TailwindCSS-4-38BDF8?style=for-the-badge&logo=tailwindcss" />
  <img src="https://img.shields.io/badge/MaryUI-Modern-blueviolet?style=for-the-badge" />
  <img src="https://img.shields.io/badge/PHP-8.3-blue?style=for-the-badge&logo=php" />
</p>

---

# 📌 About Project

**Activity** adalah aplikasi monitoring aktivitas karyawan yang dibuat menggunakan **Laravel 13** dengan kombinasi modern stack seperti:

* ⚡ Laravel 13
* 🔥 Livewire 3
* 🎨 Mary UI
* 💨 Tailwind CSS

Aplikasi ini memungkinkan boss / management untuk:

* ✅ Melihat aktivitas harian karyawan
* ✅ Dashboard modern & responsive
* ✅ Sistem yang ringan dan cepat

---

# 🖼️ Tech Stack

| Technology   | Description          |
| ------------ | -------------------- |
| Laravel 13   | PHP Framework        |
| Livewire 3   | Reactive Component   |
| Mary UI      | UI Component Library |
| Tailwind CSS | Utility First CSS    |
| MySQL        | Database             |
| Vite         | Frontend Build Tool  |

---

# ⚙️ System Requirements

Pastikan environment sudah memenuhi requirement berikut:

| Requirement | Version |
| ----------- | ------- |
| PHP         | >= 8.3  |
| Composer    | Latest  |
| Node.js     | >= 20   |
| NPM         | Latest  |
| MySQL       | >= 8    |
| Git         | Latest  |

---

# 📥 Installation Guide

## 1️⃣ Clone Repository

```bash
git clone https://github.com/Adine07/Activity.git
```

Masuk ke folder project:

```bash
cd Activity
```

---

## 2️⃣ Install PHP Dependencies

```bash
composer install
```

---

## 3️⃣ Install Node Dependencies

```bash
npm install
```

---

## 4️⃣ Setup Environment

Copy file `.env`

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## 5️⃣ Configure Database

Buka file `.env` lalu sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=activity
DB_USERNAME=root
DB_PASSWORD=
```

---

## 6️⃣ Run Migration & Seeder

```bash
php artisan migrate --seed
```

---

## 7️⃣ Build Frontend Assets

Development mode:

```bash
npm run dev
```

Production build:

```bash
npm run build
```

---

## 8️⃣ Run Local Server

```bash
php artisan serve
```

Akses project di:

```txt
http://127.0.0.1:8000
```

---

# 🔐 Default Login

```txt
Email    : admin@mail.com
Password : password
```

> Sesuaikan jika credential default berbeda di Seeder.

---

# 📂 Project Structure

```txt
app/
├── Livewire/
├── Models/
├── Http/
resources/
├── views/
├── css/
├── js/
database/
├── migrations/
├── seeders/
routes/
```

---

# 🧩 Features

* 📊 Dashboard Monitoring
* 👨‍💼 Employee Activity Tracking
* 📝 Daily Activity Report
* ⚡ Realtime UI with Livewire
* 📱 Responsive Design
* 🔐 Authentication
* 🎨 Modern UI using Mary UI

---

# 🛠️ Useful Commands

## Clear Cache

```bash
php artisan optimize:clear
```

## Run Queue

```bash
php artisan queue:work
```

## Run Test

```bash
php artisan test
```

---

# 📦 Deployment

Build production assets:

```bash
npm run build
```

Optimize Laravel:

```bash
php artisan optimize
```

---

# 🤝 Contributing

Pull request, issue, dan contribution sangat terbuka untuk pengembangan project ini.

---

# 📄 License

Project ini menggunakan lisensi MIT.

---

# 👨‍💻 Developer

Made with ❤️ by **Adine Pamungkas**

🔗 Repository:
[Activity Repository](https://github.com/Adine07/Activity?utm_source=chatgpt.com)
