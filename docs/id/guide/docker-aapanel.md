# Deploy di aaPanel (Docker)

aaPanel adalah salah satu panel hosting paling populer yang memiliki modul **Docker Manager** yang sangat memudahkan manajemen container. Kami merekomendasikan penggunaan gaya "Native" aaPanel agar manajemen resource dan env lebih rapi.

::: tip PRASYARAT
Pastikan Anda sudah menginstall modul **Docker** dari App Store di dalam aaPanel Anda.
:::

## 1. Siapkan Konfigurasi

Metode ini memisahkan konfigurasi (di file `.env`) dari template logic, sehingga Anda bisa dengan mudah mengubah port atau resource limit tanpa mengedit file YAML yang rumit.

**File Environment (`.env`)**
Buat file baru bernama `.env` (atau simpan teks ini untuk nanti):

```ini
VERSION=latest
CONTAINER_NAME=mivo
HOST_IP=0.0.0.0
APP_PORT=8085
APP_PATH=/www/dk_project/mivo
APP_ENV=production
APP_DEBUG=false
TZ=Asia/Jakarta
CPUS=1.0
MEMORY_LIMIT=512M
```

**Penjelasan Atribut:**
- `APP_PATH`: **Penting**. Ini harus sama persis dengan lokasi project di aaPanel Anda (defaultnya `/www/dk_project/<nama_project>`).
- `APP_PORT`: Port host yang ingin dibuka (default `8080`).
- `CPUS` & `MEMORY_LIMIT`: Batasan resource agar container tidak membebani server/VPS Anda.

## 2. Buat Project di aaPanel

1. Login ke dashboard aaPanel.
2. Masuk ke menu **Docker** > **Project** (atau **Compose** di versi lama).
3. Klik tombol **Add Project**.
4. Isi form sebagai berikut:
   - **Name**: `mivo` (atau nama lain yang Anda suka)
   - **Path**: Perhatikan path yang muncul otomatis (biasanya `/www/dk_project/mivo`). **Pastikan `APP_PATH` di .env Anda sesuai dengan path ini!**
   - **Compose Template**: Copy-paste kode YAML berikut:

```yaml
version: '3.8'

services:
  mivo:
    image: dyzulk/mivo:${VERSION:-latest}
    container_name: ${CONTAINER_NAME:-mivo}
    restart: unless-stopped
    ports:
      - "${HOST_IP:-0.0.0.0}:${APP_PORT:-8085}:80"
    volumes:
      # Database & Sessions
      - ${APP_PATH:-.}/mivo_data:/var/www/html/app/Database
      
      # Custom Logos
      - ${APP_PATH:-.}/mivo_logos:/var/www/html/public/assets/img/logos
      
      # Environment file (Optional - mapped from host)
      # - ${APP_PATH:-.}/.env:/var/www/html/.env
      
    environment:
      - APP_ENV=${APP_ENV:-production}
      - APP_DEBUG=${APP_DEBUG:-false}
      - TZ=${TZ:-Asia/Jakarta}
      
    deploy:
      resources:
        limits:
          cpus: '${CPUS:-1.0}'
          memory: ${MEMORY_LIMIT:-512M}

    networks:
      - mivo_net

networks:
  mivo_net:
    driver: bridge
```

5. **Tunggu!** Sebelum klik "Confirm":
   - Cari bagian konfigurasi **.env** (biasanya berupa tab atau input area di samping/bawah editor YAML).
   - Paste konten `.env` yang sudah Anda siapkan di langkah 1 ke sana.
6. Klik **Confirm** untuk memulai deployment.

## 3. Verifikasi Deployment

aaPanel akan otomatis mendownload image dan menjalankan container.

- **Lokasi Data**: Database Anda aman tersimpan di `/www/dk_project/mivo/mivo_data`. folder ini tidak akan hilang walau container dihapus.
- **Lokasi Logo**: Upload logo kustom Anda ke `/www/dk_project/mivo/mivo_logos`.

## 4. Setup Domain (Reverse Proxy)

Agar MIVO bisa diakses menggunakan domain (contoh: `mivo.domainanda.com`):

1. Ke menu **Website** > **Add Site**.
2. Masukkan nama domain Anda.
3. Pada **PHP Version**, pilih **Static** (atau langsung Reverse Proxy jika ada opsinya).
4. Setelah site dibuat, buka settingannya > **Reverse Proxy** > **Add Reverse Proxy**.
5. **Target URL**: `http://127.0.0.1:8085` (Ganti `8085` sesuai dengan `APP_PORT` Anda).
6. Simpan dan aktifkan SSL agar lebih aman.
