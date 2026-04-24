@echo off
setlocal enabledelayedexpansion
title StayScape - Airbnb Clone Setup
color 0A

echo.
echo  ============================================
echo     StayScape - One Click Setup
echo  ============================================
echo.

:: ---- Find XAMPP ----
set XAMPP=
if exist "C:\xampp" (
    set "XAMPP=C:\xampp"
) else if exist "D:\xampp" (
    set "XAMPP=D:\xampp"
) else if exist "E:\xampp" (
    set "XAMPP=E:\xampp"
) else (
    echo [ERROR] XAMPP not found on C:\, D:\, or E:\
    echo.
    echo Download XAMPP from: https://www.apachefriends.org
    echo Install it and re-run this script.
    echo.
    pause
    exit /b 1
)

echo [OK] XAMPP found at !XAMPP!

set "PHP_EXE=!XAMPP!\php\php.exe"
set "MYSQL_EXE=!XAMPP!\mysql\bin\mysql.exe"
set "MYSQLD_EXE=!XAMPP!\mysql\bin\mysqld.exe"
set "MYSQL_DATA=!XAMPP!\mysql\data"

if not exist "!PHP_EXE!" (
    echo [ERROR] PHP not found at !PHP_EXE!
    pause
    exit /b 1
)
echo [OK] PHP found

if not exist "!MYSQL_EXE!" (
    echo [ERROR] MySQL not found at !MYSQL_EXE!
    pause
    exit /b 1
)
echo [OK] MySQL found

:: ---- Start MySQL if not running ----
echo.
echo Checking if MySQL is running...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if !errorlevel! neq 0 (
    echo Starting MySQL server...
    start /B "" "!MYSQLD_EXE!" --defaults-file="!XAMPP!\mysql\bin\my.ini" --standalone
    echo Waiting for MySQL to start...
    timeout /t 5 /nobreak >nul

    :: Verify it started
    tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
    if !errorlevel! neq 0 (
        echo [WARNING] MySQL may not have started. Trying anyway...
    ) else (
        echo [OK] MySQL started successfully
    )
) else (
    echo [OK] MySQL is already running
)

:: ---- Import Database ----
echo.
echo Importing database...
"!MYSQL_EXE!" -u root < "%~dp0database.sql" 2>nul
if !errorlevel! equ 0 (
    echo [OK] Database 'stayscape' created with sample data!
) else (
    echo [WARNING] Database import had issues.
    echo   It may already exist - continuing anyway...
)

:: ---- Start PHP Server ----
echo.
echo  ============================================
echo.
echo   Server starting at: http://localhost:8080
echo.
echo   Demo Login:
echo     Email:    demo@stayscape.com
echo     Password: password123
echo.
echo   Press Ctrl+C to stop the server.
echo.
echo  ============================================
echo.

start http://localhost:8080
"!PHP_EXE!" -S localhost:8080 -t "%~dp0"
pause
