# cloudflare-tunnel-task.ps1
# Configura el Cloudflare Tunnel como tarea programada de Windows
# (alternativa sin NSSM, usa el Programador de Tareas nativo)

param(
    [switch]$Install,
    [switch]$Uninstall,
    [switch]$Status
)

$TaskName = "BicicleteriaFagua-CloudflareTunnel"
$RepoDir = "C:\Users\fagua\dev\fagua-bike-theme"
$ScriptPath = Join-Path $RepoDir "tools\cloudflare-tunnel.sh"
$BashPath = "C:\Program Files\Git\bin\bash.exe"

function Show-Banner {
    Write-Host ""
    Write-Host "=== Bicicleteria Fagua - Cloudflare Tunnel (Task Scheduler) ===" -ForegroundColor Cyan
    Write-Host ""
}

function Install-Task {
    Show-Banner

    Write-Host "Configurando tarea programada de Windows..." -ForegroundColor Yellow
    Write-Host ""

    # Argumento: bash.exe ejecuta el script en modo servicio (loop infinito)
    $Arguments = "-ArgumentList `"$ScriptPath`" run-as-service"

    # Acción: ejecutar Git Bash con el script en modo servicio
    $action = New-ScheduledTaskAction -Execute $BashPath -Argument "`"$ScriptPath`" run-as-service" -WorkingDirectory $RepoDir

    # Trigger: al iniciar sesión, con delay de 30s para que la red esté lista
    $trigger = New-ScheduledTaskTrigger -AtLogOn
    $trigger.Delay = "PT30S"  # 30 segundos de delay

    # Settings
    $settings = New-ScheduledTaskSettingsSet `
        -AllowStartIfOnBatteries `
        -DontStopIfGoingOnBatteries `
        -StartWhenAvailable `
        -RestartCount 999 `
        -RestartInterval (New-TimeSpan -Minutes 1) `
        -ExecutionTimeLimit (New-TimeSpan -Hours 0)

    # Eliminar si existe
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false -ErrorAction SilentlyContinue

    try {
        Register-ScheduledTask `
            -TaskName $TaskName `
            -Action $action `
            -Trigger $trigger `
            -Settings $settings `
            -Description "Mantiene el Cloudflare Tunnel activo hacia LocalWP. Se reinicia automáticamente si falla." `
            -RunLevel Highest `
            -Force | Out-Null

        Write-Host "OK Tarea programada instalada correctamente" -ForegroundColor Green
        Write-Host ""
        Write-Host "Nombre: $TaskName" -ForegroundColor Cyan
        Write-Host "Trigger: Al iniciar sesion (con 30s de delay)" -ForegroundColor Cyan
        Write-Host "Reintentos: 999 (cada 1 minuto)" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Para iniciar manualmente ahora:" -ForegroundColor Yellow
        Write-Host "  Start-ScheduledTask -TaskName '$TaskName'" -ForegroundColor Gray
    } catch {
        Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host ""
        Write-Host "Si el error es de permisos, ejecuta PowerShell como Administrador:" -ForegroundColor Yellow
        Write-Host "  Click derecho en PowerShell > Ejecutar como administrador" -ForegroundColor Yellow
    }
}

function Uninstall-Task {
    Show-Banner

    try {
        Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false -ErrorAction Stop
        Write-Host "OK Tarea '$TaskName' eliminada" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    }
}

function Get-Status {
    Show-Banner

    $task = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
    if (-not $task) {
        Write-Host "Tarea NO instalada" -ForegroundColor Red
        Write-Host "Para instalar: .\tools\cloudflare-tunnel-task.ps1 -Install" -ForegroundColor Yellow
        return
    }

    Write-Host "Tarea: $($task.TaskName)" -ForegroundColor Cyan
    Write-Host "Estado: $($task.State)" -ForegroundColor $(if($task.State -eq "Ready"){"Green"}else{"Yellow"})

    $info = Get-ScheduledTaskInfo -TaskName $TaskName -ErrorAction SilentlyContinue
    if ($info) {
        Write-Host "Ultima ejecucion: $($info.LastRunTime)"
        Write-Host "Resultado: $($info.LastTaskResult)"
        Write-Host "Proxima ejecucion: $($info.NextRunTime)"
    }

    # URL pública
    $urlFile = "$env:USERPROFILE\.cloudflared\public-url.txt"
    if (Test-Path $urlFile) {
        $url = Get-Content $urlFile
        Write-Host ""
        Write-Host "URL publica: $url" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "URL publica: (no asignada todavia)" -ForegroundColor Yellow
        Write-Host "Espera 30 segundos despues del inicio" -ForegroundColor Gray
    }
}

# Main
if ($Install) {
    Install-Task
} elseif ($Uninstall) {
    Uninstall-Task
} elseif ($Status) {
    Get-Status
} else {
    Show-Banner
    Write-Host "Uso:" -ForegroundColor Yellow
    Write-Host "  .\tools\cloudflare-tunnel-task.ps1 -Install    Instala la tarea"
    Write-Host "  .\tools\cloudflare-tunnel-task.ps1 -Uninstall  Desinstala"
    Write-Host "  .\tools\cloudflare-tunnel-task.ps1 -Status     Muestra estado"
    Write-Host ""
    Write-Host "Alternativa (con NSSM, requiere admin):" -ForegroundColor Gray
    Write-Host "  .\tools\cloudflare-tunnel-service.ps1 -Install" -ForegroundColor Gray
}
