#!/usr/bin/env bash
###############################################################################
#  setup-hosting.sh — Asistente interactivo para configurar el hosting
#  de Bicicletería Fagua paso a paso, y los secretos en GitHub.
#
#  Uso:
#    ./tools/setup-hosting.sh
###############################################################################

set -euo pipefail

# ── Colores ──────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

REPO="JORMS-FA/fagua-bike-theme"

# ── Banner ───────────────────────────────────────────
echo -e "${BLUE}"
echo "╔══════════════════════════════════════════════════════════╗"
echo "║  🚲 Bicicletería Fagua — Setup de hosting              ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# ── Verificar gh CLI ─────────────────────────────────
if ! command -v gh >/dev/null 2>&1; then
    echo -e "${RED}❌ GitHub CLI (gh) no está instalado.${NC}"
    exit 1
fi

if ! gh repo view "$REPO" >/dev/null 2>&1; then
    echo -e "${RED}❌ No se encontró el repo $REPO${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Repositorio verificado: $REPO${NC}"
echo ""

# ── Menú ─────────────────────────────────────────────
echo -e "${YELLOW}Selecciona qué quieres hacer:${NC}"
echo ""
echo "  1. 📋 Ver instrucciones paso a paso (crear cuenta en InfinityFree)"
echo "  2. 🔐 Configurar secretos FTP en GitHub (interactivo)"
echo "  3. 🌐 Verificar estado del último deploy"
echo "  4. 🧪 Ejecutar Playwright contra URL de producción"
echo "  5. ❌ Salir"
echo ""

read -rp "Opción (1-5): " choice

case "$choice" in
    1)
        cat <<EOF
${BLUE}═══════════════════════════════════════════════════════════
📋 INSTRUCCIONES PARA CREAR CUENTA EN INFINITYFREE
═══════════════════════════════════════════════════════════${NC}

1. Abre en tu navegador: ${YELLOW}https://www.infinityfree.com/${NC}
2. Click en "Register Now"
3. Completa el registro con tu email
4. Confirma tu email
5. Inicia sesión en: ${YELLOW}https://app.infinityfree.com/${NC}

6. Click en "Create Account" para crear hosting:
   - Subdomain: elige uno (ej: ${YELLOW}faguabike${NC})
   - Te dará: ${YELLOW}faguabike.infinityfreeapp.com${NC}
   - Password: uno seguro

7. Espera ~1 minuto a que se active.

8. En el panel ve a "FTP Accounts" y anota:
   ✏️  Hostname (ej: ${YELLOW}ftpupload.net${NC})
   ✏️  Username (ej: ${YELLOW}epiz_12345678${NC})
   ✏️  Password

9. En "MySQL Databases" crea una BD nueva:
   ✏️  Database Name: ${YELLOW}fagua_bike${NC}
   ✏️  Username: ${YELLOW}fagua_user${NC}
   ✏️  Password
   ✏️  MySQL Hostname (ej: ${YELLOW}sql123.infinityfree.com${NC})

10. Vuelve al terminal y elige opción 2.

EOF
        read -rp "Presiona Enter para continuar"
        ;;

    2)
        echo ""
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo -e "${BLUE}🔐 CONFIGURAR SECRETOS EN GITHUB${NC}"
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo ""

        read -rp "FTP_SERVER (ej: ftpupload.net): " ftp_server
        read -rp "FTP_USERNAME (ej: epiz_12345678): " ftp_user
        read -rsp "FTP_PASSWORD: " ftp_password
        echo ""
        read -rp "FTP_SERVER_DIR (ej: htdocs/wp-content/themes/bicicleteria-fagua-theme/): " ftp_dir
        read -rp "PRODUCTION_URL (ej: https://faguabike.infinityfreeapp.com): " prod_url

        echo ""
        echo -e "${YELLOW}Configurando secretos...${NC}"

        for kv in \
            "FTP_SERVER=$ftp_server" \
            "FTP_USERNAME=$ftp_user" \
            "FTP_PASSWORD=$ftp_password" \
            "FTP_SERVER_DIR=$ftp_dir" \
            "PRODUCTION_URL=$prod_url"; do
            key="${kv%%=*}"
            value="${kv#*=}"
            echo -n "  → $key"
            if echo "$value" | gh secret set "$key" --repo "$REPO" 2>/dev/null; then
                echo -e " ${GREEN}✅${NC}"
            else
                echo -e " ${RED}❌${NC}"
            fi
        done

        echo ""
        echo -e "${GREEN}✅ Secretos configurados.${NC}"
        echo ""
        echo "Para disparar el primer deploy:"
        echo "  git commit --allow-empty -m 'ci: trigger first deploy'"
        echo "  git push origin main"
        echo ""
        ;;

    3)
        echo ""
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo -e "${BLUE}🌐 ÚLTIMOS DEPLOYS${NC}"
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo ""
        gh run list --repo "$REPO" --limit 10
        ;;

    4)
        echo ""
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo -e "${BLUE}🧪 PLAYWRIGHT E2E${NC}"
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo ""
        read -rp "URL de producción (ej: https://faguabike.infinityfreeapp.com): " url

        if [ ! -d "node_modules" ]; then
            echo -e "${YELLOW}Instalando dependencias...${NC}"
            npm install --no-audit --no-fund
        fi

        echo -e "${YELLOW}Ejecutando Playwright contra $url ...${NC}"
        BASE_URL="$url" npx playwright test
        ;;

    5)
        exit 0
        ;;

    *)
        echo -e "${RED}Opción inválida${NC}"
        exit 1
        ;;
esac
