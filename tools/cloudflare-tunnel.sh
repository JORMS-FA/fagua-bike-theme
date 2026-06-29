#!/usr/bin/env bash
###############################################################################
#  cloudflare-tunnel.sh — Mantiene un Cloudflare Tunnel activo hacia LocalWP
#
#  Este script corre en loop infinito. Si el tunnel muere por cualquier razón,
#  lo reinicia automáticamente.
#
#  - URL local: http://bicicleteriafagua.local:10016
#  - URL pública: https://xxxx.trycloudflare.com (URL fija en tunnel-final.log)
#  - SSL: automático (gestionado por Cloudflare)
#  - Persistencia: este script se ejecuta como servicio de Windows
#
#  Uso:
#    ./tools/cloudflare-tunnel.sh                  # Inicia tunnel (foreground)
#    ./tools/cloudflare-tunnel.sh install-service  # Instala como servicio
#    ./tools/cloudflare-tunnel.sh stop             # Detiene
#    ./tools/cloudflare-tunnel.sh status           # Estado
#    ./tools/cloudflare-tunnel.sh url              # Muestra la URL pública
###############################################################################

set -uo pipefail

# ── Configuración ────────────────────────────────────
LOCAL_URL="http://bicicleteriafagua.local:10016"
LOG_DIR="$HOME/.cloudflared"
LOG_FILE="$LOG_DIR/tunnel-final.log"
PID_FILE="$LOG_DIR/tunnel.pid"
SERVICE_NAME="BicicleteriaFagua-Tunnel"
RESTART_DELAY=5
MAX_RESTARTS=0  # 0 = infinito

# ── Colores ──────────────────────────────────────────
if [ -t 1 ]; then
    RED='\033[0;31m'
    GREEN='\033[0;32m'
    YELLOW='\033[1;33m'
    BLUE='\033[0;34m'
    NC='\033[0m'
else
    RED='' GREEN='' YELLOW='' BLUE='' NC=''
fi

mkdir -p "$LOG_DIR"

# ── Helpers ──────────────────────────────────────────
log() {
    echo -e "[$(date +'%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"
}

extract_url() {
    # Busca la URL https://*.trycloudflare.com en el log
    grep -oE 'https://[a-z0-9-]+\.trycloudflare\.com' "$LOG_FILE" 2>/dev/null | tail -1
}

is_running() {
    if [ -f "$PID_FILE" ]; then
        local pid
        pid=$(cat "$PID_FILE" 2>/dev/null || echo "")
        if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
            return 0
        fi
    fi
    # Verificar si hay cloudflared corriendo
    pgrep -f "cloudflared tunnel --url" >/dev/null 2>&1
}

start_tunnel() {
    log "${BLUE}🚀 Iniciando Cloudflare Tunnel hacia $LOCAL_URL${NC}"

    # Limpiar log anterior (conservar últimas líneas)
    if [ -f "$LOG_FILE" ]; then
        tail -50 "$LOG_FILE" > "$LOG_FILE.tmp"
        mv "$LOG_FILE.tmp" "$LOG_FILE"
    fi

    # Verificar que cloudflared está instalado
    if ! command -v cloudflared >/dev/null 2>&1; then
        log "${RED}❌ cloudflared no está instalado${NC}"
        log "   Instala con: winget install Cloudflare.cloudflared"
        return 1
    fi

    # Verificar que LocalWP responde
    if ! curl -sfo /dev/null --max-time 5 "$LOCAL_URL" 2>/dev/null; then
        log "${YELLOW}⚠️  LocalWP no responde en $LOCAL_URL${NC}"
        log "   Asegúrate de que el sitio 'bicicleteria-fagua' esté corriendo en LocalWP"
        # No detenemos — el tunnel seguirá intentando
    fi

    # Iniciar cloudflared en background
    nohup cloudflared tunnel --url "$LOCAL_URL" --no-autoupdate >> "$LOG_FILE" 2>&1 &
    local pid=$!
    echo "$pid" > "$PID_FILE"
    log "${GREEN}✅ Tunnel iniciado (PID: $pid)${NC}"

    # Esperar a que se cree la URL
    log "⏳ Esperando a que Cloudflare asigne URL pública..."
    local attempts=0
    local max_attempts=30
    while [ $attempts -lt $max_attempts ]; do
        sleep 2
        local url
        url=$(extract_url)
        if [ -n "$url" ]; then
            log "${GREEN}✅ URL pública: $url${NC}"
            # Guardar URL en archivo separado
            echo "$url" > "$LOG_DIR/public-url.txt"
            return 0
        fi
        attempts=$((attempts + 1))
    done

    log "${YELLOW}⚠️  No se detectó URL después de ${max_attempts} intentos${NC}"
    log "   Revisa $LOG_FILE para más detalles"
    return 1
}

stop_tunnel() {
    log "${YELLOW}🛑 Deteniendo tunnel...${NC}"
    if [ -f "$PID_FILE" ]; then
        local pid
        pid=$(cat "$PID_FILE" 2>/dev/null)
        if [ -n "$pid" ]; then
            kill "$pid" 2>/dev/null || true
            # Matar también el proceso tree
            pkill -P "$pid" 2>/dev/null || true
        fi
        rm -f "$PID_FILE"
    fi
    pkill -f "cloudflared tunnel --url" 2>/dev/null || true
    log "${GREEN}✅ Tunnel detenido${NC}"
}

