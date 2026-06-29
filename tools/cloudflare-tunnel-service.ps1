# cloudflare-tunnel-service.ps1
# Instala y gestiona el servicio de Windows para el Cloudflare Tunnel
# de Bicicletería Fagua. Mantiene el sitio público 24/7 con auto-inicio.

param(
    [switch]$Install,
    [switch]$Uninstall,
    [switch]$Start,
    [switch]$Stop,
    [switch]$Restart,
    [switch]$Status
)

$ErrorActionPreference = "Stop"

$ServiceName = "BicicleteriaFagua-Tunnel"
$DisplayName = "Bicicletería Fagua - Cloudflare Tunnel"
$Description = "Mantiene un tunnel público hacia LocalWP en http://bicicleteriafagua.local:10016"
$RepoDir = "C:\Users\fagua\dev\fagua-bike-theme"
$ScriptPath = Join-Path $RepoDir "tools\cloudflare-tunnel.sh"
$BashPath = "C:\Program Files\Git\bin\bash.exe"
$LogDir = "$env:USERPROFILE\.cloudflared"
$StdoutLog = Join-Path $LogDir "service-stdout.log"
$StderrLog = Join-Path $LogDir "service-stderr.log"

# ── Banner ───────────────────────────────────────────
function Show-Banner {
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  🚲 Bicicletería Fagua — Cloudflare Tunnel Service     ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
}

