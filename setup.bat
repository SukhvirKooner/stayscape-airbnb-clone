@echo off
setlocal enabledelayedexpansion
title StayScape - Airbnb Clone Setup
color 0A

echo.
echo  ============================================
echo     StayScape - Airbnb Clone Setup
echo  ============================================
echo.
echo  Make sure XAMPP is installed and MySQL is
echo  running from the XAMPP Control Panel.
echo  ============================================
echo.

:: ---- Find PHP ----
set PHP_EXE=
if exist "C:\xampp\php\php.exe" (
    set "PHP_EXE=C:\xampp\php\php.exe"
    echo [OK] Found PHP at C:\xampp\php
) else (
    for /f "delims=" %%i in ('where php 2^>nul') do set "PHP_EXE=%%i"
    if defined PHP_EXE (
        echo [OK] Found PHP in PATH
    ) else (
        echo [ERROR] PHP not found!
        echo.
        echo Install XAMPP from: https://www.apachefriends.org
        echo Then re-run this script.
        pause
        exit /b 1
    )
)

:: ---- Find MySQL ----
set MYSQL_EXE=
if exist "C:\xampp\mysql\bin\mysql.exe" (
    set "MYSQL_EXE=C:\xampp\mysql\bin\mysql.exe"
    echo [OK] Found MySQL at C:\xampp\mysql\bin
) else (
    for /f "delims=" %%i in ('where mysql 2^>nul') do set "MYSQL_EXE=%%i"
    if defined MYSQL_EXE (
        echo [OK] Found MySQL in PATH
    ) else (
        echo [WARNING] MySQL not found automatically.
        echo.
        echo You will need to import the database manually:
        echo   1. Open http://localhost/phpmyadmin
        echo   2. Create a database named 'stayscape'
        echo   3. Go to Import tab and upload database.sql
        echo.
        goto start_server
    )
)

:: ---- Import Database ----
echo.
echo Importing database...
"!MYSQL_EXE!" -u root < "%~dp0database.sql" 2>nul
if !errorlevel! equ 0 (
    echo [OK] Database 'stayscape' created with sample data!
) else (
    echo [WARNING] Could not import database.
    echo.
    echo Make sure MySQL is running in XAMPP Control Panel, then either:
    echo   - Re-run this script, OR
    echo   - Import manually via http://localhost/phpmyadmin
)

:start_server
echo.
echo  ============================================
echo.
echo   Starting server at: http://localhost:8080
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
