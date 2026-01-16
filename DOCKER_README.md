<p align="center">
  <img src="https://raw.githubusercontent.com/dyzulk/mivo/main/public/assets/img/logo.png" alt="MIVO Logo" width="200" />
</p>

# MIVO (Mikrotik Voucher) Docker Image

> **Modern. Lightweight. Efficient.**

MIVO is a next-generation **Mikrotik Voucher Management System** with a modern MVC architecture, designed to run efficiently on low-end devices like STB (Set Top Boxes) and Android, while providing a premium user experience on desktop.

This Docker image is built on **Alpine Linux** and **Nginx**, optimized for high performance and low resource usage.

![Status](https://img.shields.io/badge/Status-Beta-orange) ![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4) ![License](https://img.shields.io/badge/License-MIT-green)

## Quick Start

Run MIVO in a single command:

```bash
docker run -d \
  --name mivo \
  -p 8080:80 \
  -e APP_KEY=base64:YOUR_GENERATED_KEY \
  -e APP_ENV=production \
  -v mivo_data:/var/www/html/app/Database \
  -v mivo_config:/var/www/html/.env \
  dyzulk/mivo:latest
```

Open your browser and navigate to `http://localhost:8080`.

**Initial Setup:**
If this is your first run, you will be redirected to the **Web Installer**. Follow the on-screen instructions to create the database and admin account.

## Docker Compose

For a more permanent setup, use `docker-compose.yml`:

```yaml
services:
  mivo:
    image: dyzulk/mivo:latest
    container_name: mivo
    restart: unless-stopped
    ports:
      - "8080:80"
    environment:
      - APP_ENV=production
      - TZ=Asia/Jakarta
    volumes:
      - ./mivo-data:/var/www/html/app/Database
```

## Tags

- `latest`: Stable release (recommended).
- `edge`: Bleeding edge build from the `main` branch.
- `v1.x.x`: Specific released versions.

## Deploy on aaPanel (Advanced / Native Style)

This method follows the standard aaPanel "Quick Install" pattern, using full control over paths and resources via a `.env` file.

1.  **Prepare Files**:
    *   Copy the content of [docker/aapanel-template.yml](docker/aapanel-template.yml).
    *   Copy the content of [docker/aapanel-env.example](docker/aapanel-env.example).

2.  **Add Project in aaPanel**:
    *   Go to **Docker** -> **Project** -> **Add Project**.
    *   **Name**: `mivo`
    *   **Compose Template**: Paste the content of `aapanel-template.yml`.

3.  **Define Configuration (.env)**:
    *   In the sidebar or tab for **.env** (which appears after you paste the template in some versions, or you create manually):
    *   Paste the content of `aapanel-env.example`.
    *   **Crucial Step**: Edit `APP_PATH` to match your project path (usually `/www/dk_project/mivo`).
    *   Adjust `APP_PORT` if needed.

4.  **Confirm**: Click "Add" or "Confirm" to deploy.

5.  **Setup Reverse Proxy**:
    *   Go to **Website** -> **Add Site** -> **Reverse Proxy**.
    *   Target: `http://127.0.0.1:8080` (Usage of variable `${APP_PORT}` matches this).
    *   Go to **Website** -> **Add Site**.
    *   Enter your domain name (e.g., `mivo.yourdomain.com`).
    *   Select **Reverse Proxy** as the PHP version (or set it up manually afterwards).
    *   After the site is created, click on it -> **Reverse Proxy** -> **Add Reverse Proxy**.
    *   **Target URL**: `http://127.0.0.1:8080` (or the port you configured).
    *   Save and enable SSL.

## Environment Variables

| Variable | Description | Default |
| :--- | :--- | :--- |
| `APP_ENV` | Application environment (`production` or `local`). | `production` |
| `APP_DEBUG` | Enable debug mode (`true` or `false`). | `false` |
| `APP_KEY` | 32-character random string (base64). Auto-generated on first install if not provided. | |
| `TZ` | Timezone for the container. | `UTC` |

## Volumes

Persist your data by mounting these paths:

- `/var/www/html/app/Database`: Stores the SQLite database and session files. **(Critical)**
- `/var/www/html/public/assets/img/logos`: Stores uploaded custom logos.

## Support the Project

If you find MIVO useful, please consider supporting its development. Your contribution helps keep the project alive!

[![SociaBuzz Tribe](https://img.shields.io/badge/SociaBuzz-Tribe-green?style=for-the-badge&logo=sociabuzz&logoColor=white)](https://sociabuzz.com/dyzulkdev/tribe)

---
*Created by DyzulkDev*