show_status() {
    if is_running; then
        local url
        url=$(cat "$LOG_DIR/public-url.txt" 2>/dev/null || extract_url || echo "(esperando...)")
        echo -e "${GREEN}●${NC} Tunnel CORRIENDO"
        echo -e "  URL pública: ${BLUE}$url${NC}"
        echo -e "  URL local:   $LOCAL_URL"
        echo -e "  Log:         $LOG_FILE"
    else
        echo -e "${RED}●${NC} Tunnel DETENIDO"
    fi
}

show_url() {
    local url
    url=$(cat "$LOG_DIR/public-url.txt" 2>/dev/null || extract_url || echo "")
    if [ -n "$url" ]; then
        echo "$url"
    else
        echo "(Tunnel no iniciado o URL no asignada aún)"
        return 1
    fi
}

install_windows_service() {
    # Solo funciona en Windows con permisos de admin
    if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" && "$OSTYPE" != "cygwin" ]]; then
        echo "❌ install-service solo funciona en Windows (Git Bash/WSL/MSYS)"
        exit 1
    fi

    local script_path
    script_path="$(cygpath -w "$(realpath "$0")")"
    local nssm_path
    nssm_path=$(command -v nssm 2>/dev/null || echo "")

    if [ -z "$nssm_path" ]; then
        echo "❌ NSSM no está instalado. Instala con:"
        echo "   winget install nssm"
        exit 1
    fi

    # Usar PowerShell para llamar a NSSM con paths nativos
    powershell.exe -Command "
        \$nssm = '$nssm_path'
        \$service = '$SERVICE_NAME'
        \$script = '$script_path'

        # Detener si existe
        & \$nssm stop \$service 2>\$null

        # Instalar
        & \$nssm install \$service 'C:\Program Files\Git\bin\bash.exe' \"\$script\" 'run-as-service'
        & \$nssm set \$service AppDirectory 'C:\Users\fagua\dev\fagua-bike-theme'
        & \$nssm set \$service DisplayName 'Bicicletería Fagua - Cloudflare Tunnel'
        & \$nssm set \$service Description 'Mantiene un tunnel público hacia LocalWP en http://bicicleteriafagua.local:10016'
        & \$nssm set \$service Start SERVICE_AUTO_START
        & \$nssm set \$service AppStdout '$LOG_DIR\service-stdout.log'
        & \$nssm set \$service AppStderr '$LOG_DIR\service-stderr.log'
        & \$nssm set \$service AppRotateFiles 1
        & \$nssm set \$service AppRotateBytes 1048576

        # Iniciar
        & \$nssm start \$service
        Write-Host '✅ Servicio instalado e iniciado'
    "
}

uninstall_windows_service() {
    if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" && "$OSTYPE" != "cygwin" ]]; then
        echo "❌ uninstall-service solo funciona en Windows"
        exit 1
    fi

    local nssm_path
    nssm_path=$(command -v nssm 2>/dev/null || echo "")

    if [ -z "$nssm_path" ]; then
        echo "❌ NSSM no está instalado"
        exit 1
    fi

    powershell.exe -Command "
        \$nssm = '$nssm_path'
        \$service = '$SERVICE_NAME'

        & \$nssm stop \$service 2>\$null
        & \$nssm remove \$service confirm
        Write-Host '✅ Servicio desinstalado'
    "
}

# ── Modo service: loop infinito con auto-reconexión ──
run_as_service() {
    log "${BLUE}══════════════════════════════════════════════════════════${NC}"
    log "${BLUE}  Modo servicio: auto-reconexión activada${NC}"
    log "${BLUE}══════════════════════════════════════════════════════════${NC}"

    local restarts=0
    while true; do
        if [ $MAX_RESTARTS -gt 0 ] && [ $restarts -ge $MAX_RESTARTS ]; then
            log "${RED}❌ Máximo de restarts ($MAX_RESTARTS) alcanzado. Saliendo.${NC}"
            exit 1
        fi

        start_tunnel
        restarts=$((restarts + 1))

        # Monitorear el proceso cada 30 segundos
        while is_running; do
            sleep 30

            # Verificar que la URL sigue respondiendo
            local current_url
            current_url=$(cat "$LOG_DIR/public-url.txt" 2>/dev/null || extract_url || echo "")
            if [ -n "$current_url" ]; then
                if ! curl -sfo /dev/null --max-time 10 "$current_url" 2>/dev/null; then
                    log "${YELLOW}⚠️  URL no responde, reiniciando tunnel${NC}"
                    stop_tunnel
                    sleep 5
                    break
                fi
            fi
        done

        log "${YELLOW}⚠️  Tunnel murió. Reiniciando en $RESTART_DELAY segundos...${NC}"
        sleep $RESTART_DELAY
    done
}

# ── Main ─────────────────────────────────────────────
case "${1:-start}" in
    start|"")
        if is_running; then
            echo "⚠️  Tunnel ya está corriendo"
            show_status
            exit 0
        fi
        start_tunnel
        show_status
        ;;
    stop)
        stop_tunnel
        ;;
    restart)
        stop_tunnel
        sleep 2
        start_tunnel
        show_status
        ;;
    status)
        show_status
        ;;
    url)
        show_url
        ;;
    install-service)
        install_windows_service
        ;;
    uninstall-service)
        uninstall_windows_service
        ;;
    run-as-service|service)
        run_as_service
        ;;
    *)
        echo "Uso: $0 {start|stop|restart|status|url|install-service|uninstall-service}"
        exit 1
        ;;
esac
