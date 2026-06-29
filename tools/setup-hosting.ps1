# setup-hosting.ps1
# Script para guiar al usuario en la creación de la cuenta de hosting
# y configurar los secretos en GitHub automáticamente.
#
# Uso (PowerShell):
#   .\tools\setup-hosting.ps1

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  🚲 Bicicletería Fagua — Setup de hosting paso a paso  ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# ── Verificar gh CLI ───────────────────────────────
if (-not (Get-Command gh -ErrorAction SilentlyContinue)) {
    Write-Host "❌ GitHub CLI (gh) no está instalado." -ForegroundColor Red
    exit 1
}

# ── Verificar repo ─────────────────────────────────
$REPO = "JORMS-FA/fagua-bike-theme"
try {
    gh repo view $REPO 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "No encontrado" }
} catch {
    Write-Host "❌ No se encontró el repo $REPO" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Repositorio verificado: $REPO" -ForegroundColor Green
Write-Host ""

# ── Menú principal ─────────────────────────────────
Write-Host "Selecciona qué quieres hacer:" -ForegroundColor Yellow
Write-Host ""
Write-Host "  1. 📋 Ver instrucciones paso a paso (manual)"
Write-Host "  2. 🔐 Configurar secretos FTP en GitHub"
Write-Host "  3. 🌐 Verificar estado del último deploy"
Write-Host "  4. 🧪 Ejecutar Playwright contra URL de producción"
Write-Host "  5. ❌ Salir"
Write-Host ""

$choice = Read-Host "Opción (1-5)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host "📋 INSTRUCCIONES PARA CREAR CUENTA EN INFINITYFREE" -ForegroundColor Cyan
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "1. Abre en tu navegador: https://www.infinityfree.com/" -ForegroundColor White
        Write-Host "2. Click en 'Register Now' (esquina superior derecha)" -ForegroundColor White
        Write-Host "3. Llena el formulario:" -ForegroundColor White
        Write-Host "   - Email: el que uses normalmente (lo necesitas para verificar)" -ForegroundColor Gray
        Write-Host "   - Password: uno seguro" -ForegroundColor Gray
        Write-Host "4. Confirma tu email" -ForegroundColor White
        Write-Host "5. Inicia sesión en el panel: https://app.infinityfree.com/" -ForegroundColor White
        Write-Host ""
        Write-Host "6. Click en 'Create Account' para crear el hosting:" -ForegroundColor White
        Write-Host "   - Subdomain: elige uno memorable (ej: faguabike)" -ForegroundColor Gray
        Write-Host "   - Esto te dará: faguabike.infinityfreeapp.com" -ForegroundColor Gray
        Write-Host "   - Password: uno seguro" -ForegroundColor Gray
        Write-Host ""
        Write-Host "7. Espera a que se active (~1 minuto)" -ForegroundColor White
        Write-Host ""
        Write-Host "8. En el panel de control, ve a 'FTP Accounts' y anota:" -ForegroundColor White
        Write-Host "   ✏️  Hostname: ftp.tu-subdominio.infinityfree.com" -ForegroundColor Yellow
        Write-Host "   ✏️  Username: epiz_XXXXXXX" -ForegroundColor Yellow
        Write-Host "   ✏️  Password: el que pusiste" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "9. Ve a 'MySQL Databases' y crea una BD nueva:" -ForegroundColor White
        Write-Host "   ✏️  Database Name: fagua_bike" -ForegroundColor Yellow
        Write-Host "   ✏️  Username: fagua_user" -ForegroundColor Yellow
        Write-Host "   ✏️  Password: uno seguro" -ForegroundColor Yellow
        Write-Host "   ✏️  Anota el MySQL Hostname (ej: sql123.infinityfree.com)" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "10. Vuelve aquí y elige la opción 2 para configurar los secretos" -ForegroundColor White
        Write-Host ""
        Read-Host "Presiona Enter para continuar"
    }
    "2" {
        Write-Host ""
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host "🔐 CONFIGURAR SECRETOS EN GITHUB" -ForegroundColor Cyan
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host ""

        $ftpServer = Read-Host "FTP_SERVER (ej: ftpupload.net)"
        $ftpUser = Read-Host "FTP_USERNAME (ej: epiz_12345678)"
        $ftpPassword = Read-Host "FTP_PASSWORD" -AsSecureString
        $ftpPasswordPlain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
            [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($ftpPassword)
        )
        $ftpDir = Read-Host "FTP_SERVER_DIR (ej: htdocs/wp-content/themes/bicicleteria-fagua-theme/)"
        $prodUrl = Read-Host "PRODUCTION_URL (ej: https://faguabike.infinityfreeapp.com)"

        Write-Host ""
        Write-Host "Configurando secretos..." -ForegroundColor Yellow

        $secrets = @{
            "FTP_SERVER"     = $ftpServer
            "FTP_USERNAME"   = $ftpUser
            "FTP_PASSWORD"   = $ftpPasswordPlain
            "FTP_SERVER_DIR" = $ftpDir
            "PRODUCTION_URL" = $prodUrl
        }

        foreach ($key in $secrets.Keys) {
            Write-Host "  → $key" -NoNewline
            $value = $secrets[$key]
            $value | gh secret set $key --repo $REPO 2>&1 | Out-Null
            if ($LASTEXITCODE -eq 0) {
                Write-Host " ✅" -ForegroundColor Green
            } else {
                Write-Host " ❌" -ForegroundColor Red
            }
        }

        Write-Host ""
        Write-Host "✅ Secretos configurados. Puedes disparar el deploy con:" -ForegroundColor Green
        Write-Host "  git commit --allow-empty -m 'ci: trigger deploy'"
        Write-Host "  git push origin main"
        Write-Host ""
    }
    "3" {
        Write-Host ""
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host "🌐 ÚLTIMOS DEPLOYS" -ForegroundColor Cyan
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host ""
        gh run list --repo $REPO --limit 10
    }
    "4" {
        Write-Host ""
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host "🧪 PLAYWRIGHT E2E" -ForegroundColor Cyan
        Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
        Write-Host ""

        $url = Read-Host "URL de producción (ej: https://faguabike.infinityfreeapp.com)"

        if (-not (Test-Path "node_modules")) {
            Write-Host "Instalando dependencias..." -ForegroundColor Yellow
            npm install --no-audit --no-fund
        }

        Write-Host "Ejecutando Playwright contra $url ..." -ForegroundColor Yellow
        $env:BASE_URL = $url
        npx playwright test
    }
    "5" {
        exit 0
    }
    default {
        Write-Host "Opción inválida" -ForegroundColor Red
    }
}
