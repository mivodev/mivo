@echo off
echo Starting Mikhmon v3 Remake Development Server...
echo.
echo Local: http://localhost:8000
echo Network URLs (Check below):
ipconfig | findstr "IPv4"
echo.
echo Server will start in 5 seconds...
echo Press Ctrl+C to stop.
timeout /t 5 >nul
php -S 0.0.0.0:8000 -t public public/index.php
