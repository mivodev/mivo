# Deploy on aaPanel (Docker)

aaPanel makes it easiest to manage Docker projects using its native **Docker Manager** module. We recommend following the standard "Quick Install" pattern which uses environment variables for better maintainability.

::: tip PREREQUISITES
Ensure you have the **Docker** module installed from the aaPanel App Store.
:::

## 1. Prepare Configuration

We use a "Native aaPanel Style" setup where configuration is separated from the template.

**Environment File (`.env`)**
Create a new file named `.env` and configure your preferences:

```ini
VERSION=latest
CONTAINER_NAME=mivo
HOST_IP=0.0.0.0
APP_PORT=8080
APP_PATH=/www/dk_project/mivo
APP_ENV=production
APP_DEBUG=false
TZ=Asia/Jakarta
CPUS=1.0
MEMORY_LIMIT=512M
```

**Attribute Explanation:**
- `APP_PATH`: **Crucial**. This must match your project directory in aaPanel (default is `/www/dk_project/<project_name>`).
- `APP_PORT`: The port you want to expose (default `8080`).
- `TZ`: Your timezone (e.g., `Asia/Jakarta`).

## 2. Create Project in aaPanel

1. Login to your aaPanel dashboard.
2. Navigate to **Docker** > **Project**.
3. Click **Add Project**.
4. Fill in the details:
   - **Name**: `mivo` (or your preferred name)
   - **Path**: This will auto-fill (usually `/www/dk_project/mivo`). **Update `APP_PATH` in your .env to match this!**
   - **Compose Template**: Paste the following YAML content:

```yaml
version: '3.8'

services:
  mivo:
    image: dyzulk/mivo:${VERSION}
    container_name: ${CONTAINER_NAME}
    restart: unless-stopped
    ports:
      - "${HOST_IP}:${APP_PORT}:80"
    volumes:
      # Database & Sessions
      - ${APP_PATH}/mivo_data:/var/www/html/app/Database
      
      # Custom Logos
      - ${APP_PATH}/mivo_logos:/var/www/html/public/assets/img/logos
      
      # Environment file (Optional - mapped from host)
      # - ${APP_PATH}/.env:/var/www/html/.env
      
    environment:
      - APP_ENV=${APP_ENV}
      - APP_DEBUG=${APP_DEBUG}
      - TZ=${TZ}
      
    deploy:
      resources:
        limits:
          cpus: '${CPUS}'
          memory: ${MEMORY_LIMIT}

    networks:
      - mivo_net

networks:
  mivo_net:
    driver: bridge
```

5. **Wait!** Before clicking "Confirm" or "Add":
   - Look for the **.env** config section (usually a tab or sidebar in the Add Project modal).
   - Paste your `.env` content there.
6. Click **Confirm** to deploy.

## 3. Verify Deployment

aaPanel will pull the image and start the container. Once running:

- **Database Location**: Your data is safe at `/www/dk_project/mivo/mivo_data`.
- **Logos Location**: Upload custom logos to `/www/dk_project/mivo/mivo_logos`.

## 4. Setup Domain (Reverse Proxy)

To access MIVO via a domain (e.g., `mivo.yourdomain.com`):

1. Go to **Website** > **Add Site**.
2. Enter your domain name.
3. For **PHP Version**, select **Reverse Proxy** (or create as Static and set up proxy later).
4. After creation, open the site settings > **Reverse Proxy** > **Add Reverse Proxy**.
5. **Target URL**: `http://127.0.0.1:8080` (Replace `8080` with your `APP_PORT`).
6. Save and secure with SSL.
