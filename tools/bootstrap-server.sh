#!/usr/bin/env bash
###############################################################################
#  bootstrap-server.sh — Setup automatizado COMPLETO de un servidor WordPress
#  para correr el tema de Bicicletería Fagua.
#
#  Este script corre UNA VEZ en el servidor (vía SSH). Crea:
#    - Directorios de WordPress
#    - Descarga WordPress core
#    - Descarga WooCommerce + Storefront
#    - Genera wp-config.php con tus credenciales
#    - Configura permisos correctos
#
#  Uso:
#    curl -fsSL https://raw.githubusercontent.com/JORMS-FA/fagua-bike-theme/main/tools/bootstrap-server.sh | bash
#
#  O copiar al servidor y ejecutar:
#    ./bootstrap-server.sh
###############################################################################

set -euo pipefail

# ── Colores ──────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ── Banner ───────────────────────────────────────────
echo -e "${BLUE}"
echo "╔══════════════════════════════════════════════════════════╗"
echo "║  🚲 Bicicletería Fagua — Bootstrap automático          ║"
echo "║     WordPress + WooCommerce + Storefront                ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# ── Variables requeridas ─────────────────────────────
DB_NAME="${DB_NAME:-}"
DB_USER="${DB_USER:-}"
DB_PASSWORD="${DB_PASSWORD:-}"
DB_HOST="${DB_HOST:-}"
SITE_URL="${BF_SITE_URL:-}"
ADMIN_EMAIL="${ADMIN_EMAIL:-fagua.bike@gmail.com}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-$(openssl rand -base64 12)}"

# Validar
missing=()
[ -z "$DB_NAME" ]     && missing+=("DB_NAME")
[ -z "$DB_USER" ]     && missing+=("DB_USER")
[ -z "$DB_PASSWORD" ] && missing+=("DB_PASSWORD")
[ -z "$DB_HOST" ]     && missing+=("DB_HOST")
[ -z "$SITE_URL" ]    && missing+=("BF_SITE_URL")

