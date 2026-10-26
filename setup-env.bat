@echo off
title OneFinance - Setup Otomatis ENV
color 0A

echo ===================================================
echo     OneFinance - Konfigurasi Otomatis .ENV
echo ===================================================
echo.

:: 1. Salin .env.example jika .env belum ada
if not exist ".env" (
    if exist ".env.example" (
        copy .env.example .env >nul
        echo [OK] File .env berhasil disalin otomatis dari .env.example
    ) else (
        echo [ERROR] File .env.example tidak ditemukan!
        goto end
    )
) else (
    echo [INFO] File .env sudah ada.
)

:: 2. Cek folder vendor
if not exist "vendor\autoload.php" (
    echo [INFO] Folder vendor belum ada. Anda dapat menjalankan: composer install
) else (
    :: 3. Generate key jika kosong
    findstr /R "^APP_KEY=..*" .env >nul
    if errorlevel 1 (
        echo [INFO] Membuat Application Key (APP_KEY)...
        call php artisan key:generate
    ) else (
        echo [INFO] APP_KEY sudah terisi di .env.
    )

    :: 4. Jalankan perintah env:auto
    call php artisan env:auto
)

echo.
echo ===================================================
echo   Setup .env Otomatis Selesai!
echo ===================================================
echo.

:end
pause
