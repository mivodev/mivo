#!/bin/bash
# aaPanel Webhook Deployment Script for Mivo
# Path: /www/wwwroot/<your_project_path>

PROJECT_PATH="/www/wwwroot/<your_project_path>"

echo "---------------------------------------"
echo "Starting Deployment: $(date)"
echo "---------------------------------------"

if [ ! -d "$PROJECT_PATH" ]; then
    echo "Error: Project directory $PROJECT_PATH not found."
    exit 1
fi

cd $PROJECT_PATH || exit

# 1. Pull latest changes
echo "Step 1: Pulling latest changes from Git..."
git pull origin main # Adjust branch name if necessary

# 2. Install PHP dependencies
if [ -f "composer.json" ]; then
    echo "Step 2: Installing PHP dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

# 3. Build Assets
if [ -f "package.json" ]; then
    echo "Step 3: Building assets..."
    # If node_modules doesn't exist, install first
    if [ ! -d "node_modules" ]; then
        npm install
    fi
    npm run build
fi

# 4. Set Permissions
echo "Step 4: Setting permissions..."
chown -R www:www .
chmod +x mivo
chmod -R 755 public
# If there's a storage directory (MVC style usually has one)
if [ -d "storage" ]; then
    chmod -R 775 storage
fi

echo "---------------------------------------"
echo "Deployment Finished Successfully!"
echo "---------------------------------------"
