@echo off
title StayScape - Airbnb Clone Setup
color 0A

echo ============================================
echo    StayScape - Airbnb Clone Setup Script
echo ============================================
echo.

:: Check for XAMPP MySQL
set MYSQL_PATH=
if exist "C:\xampp\mysql\bin\mysql.exe" (
    set MYSQL_PATH=C:\xampp\mysql\bin
    echo [OK] Found XAMPP MySQL
) else if exist "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" (
    set MYSQL_PATH=C:\wamp64\bin\mysql\mysql8.0.31\bin
    echo [OK] Found WAMP MySQL
) else if exist "C:\wamp\bin\mysql\mysql8.0.31\bin\mysql.exe" (
    set MYSQL_PATH=C:\wamp\bin\mysql\mysql8.0.31\bin
    echo [OK] Found WAMP MySQL
) else (
    where mysql >nul 2>nul
    if %errorlevel% equ 0 (
        set MYSQL_PATH=.
        echo [OK] Found MySQL in PATH
    ) else (
        echo [ERROR] MySQL not found!
        echo.
        echo Please make sure XAMPP or WAMP is installed and MySQL is running.
        echo.
        echo Common XAMPP path: C:\xampp\mysql\bin
        echo.
        set /p MYSQL_PATH="Enter your MySQL bin folder path (or press Enter to skip DB setup): "
        if "!MYSQL_PATH!"=="" goto skip_db
    )
)

:: Check for PHP
set PHP_PATH=
if exist "C:\xampp\php\php.exe" (
    set PHP_PATH=C:\xampp\php
    echo [OK] Found XAMPP PHP
) else (
    where php >nul 2>nul
    if %errorlevel% equ 0 (
        set PHP_PATH=.
        echo [OK] Found PHP in PATH
    ) else (
        echo [ERROR] PHP not found!
        echo Please install XAMPP from https://www.apachefriends.org
        pause
        exit /b 1
    )
)

echo.
echo ============================================
echo    Step 1: Setting up Database
echo ============================================
echo.

echo Importing database...
if "%MYSQL_PATH%"=="." (
    mysql -u root < database.sql 2>nul
) else (
    "%MYSQL_PATH%\mysql.exe" -u root < database.sql 2>nul
)

if %errorlevel% equ 0 (
    echo [OK] Database 'stayscape' created and sample data imported!
) else (
    echo [WARNING] Could not import database automatically.
    echo.
    echo Try manually:
    echo   1. Open XAMPP Control Panel
    echo   2. Start Apache and MySQL
    echo   3. Open phpMyAdmin (http://localhost/phpmyadmin)
    echo   4. Create database named 'stayscape'
    echo   5. Import the database.sql file
    echo.
)

:skip_db

echo.
echo ============================================
echo    Step 2: Starting PHP Server
echo ============================================
echo.
echo Starting PHP development server on http://localhost:8080
echo.
echo -----------------------------------------------
echo   OPEN IN BROWSER: http://localhost:8080
echo -----------------------------------------------
echo.
echo   Demo Login:
echo     Email:    demo@stayscape.com
echo     Password: password123
echo.
echo   Press Ctrl+C to stop the server.
echo -----------------------------------------------
echo.

if "%PHP_PATH%"=="." (
    php -S localhost:8080 -t "%~dp0"
) else (
    "%PHP_PATH%\php.exe" -S localhost:8080 -t "%~dp0"
)

pause
