#!/usr/bin/env bash
###############################################################################
#  deploy-setup.sh — Bootstrap inicial de un servidor limpio (InfinityFree u
#  otro hosting con acceso FTP/SFTP) para correr WordPress + WooCommerce con
#  el tema de Bicicletería Fagua.
#
#  Este script NO corre en GitHub Actions. Corre UNA vez en el servidor (vía
#  SSH o el panel del hosting) para preparar el entorno. Los deploys
#  siguientes son automáticos vía GitHub Actions → FTP.
#
#  Uso:
#    1. Crea la cuenta en InfinityFree y obtén:
#         - Hostname FTP
#         - Usuario FTP
#         - Password FTP
#         - Nombre de la BD MySQL
#         - Usuario MySQL
#         - Password MySQL
#         - Host MySQL
#    2. Descarga WordPress en htdocs/ (o desde el panel de InfinityFree
#       usa Softaculous → WordPress).
#    3. Crea wp-config.php a partir de wp-config.php.template con tus datos.
#    4. Activa WooCommerce, Storefront, y el tema bicicleteria-fagua-theme
#       (que llega vía FTP desde GitHub Actions).
#    5. Siembra el catálogo (opcional):
#         wp eval-file wp-content/themes/bicicleteria-fagua-theme/inc/seed-catalog.php
###############################################################################

set -euo pipefail

echo "╔══════════════════════════════════════════════════════════╗"
echo "║  Bicicletería Fagua — Bootstrap de servidor             ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

# ── 1. Validar variables de entorno ─────────────────
required_vars=(
    "DB_NAME" "DB_USER" "DB_PASSWORD" "DB_HOST"
    "BF_SITE_URL"
)

missing=()
for v in "${required_vars[@]}"; do
    if [ -z "${!v:-}" ]; then
        missing+=("$v")
    fi
done

if [ ${#missing[@]} -gt 0 ]; then
    echo "❌ Faltan variables de entorno requeridas:"
    printf '   - %s\n' "${missing[@]}"
    echo ""
    echo "   Ejemplo:"
    echo "     export DB_NAME=fagua_bike"
    echo "     export DB_USER=fagua_user"
    echo "     export DB_PASSWORD=tu_password_seguro"
    echo "     export DB_HOST=sql123.infinityfree.com"
    echo "     export BF_SITE_URL=https://faguabike.infinityfreeapp.com"
    echo "     export BF_ENV=production"
    exit 1
fi

echo "✅ Variables de entorno OK"

# ── 2. Generar secret keys (si no están) ─────────────
generate_keys() {
    curl -fsS https://api.wordpress.org/secret-key/1.1/salt/ 2>/dev/null || {
        echo "⚠️  No se pudo generar keys automáticamente. Usa https://api.wordpress.org/secret-key/1.1/salt/ manualmente."
    }
}

# ── 3. Crear wp-config.php desde template ───────────
SITE_DIR="${SITE_DIR:-$(pwd)}"
TEMPLATE="$SITE_DIR/wp-content/themes/bicicleteria-fagua-theme/wp-config.php.template"
TARGET="$SITE_DIR/wp-config.php"

if [ -f "$TARGET" ]; then
    echo "⚠️  wp-config.php ya existe. No se sobrescribe."
    echo "   Si quieres regenerarlo, bórralo primero: rm $TARGET"
else
    if [ ! -f "$TEMPLATE" ]; then
        echo "❌ No se encontró la plantilla: $TEMPLATE"
        exit 1
    fi

    echo "📝 Generando wp-config.php desde plantilla..."
    sed "s|put-your-unique-phrase-here|$(generate_keys | head -1 | grep -oP "'[^']*'" | head -1 | tr -d "'")|" "$TEMPLATE" > "$TARGET" || true

    # Si la generación de keys falló, usar el template literal
    if grep -q "put-your-unique-phrase-here" "$TARGET"; then
        cp "$TEMPLATE" "$TARGET"
        echo "⚠️  Genera las keys manualmente en wp-config.php"
    fi

    # Permisos seguros
    chmod 600 "$TARGET"
    echo "✅ wp-config.php creado (permisos 600)"
fi

# ── 4. Validar estructura de directorios ────────────
dirs=(
    "wp-admin"
    "wp-includes"
    "wp-content/plugins"
    "wp-content/uploads"
    "wp-content/themes/bicicleteria-fagua-theme"
)

for d in "${dirs[@]}"; do
    if [ ! -d "$SITE_DIR/$d" ]; then
        echo "⚠️  Falta directorio: $d (lo creará WordPress al instalarse)"
        mkdir -p "$SITE_DIR/$d"
    fi
done

# ── 5. Verificar conectividad BD ───────────────────
echo ""
echo "🔌 Probando conexión a base de datos..."
if command -v mysql >/dev/null 2>&1; then
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "USE \`$DB_NAME\`" 2>/dev/null; then
        echo "✅ Conexión BD OK"
    else
        echo "❌ No se pudo conectar a la BD. Verifica credenciales."
        exit 1
    fi
else
    echo "⚠️  Cliente mysql no disponible. Verifica manualmente en el panel."
fi

# ── 6. Mensaje final ─────────────────────────────────
cat <<EOF

✅ Bootstrap completado.

📋 Próximos pasos:

  1. Accede a tu sitio: $BF_SITE_URL/wp-admin/install.php
  2. Completa el wizard de instalación de WordPress
  3. Instala los plugins requeridos:
     - WooCommerce
     - Storefront (parent theme)
  4. Activa el tema 'Bicicletería Fagua'
  5. Sembra el catálogo (opcional):
     - Visita $BF_SITE_URL/?bf-seed=run (solo admins)
     - O desde WP-CLI:
       wp theme activate bicicleteria-fagua-theme
       wp plugin activate woocommerce

🔄 Deploys automáticos:

  - Push a main  → Deploy a producción
  - Push a develop → Deploy a staging
  - Manual → GitHub Actions → Run workflow → Selecciona entorno

📞 Soporte: fagua.bike@gmail.com

EOF
