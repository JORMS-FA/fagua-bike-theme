@echo off
:: Auto-elevar a administrador
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Solicitando permisos de administrador...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

cd /d C:\Users\fagua\dev\fagua-bike-theme
echo.
echo === Instalando tarea programada: BicicleteriaFagua-CloudflareTunnel ===
echo.

powershell -ExecutionPolicy Bypass -File "tools\cloudflare-tunnel-task.ps1" -Install

echo.
pause
