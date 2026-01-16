$ErrorActionPreference = "Stop"

# Configuration
$RemotePath = "/www/wwwroot/app.mivo.dyzulk.com"

Write-Host "Starting Deployment to app.mivo.dyzulk.com..." -ForegroundColor Green

# 1. Build Assets
Write-Host "Building assets..." -ForegroundColor Cyan
cmd /c "npm run build"
if ($LASTEXITCODE -ne 0) {
    Write-Error "Build failed!"
}

# 2. Create Archive
Write-Host "Creating deployment package..." -ForegroundColor Cyan
# Excluding potential garbage
$excludeParams = @("--exclude", "node_modules", "--exclude", ".git", "--exclude", ".github", "--exclude", "temp_debug", "--exclude", "deploy.ps1", "--exclude", "*.tar.gz")
tar -czf deploy_package.tar.gz @excludeParams app public routes mivo src package.json
if ($LASTEXITCODE -ne 0) {
    Write-Error "Failed to create archive!"
}

# 3. Upload
Write-Host "Uploading to server ($RemotePath)..." -ForegroundColor Cyan
scp deploy_package.tar.gz "aapanel:$RemotePath/"
if ($LASTEXITCODE -ne 0) {
    Write-Error "SCP upload failed!"
}

# 4. Extract and Cleanup on Server
Write-Host "Extracting and configuring permissions..." -ForegroundColor Cyan
# Commands:
# 1. cd to remote path
# 2. Extract
# 3. Set ownership to www:www
# 4. Set mivo executable
# 5. Set public folder to 755 (Laravel recommendation)
# 6. Cleanup archive
$remoteCommands = "cd $RemotePath && tar -xzf deploy_package.tar.gz && chown -R www:www . && chmod +x mivo && chmod -R 755 public && rm deploy_package.tar.gz"

ssh aapanel $remoteCommands
if ($LASTEXITCODE -ne 0) {
    Write-Error "Remote deployment failed!"
}

# 5. Local Cleanup
Write-Host "Cleaning up local package..." -ForegroundColor Cyan
if (Test-Path deploy_package.tar.gz) {
    Remove-Item deploy_package.tar.gz
}

Write-Host "Deployment successfully completed!" -ForegroundColor Green
