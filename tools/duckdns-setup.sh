#!/usr/bin/env bash
###############################################################################
#  duckdns-setup.sh — Registra y actualiza bicicleteriafagua.duckdns.org
#
#  DuckDNS es un servicio gratuito de DNS dinámico. Te asigna un subdominio
#  *.duckdns.org que puede actualizarse vía API.
#
#  Este script:
#    1. Verifica que el subdominio esté disponible
#    2. Lo registra (si tienes token) o muestra instrucciones
#    3. Configura un cron/scheduled task que actualiza la IP cada 5 min
#
#  Para registrarte (gratis):
#    - Ve a https://www.duckdns.org/
#    - Inicia sesión con Google/GitHub/etc
#    - Crea un subdominio: bicicleteriafagua
#    - Te dará un TOKEN (string largo)
#    - Pasa el token a este script
#
#  Uso:
#    ./tools/duckdns-setup.sh YOUR_TOKEN_HERE
###############################################################################

set -euo pipefail

# ── Configuración ────────────────────────────────────
SUBDOMAIN="bicicleteriafagua"
FULL_DOMAIN="${SUBDOMAIN}.duckdns.org"
TUNNEL_LOG_DIR="$HOME/.cloudflared"
DUCKDNS_LOG="$TUNNEL_LOG_DIR/duckdns.log"

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

log() {
    echo -e "[$(date +'%Y-%m-%d %H:%M:%S')] $*" | tee -a "$DUCKDNS_LOG"
}

mkdir -p "$TUNNEL_LOG_DIR"

# ── Validar argumentos ───────────────────────────────
TOKEN="${1:-${DUCKDNS_TOKEN:-}}"

if [ -z "$TOKEN" ]; then
    cat <<EOF
${YELLOW}═══════════════════════════════════════════════════════════
📋 CÓMO OBTENER TU TOKEN DE DUCKDNS
═══════════════════════════════════════════════════════════${NC}

1. Ve a ${BLUE}https://www.duckdns.org/${NC}
2. Click en "Sign in" (esquina superior derecha)
3. Inicia sesión con Google, GitHub, etc.
4. En el panel principal, en "Step 1: create a domain":
   - Escribe: ${GREEN}bicicleteriafagua${NC}
   - Click "+ add domain"
5. En la tabla de dominios verás:
   - domain: ${GREEN}bicicleteriafagua${NC}
   - token: ${GREEN}un-string-muy-largo-aqui${NC}
6. Copia el token y ejecuta este script:
   ${YELLOW}./tools/duckdns-setup.sh TU_TOKEN_COPIADO${NC}

${YELLOW}═══════════════════════════════════════════════════════════${NC}
EOF
    exit 0
fi

# ── Validar formato del token ────────────────────────
if [ ${#TOKEN} -lt 20 ]; then
    echo -e "${RED}❌ El token parece muy corto. Verifica que copiaste el token completo.${NC}"
    exit 1
fi

# ── Registrar/actualizar dominio en DuckDNS ──────────
log "${BLUE}🌐 Registrando $FULL_DOMAIN en DuckDNS...${NC}"

# La API de DuckDNS: https://www.duckdns.org/update?domains=SUBDOMAIN&token=TOKEN&ip=
RESPONSE=$(curl -s "https://www.duckdns.org/update?domains=${SUBDOMAIN}&token=${TOKEN}&ip=" 2>&1)

case "$RESPONSE" in
    "OK")
        log "${GREEN}✅ Dominio $FULL_DOMAIN registrado/actualizado correctamente${NC}"
        ;;
    "KO")
        log "${RED}❌ Token inválido. Verifica que copiaste bien.${NC}"
        exit 1
        *)
        log "${RED}❌ Respuesta inesperada de DuckDNS: $RESPONSE${NC}"
        exit 1
        ;;
esac

# ── Configurar actualización automática (Windows Task Scheduler) ──
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" || "$OSTYPE" == "cygwin" ]]; then
    log "${BLUE}⏰ Configurando actualización automática cada 5 min en Windows...${NC}"

    SCRIPT_PATH="$(cygpath -w "$(realpath "$0")")"
    TASK_NAME="DuckDNS-Update-BicicleteriaFagua"

    # Crear tarea programada (cada 5 minutos, por 12 horas = 144 ejecuciones)
    powershell.exe -Command "
        \$action = New-ScheduledTaskAction -Execute 'C:\Program Files\Git\bin\bash.exe' -Argument '$SCRIPT_PATH $TOKEN'
        \$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 5) -RepetitionDuration (New-TimeSpan -Hours 720)
        \$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable

        # Eliminar si existe
        Unregister-ScheduledTask -TaskName '$TASK_NAME' -Confirm:\$false -ErrorAction SilentlyContinue

        Register-ScheduledTask -TaskName '$TASK_NAME' -Action \$action -Trigger \$trigger -Settings \$settings -Description 'Actualiza la IP de bicicleteriafagua.duckdns.org cada 5 min'
        Write-Host '✅ Tarea programada creada: $TASK_NAME'
    "
fi

# ── Verificar resolución DNS ─────────────────────────
log "${BLUE}🔍 Verificando resolución DNS de $FULL_DOMAIN...${NC}"
sleep 2

# Resolver con múltiples servidores DNS
for dns in "1.1.1.1" "8.8.8.8" "9.9.9.9"; do
    RESOLVED=$(dig +short "@$dns" "$FULL_DOMAIN" 2>/dev/null | head -1)
    if [ -n "$RESOLVED" ]; then
        log "${GREEN}✅ $FULL_DOMAIN → $RESOLVED (via $dns)${NC}"
    fi
done

# ── Información del tunnel (si está corriendo) ───────
PUBLIC_URL_FILE="$TUNNEL_LOG_DIR/public-url.txt"
if [ -f "$PUBLIC_URL_FILE" ]; then
    PUBLIC_URL=$(cat "$PUBLIC_URL_FILE")
    log ""
    log "${BLUE}══════════════════════════════════════════════════════════${NC}"
    log "${GREEN}  RESUMEN${NC}"
    log "${BLUE}══════════════════════════════════════════════════════════${NC}"
    log "  Subdominio DuckDNS:  ${GREEN}https://$FULL_DOMAIN${NC}"
    log "  Tunnel directo:      ${BLUE}$PUBLIC_URL${NC}"
    log ""
    log "${YELLOW}⚠️  IMPORTANTE:${NC}"
    log "  Tu subdominio DuckDNS ($FULL_DOMAIN) ahora apunta a una"
    log "  IP dinámica de tu casa, NO al tunnel de Cloudflare."
    log "  Para que apunte al tunnel, necesitas:"
    log "  1. Que la IP de tu casa sea estable (DuckDNS ya la actualiza)"
    log "  2. Que el puerto 80/443 de tu casa esté abierto (port forwarding)"
    log "  3. O migrar a un Named Tunnel con dominio custom (recomendado)"
    log ""
    log "  ${YELLOW}Recomendación:${NC} usa directamente la URL del tunnel"
    log "  (https://xxxx.trycloudflare.com) que es más estable y tiene"
    log "  HTTPS automático. La URL DuckDNS solo es útil si combinas"
    log "  con port forwarding (no recomendado por seguridad)."
fi
