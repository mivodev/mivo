---
title: Docker Guide
---

# Docker Guide

This Docker image is built on **Alpine Linux** and **Nginx**, optimized for high performance and low resource usage.

## 🚀 Quick Start

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

## 🛠️ Docker Compose

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

## 📦 Tags

- `latest`: Stable release (recommended).
- `edge`: Bleeding edge build from the `main` branch.
- `v1.x.x`: Specific released versions.

## 🔧 Environment Variables

| Variable | Description | Default |
| :--- | :--- | :--- |
| `APP_ENV` | Application environment (`production` or `local`). | `production` |
| `APP_DEBUG` | Enable debug mode (`true` or `false`). | `false` |
| `APP_KEY` | 32-character random string (base64). Auto-generated on first install if not provided. | |
| `TZ` | Timezone for the container. | `UTC` |

## 📂 Volumes

Persist your data by mounting these paths:

- `/var/www/html/app/Database`: Stores the SQLite database and session files. **(Critical)**
- `/var/www/html/public/assets/img/logos`: Stores uploaded custom logos.

## 🤝 Support the Project

If you find MIVO useful, please consider supporting its development. Your contribution helps keep the project alive!

[![SociaBuzz Tribe](https://img.shields.io/badge/SociaBuzz-Tribe-green?style=for-the-badge&logo=sociabuzz&logoColor=white)](https://sociabuzz.com/dyzulkdev/tribe)

---
*Created with ❤️ by DyzulkDev*