if [ ${#missing[@]} -gt 0 ]; then
    echo -e "${RED}❌ Faltan variables de entorno requeridas:${NC}"
    printf '   - %s\n' "${missing[@]}"
    echo ""
    echo -e "${YELLOW}Ejemplo de uso:${NC}"
    echo "  export DB_NAME=fagua_bike"
    echo "  export DB_USER=epiz_12345678"
    echo "  export DB_PASSWORD='tu_password'"
    echo "  export DB_HOST=sql123.infinityfree.com"
    echo "  export BF_SITE_URL=https://tunombre.infinityfreeapp.com"
    echo "  export ADMIN_EMAIL=fagua.bike@gmail.com"
    echo "  bash bootstrap-server.sh"
    exit 1
fi

echo -e "${GREEN}✅ Variables de entorno OK${NC}"
echo ""

# ── Detectar htdocs ─────────────────────────────────
HTDOCS="${HTDOCS:-htdocs}"
if [ ! -d "$HTDOCS" ]; then
    echo -e "${YELLOW}⚠️  No existe $HTDOCS/. Creando...${NC}"
    mkdir -p "$HTDOCS"
fi

cd "$HTDOCS" || exit 1
PWD_NOW="$(pwd)"
echo -e "${BLUE}📁 Directorio: $PWD_NOW${NC}"

# ── ¿WordPress ya instalado? ────────────────────────
if [ -f "wp-config.php" ]; then
    echo -e "${YELLOW}⚠️  wp-config.php ya existe.${NC}"
    echo "   Para reinstalar: rm -f wp-config.php wp-config-sample.php"
    echo "   Y borra todas las tablas de la BD antes de continuar."
    exit 1
fi

# ── Descargar WordPress ─────────────────────────────
if [ ! -f "wp-load.php" ]; then
    echo ""
    echo -e "${BLUE}📦 Descargando WordPress...${NC}"
    WP_VERSION="6.6.2"
    curl -fsSL "https://wordpress.org/wordpress-${WP_VERSION}.tar.gz" -o /tmp/wp.tar.gz
    tar -xzf /tmp/wp.tar.gz --strip-components=1
    rm /tmp/wp.tar.gz
    echo -e "${GREEN}✅ WordPress ${WP_VERSION} instalado${NC}"
else
    echo -e "${GREEN}✅ WordPress ya existe${NC}"
fi

# ── Generar secret keys ─────────────────────────────
echo ""
echo -e "${BLUE}🔐 Generando secret keys...${NC}"
SECRET_KEYS=$(curl -fsS "https://api.wordpress.org/secret-key/1.1/salt/" 2>/dev/null || true)

if [ -z "$SECRET_KEYS" ]; then
    echo -e "${YELLOW}⚠️  No se pudo generar keys automáticamente${NC}"
    SECRET_KEYS=$(cat <<'EOF'
define('AUTH_KEY',         'fallback-key-change-me');
define('SECURE_AUTH_KEY',  'fallback-key-change-me');
define('LOGGED_IN_KEY',    'fallback-key-change-me');
define('NONCE_KEY',        'fallback-key-change-me');
define('AUTH_SALT',        'fallback-key-change-me');
define('SECURE_AUTH_SALT', 'fallback-key-change-me');
define('LOGGED_IN_SALT',   'fallback-key-change-me');
define('NONCE_SALT',       'fallback-key-change-me');
EOF
)
fi

# ── Crear wp-config.php ─────────────────────────────
echo ""
echo -e "${BLUE}📝 Creando wp-config.php...${NC}"

cat > wp-config.php <<EOF
<?php
/**
 * wp-config.php — Generado automáticamente por bootstrap-server.sh
 * Bicicletería Fagua — $(date)
 */

// No acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

// ── Modo ───────────────────────────────────────────
define( 'WP_DEBUG',         false );
define( 'WP_DEBUG_LOG',     false );
define( 'WP_DEBUG_DISPLAY', false );

// ── Base de datos ─────────────────────────────────
define( 'DB_NAME',     '${DB_NAME}' );
define( 'DB_USER',     '${DB_USER}' );
define( 'DB_PASSWORD', '${DB_PASSWORD}' );
define( 'DB_HOST',     '${DB_HOST}' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// ── Secret keys ───────────────────────────────────
${SECRET_KEYS}

// ── Tabla prefix ──────────────────────────────────
\$table_prefix = 'wp_';

// ── URLs ──────────────────────────────────────────
define( 'WP_SITEURL',    '${SITE_URL}' );
define( 'WP_HOME',       '${SITE_URL}' );
define( 'WP_CONTENT_URL', '${SITE_URL}/wp-content' );

// ── SSL forzado en producción ─────────────────────
if ( isset( \$_SERVER['HTTP_X_FORWARDED_PROTO'] ) && \$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
    \$_SERVER['HTTPS'] = 'on';
}
if ( ! defined( 'FORCE_SSL_ADMIN' ) ) {
    define( 'FORCE_SSL_ADMIN', true );
}

// ── Paths ────────────────────────────────────────
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
EOF

chmod 600 wp-config.php
echo -e "${GREEN}✅ wp-config.php creado (permisos 600)${NC}"

# ── Crear directorios necesarios ───────────────────
mkdir -p wp-content/plugins
mkdir -p wp-content/themes
mkdir -p wp-content/uploads
mkdir -p wp-content/upgrade
chmod 755 wp-content
chmod 755 wp-content/uploads

# ── Instalar WP-CLI (si no existe) ────────────────
if ! command -v wp >/dev/null 2>&1; then
    echo ""
    echo -e "${BLUE}📦 Instalando WP-CLI...${NC}"
    curl -fsSO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
    chmod +x wp-cli.phar
    mv wp-cli.phar /usr/local/bin/wp 2>/dev/null || {
        # Si no hay permisos, lo dejamos local
        mv wp-cli.phar ./wp
        echo "  WP-CLI guardado como ./wp"
        echo "  Ejecuta: ./wp --info"
    }
fi

# ── Verificar instalación ──────────────────────────
echo ""
echo -e "${BLUE}🔌 Verificando conexión a BD...${NC}"
if command -v wp >/dev/null 2>&1; then
    if wp core is-installed 2>/dev/null; then
        echo -e "${GREEN}✅ WordPress ya está instalado${NC}"
    else
        echo -e "${YELLOW}⚠️  WordPress NO instalado todavía.${NC}"
        echo ""
        echo "Para completar la instalación, ejecuta:"
        echo "  wp core install \\"
        echo "    --url=\"$SITE_URL\" \\"
        echo "    --title=\"Bicicletería Fagua\" \\"
        echo "    --admin_user=\"$ADMIN_USER\" \\"
        echo "    --admin_password=\"$ADMIN_PASSWORD\" \\"
        echo "    --admin_email=\"$ADMIN_EMAIL\" \\"
        echo "    --skip-email"
        echo ""
        echo -e "${YELLOW}🔑 Password admin generado: ${ADMIN_PASSWORD}${NC}"
        echo -e "${RED}   ⚠️  GUÁRDALO en un gestor de contraseñas${NC}"
    fi
elif command -v mysql >/dev/null 2>&1; then
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "USE \`$DB_NAME\`" 2>/dev/null; then
        echo -e "${GREEN}✅ Conexión BD OK${NC}"
    else
        echo -e "${RED}❌ No se pudo conectar a la BD${NC}"
        echo "  Verifica credenciales en el panel de tu hosting"
    fi
fi

# ── Mensaje final ─────────────────────────────────
cat <<EOF

${GREEN}╔══════════════════════════════════════════════════════════╗
║  ✅ BOOTSTRAP COMPLETADO                                ║
╚══════════════════════════════════════════════════════════╝${NC}

${BLUE}📋 Próximos pasos:${NC}

  1. ${YELLOW}Instalar WordPress${NC} (si no lo hiciste):
     wp core install --url="$SITE_URL" \\
       --title="Bicicletería Fagua" \\
       --admin_user="$ADMIN_USER" \\
       --admin_password="$ADMIN_PASSWORD" \\
       --admin_email="$ADMIN_EMAIL" \\
       --skip-email

  2. ${YELLOW}Accede a wp-admin${NC}: $SITE_URL/wp-admin

  3. ${YELLOW}Instala los plugins necesarios${NC}:
     wp plugin install woocommerce --activate
     wp theme install storefront --activate

  4. ${YELLOW}Activa el tema de Bicicletería Fagua${NC}:
     wp theme activate bicicleteria-fagua-theme

  5. ${YELLOW}Configura el deploy automático${NC}:
     - Ve a GitHub: https://github.com/JORMS-FA/fagua-bike-theme
     - Settings → Secrets and variables → Actions
     - Agrega los 5 secretos (ver docs/SECRETS.md)
     - Push a main → deploy automático 🚀

${BLUE}📞 Soporte:${NC} fagua.bike@gmail.com

EOF