# ── Verificar admin ──────────────────────────────────
function Test-Admin {
    $currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
    return $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# ── Verificar prerrequisitos ─────────────────────────
function Test-Prerequisites {
    $missing = @()

    if (-not (Test-Path $BashPath)) {
        $missing += "Git Bash (esperado en $BashPath)"
    }
    if (-not (Test-Path $ScriptPath)) {
        $missing += "Script cloudflare-tunnel.sh (esperado en $ScriptPath)"
    }
    $cloudflared = Get-Command cloudflared -ErrorAction SilentlyContinue
    if (-not $cloudflared) {
        $missing += "cloudflared (instalar con: winget install Cloudflare.cloudflared)"
    }

    if ($missing.Count -gt 0) {
        Write-Host "❌ Faltan requisitos:" -ForegroundColor Red
        $missing | ForEach-Object { Write-Host "   - $_" -ForegroundColor Yellow }
        return $false
    }

    if (-not (Test-Path $LogDir)) {
        New-Item -ItemType Directory -Path $LogDir -Force | Out-Null
    }

    return $true
}

# ── Instalar servicio con NSSM ───────────────────────
function Install-Service {
    Show-Banner

    if (-not (Test-Admin)) {
        Write-Host "❌ Este script requiere permisos de Administrador." -ForegroundColor Red
        Write-Host "   Click derecho → 'Ejecutar como administrador'" -ForegroundColor Yellow
        exit 1
    }

    if (-not (Test-Prerequisites)) {
        exit 1
    }

    # Verificar/instalar NSSM
    $nssm = Get-Command nssm -ErrorAction SilentlyContinue
    if (-not $nssm) {
        Write-Host "📦 NSSM no está instalado. Instalando..." -ForegroundColor Yellow
        try {
            winget install nssm --accept-package-agreements --accept-source-agreements 2>&1 | Out-Null
            Write-Host "✅ NSSM instalado" -ForegroundColor Green
        } catch {
            Write-Host "❌ No se pudo instalar NSSM automáticamente." -ForegroundColor Red
            Write-Host "   Instala manualmente: winget install nssm" -ForegroundColor Yellow
            exit 1
        }
        $env:Path = [System.Environment]::GetEnvironmentVariable("Path", "Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path", "User")
        $nssm = Get-Command nssm -ErrorAction SilentlyContinue
    }

    if (-not $nssm) {
        Write-Host "❌ NSSM no disponible después de instalación." -ForegroundColor Red
        exit 1
    }

    Write-Host "🔧 Instalando servicio '$ServiceName'..." -ForegroundColor Cyan

    # Detener si ya existe
    nssm stop $ServiceName 2>$null

    # Instalar servicio
    # NSSM ejecuta bash.exe con argumentos: bash.exe "C:\path\to\script.sh" "run-as-service"
    nssm install $ServiceName $BashPath "`"$ScriptPath`" run-as-service"
    nssm set $ServiceName AppDirectory $RepoDir
    nssm set $ServiceName DisplayName $DisplayName
    nssm set $ServiceName Description $Description
    nssm set $ServiceName Start SERVICE_AUTO_START
    nssm set $ServiceName AppStdout $StdoutLog
    nssm set $ServiceName AppStderr $StderrLog
    nssm set $ServiceName AppRotateFiles 1
    nssm set $ServiceName AppRotateBytes 1048576

    # Configurar reinicio automático si falla
    nssm set $ServiceName AppExit Default Restart
    nssm set $ServiceName AppRestartDelay 10000

    Write-Host "✅ Servicio instalado" -ForegroundColor Green
    Write-Host ""
    Write-Host "🚀 Iniciando servicio..." -ForegroundColor Cyan
    nssm start $ServiceName

    Start-Sleep -Seconds 8

    Write-Host ""
    Write-Host "══════════════════════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host "  Estado del servicio" -ForegroundColor Cyan
    Write-Host "══════════════════════════════════════════════════════════" -ForegroundColor Cyan

    $service = Get-Service $ServiceName -ErrorAction SilentlyContinue
    if ($service -and $service.Status -eq "Running") {
        Write-Host "✅ Servicio CORRIENDO" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Servicio en estado: $($service.Status)" -ForegroundColor Yellow
    }

    # Mostrar URL pública
    $urlFile = Join-Path $LogDir "public-url.txt"
    if (Test-Path $urlFile) {
        $url = Get-Content $urlFile
        Write-Host ""
        Write-Host "🌐 URL pública: $url" -ForegroundColor Green
        Write-Host ""
        Write-Host "Verifica en tu navegador o desde un celular con datos móviles." -ForegroundColor Yellow
    } else {
        Write-Host ""
        Write-Host "⏳ La URL pública aún se está generando..." -ForegroundColor Yellow
        Write-Host "   Espera 10 segundos y revisa el log:" -ForegroundColor Gray
        Write-Host "   $LogDir\tunnel-final.log" -ForegroundColor Gray
        Write-Host ""
        Write-Host "   O ejecuta: bash tools/cloudflare-tunnel.sh url" -ForegroundColor Gray
    }
}

# ── Desinstalar ──────────────────────────────────────
function Uninstall-Service {
    Show-Banner

    if (-not (Test-Admin)) {
        Write-Host "❌ Requiere permisos de Administrador." -ForegroundColor Red
        exit 1
    }

    $nssm = Get-Command nssm -ErrorAction SilentlyContinue
    if (-not $nssm) {
        Write-Host "❌ NSSM no está instalado." -ForegroundColor Red
        exit 1
    }

    Write-Host "🛑 Deteniendo servicio..." -ForegroundColor Yellow
    nssm stop $ServiceName 2>$null
    Start-Sleep -Seconds 2

    Write-Host "🗑️  Eliminando servicio..." -ForegroundColor Yellow
    nssm remove $ServiceName confirm

    Write-Host "✅ Servicio desinstalado" -ForegroundColor Green
}

# ── Iniciar ──────────────────────────────────────────
function Start-Service {
    $nssm = Get-Command nssm -ErrorAction SilentlyContinue
    if (-not $nssm) {
        Write-Host "❌ NSSM no instalado. Ejecuta primero: -Install" -ForegroundColor Red
        exit 1
    }
    nssm start $ServiceName
    Write-Host "✅ Servicio iniciado" -ForegroundColor Green
}

# ── Detener ──────────────────────────────────────────
function Stop-Service {
    $nssm = Get-Command nssm -ErrorAction SilentlyContinue
    if (-not $nssm) {
        Write-Host "❌ NSSM no instalado." -ForegroundColor Red
        exit 1
    }
    nssm stop $ServiceName
    Write-Host "✅ Servicio detenido" -ForegroundColor Green
}

# ── Estado ───────────────────────────────────────────
function Get-Status {
    Show-Banner

    $service = Get-Service $ServiceName -ErrorAction SilentlyContinue
    if (-not $service) {
        Write-Host "❌ Servicio NO instalado" -ForegroundColor Red
        Write-Host "   Para instalar: .\tools\cloudflare-tunnel-service.ps1 -Install" -ForegroundColor Yellow
        return
    }

    $statusColor = if ($service.Status -eq "Running") { "Green" } else { "Yellow" }
    Write-Host "Servicio:    $($service.Status)" -ForegroundColor $statusColor
    Write-Host "Nombre:      $($service.DisplayName)"

    $urlFile = Join-Path $LogDir "public-url.txt"
    if (Test-Path $urlFile) {
        $url = Get-Content $urlFile
        Write-Host "URL pública: $url" -ForegroundColor Cyan
    } else {
        Write-Host "URL pública: (no asignada todavía)" -ForegroundColor Yellow
    }

    # Test de conectividad
    if ($service.Status -eq "Running") {
        Write-Host ""
        Write-Host "Test de conectividad..." -ForegroundColor Cyan
        try {
            $response = Invoke-WebRequest -Uri (Get-Content $urlFile) -UseBasicParsing -TimeoutSec 10
            Write-Host "  ✅ HTTP $($response.StatusCode) - $($response.StatusDescription)" -ForegroundColor Green
        } catch {
            Write-Host "  ❌ Error: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

# ── Main ─────────────────────────────────────────────
if (-not (Test-Admin)) {
    Write-Host "⚠️  No eres administrador. Algunas funciones requieren admin." -ForegroundColor Yellow
    Write-Host ""
}

if ($Install) {
    Install-Service
} elseif ($Uninstall) {
    Uninstall-Service
} elseif ($Start) {
    Start-Service
} elseif ($Stop) {
    Stop-Service
} elseif ($Restart) {
    Stop-Service
    Start-Sleep -Seconds 2
    Start-Service
} elseif ($Status) {
    Get-Status
} else {
    Show-Banner
    Write-Host "Uso:" -ForegroundColor Yellow
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Install     Instala como servicio de Windows"
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Uninstall   Desinstala el servicio"
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Start       Inicia"
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Stop        Detiene"
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Restart     Reinicia"
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Status      Muestra estado"
    Write-Host ""
    Write-Host "Después de instalar, el tunnel se inicia automáticamente" -ForegroundColor Gray
    Write-Host "con Windows y se reconecta si se cae." -ForegroundColor Gray
}
