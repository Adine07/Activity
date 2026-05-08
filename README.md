# 🚀 Activity — Employee Activity Monitoring System

> An employee activity monitoring system built with Laravel to help management and business owners track employee work, progress, and daily activities in real-time.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=for-the-badge&logo=laravel" />
  <img src="https://img.shields.io/badge/Livewire-3-purple?style=for-the-badge&logo=livewire" />
  <img src="https://img.shields.io/badge/TailwindCSS-4-38BDF8?style=for-the-badge&logo=tailwindcss" />
  <img src="https://img.shields.io/badge/MaryUI-Modern-blueviolet?style=for-the-badge" />
  <img src="https://img.shields.io/badge/PHP-8.3-blue?style=for-the-badge&logo=php" />
</p>

---

# 📌 About The Project

**Activity** is a modern employee activity monitoring application built using:

* ⚡ Laravel 13
* 🔥 Livewire 3
* 🎨 Mary UI
* 💨 Tailwind CSS

This application allows management and business owners to:

* ✅ Monitor employee daily activities
* ✅ View activities in real-time
* ✅ Use a modern and responsive dashboard
* ✅ Experience a lightweight and fast system

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

Make sure your environment meets the following requirements:

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

Navigate into the project directory:

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

Copy the `.env` file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

---

## 5️⃣ Configure Database

Open the `.env` file and update the database configuration:

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

Access the project at:

```txt
http://127.0.0.1:8000
```

---

# 🔐 Default Login

```txt
Email    : admin@mail.com
Password : password
```

> Adjust the credentials if your Seeder uses different default accounts.

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

* 📊 Monitoring Dashboard
* 👨‍💼 Employee Activity Tracking
* 📝 Daily Activity Reports
* ⚡ Realtime UI with Livewire
* 📱 Responsive Design
* 🔐 Authentication System
* 🎨 Modern UI using Mary UI

---

# 🛠️ Useful Commands

## Clear Cache

```bash
php artisan optimize:clear
```

## Run Queue Worker

```bash
php artisan queue:work
```

## Run Tests

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

Pull requests, issues, and contributions are welcome for further project development.

---

# 📄 License

This project is licensed under the MIT License.

---

# 👨‍💻 Developer

Made with ❤️ by **Adine Pamungkas**

🔗 Repository:
[Activity Repository](https://github.com/Adine07/Activity?utm_source=chatgpt.com)
