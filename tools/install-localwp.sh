#!/usr/bin/env bash
###############################################################################
#  install-localwp.sh — Instala el tema en LocalWP para desarrollo
#
#  Uso (desde el root del repositorio):
#    ./tools/install-localwp.sh
###############################################################################

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME_NAME="bicicleteria-fagua-theme"
LOCALWP_BASE="$HOME/Local Sites/bicicleteria-fagua/app/public/wp-content/themes"

TARGET="$LOCALWP_BASE/$THEME_NAME"

echo "🚲 Instalando $THEME_NAME en LocalWP..."

# Validar que LocalWP existe
if [ ! -d "$HOME/Local Sites" ]; then
    echo "❌ No se encontró ~/Local Sites. ¿Está LocalWP instalado?"
    exit 1
fi

# Validar que el sitio existe
if [ ! -d "$(dirname "$LOCALWP_BASE")" ]; then
    echo "❌ No se encontró el sitio 'bicicleteria-fagua' en LocalWP."
    echo "   Créalo primero desde la app de LocalWP."
    exit 1
fi

# Crear el directorio del tema si no existe
mkdir -p "$TARGET"

# Sincronizar archivos (excluyendo los de desarrollo)
echo "📦 Sincronizando archivos..."
rsync -av --delete \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.github' \
    --exclude='tests' \
    --exclude='tools' \
    --exclude='playwright-report' \
    --exclude='test-results' \
    --exclude='*.log' \
    --exclude='.phpunit.result.cache' \
    --exclude='.phpcs.cache' \
    --exclude='.eslintcache' \
    --exclude='.stylelintcache' \
    --exclude='package-lock.json' \
    --exclude='composer.lock' \
    "$REPO_ROOT/" "$TARGET/"

echo ""
echo "✅ Tema instalado en: $TARGET"
echo ""
echo "📋 Próximos pasos:"
echo "  1. Abre LocalWP"
echo "  2. Activa el tema 'Bicicletería Fagua' en Apariencia → Temas"
echo "  3. Asegúrate de tener WooCommerce + Storefront (parent theme) activos"
echo ""
