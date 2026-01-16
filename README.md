<p align="center">
  <img src="public/assets/img/logo.png" alt="MIVO Logo" width="200" />
</p>

# MIVO (Mikrotik Voucher)

> **Modern. Lightweight. Efficient.**

MIVO is a complete rewrite of the legendary **Mikhmon v3**, re-engineered with a modern MVC architecture to run efficiently on low-end devices like STB (Set Top Boxes) and Android, while providing a premium user experience on desktop.

![Status](https://img.shields.io/badge/Status-Beta-orange) ![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4) ![License](https://img.shields.io/badge/License-MIT-green)

## 🚀 Key Features

*   **⚡ Lightweight Core**: Built on a custom minimal MVC framework (~50KB core) optimized for speed.
*   **🎨 Modern UI/UX**: Fresh Glassmorphism design system using TailwindCSS and Alpine.js.
*   **📱 Responsive**: Fully optimized mobile experience with touch-friendly navigation.
*   **🔒 Secure**: Environment-based configuration (`.env`), encrypted credentials, and secure session management.
*   **🔌 API Ready**: Built-in REST API support with CORS management for external integrations.
*   **🛠️ CLI Tool**: Includes `mivo` CLI helper for easy management and installation.

## 🛠️ Installation

### Requirements
*   PHP 8.0 or higher
*   SQLite3 Extension
*   OpenSSL Extension

### Quick Start

1.  **Install via Composer**
    ```bash
    composer create-project dyzulk/mivo
    cd mivo
    ```

    > **Alternative (Docker):**
    > ```bash
    > docker pull dyzulk/mivo
    > ```
    > *See [INSTALLATION.md](docs/INSTALLATION.md) for more tags.*

2.  **Setup Environment**
    ```bash
    cp .env.example .env
    ```

3.  **Run Development Server**
    ```bash
    php mivo serve
    ```
    Access the app at `http://localhost:8000`.

4.  **Install Application**
    *   **Option A: CLI (Recommended)**
        ```bash
        php mivo install
        ```
    *   **Option B: Web Installer**
        Open `http://localhost:8000/install` in your browser and follow the instructions.

## 📂 Structure

*   `app/` - Core application logic (Controllers, Models, Views).
*   `public/` - Web root and assets.
*   `routes/` - Route definitions (`web.php`, `api.php`).
*   `mivo` - CLI executable entry point.


## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## ☕ Support the Project

If you find MIVO useful, please consider supporting its development. Your contribution helps keep the project alive!

[![SociaBuzz Tribe](https://img.shields.io/badge/SociaBuzz-Tribe-green?style=for-the-badge&logo=sociabuzz&logoColor=white)](https://sociabuzz.com/dyzulkdev/tribe)


## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---
*Created with ❤️ by DyzulkDev*
