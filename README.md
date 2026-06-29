# 🚲 Bicicletería Fagua — WordPress Theme

Tema personalizado de WordPress para **Bicicletería Fagua** (La Macarena, Meta, Colombia).
Estética premium minimalista sobre **Storefront** + **WooCommerce**.

> **Child theme** de Storefront. No es un sitio estático. Es WordPress + WooCommerce + PHP,
> versionado con **Git**, desplegado con **GitHub Actions** y preparado para correr en
> cualquier servidor PHP 8.0+.

---

## 🏪 Sobre la tienda

Bicicletería Fagua es una tienda real de ciclismo ubicada en La Macarena, Meta, Colombia.
Vende bicicletas, componentes, ruedas, accesorios, indumentaria y ofrece servicio técnico
especializado.

- **WhatsApp:** +57 322 365 2738
- **Email:** fagua.bike@gmail.com
- **Dirección:** Calle 6 Cra. 5 - 8, La Macarena, Meta
- **Desarrollador:** Jorman Fagua ([@JORMS-FA](https://github.com/JORMS-FA))

---

## 📦 Stack

| Capa | Tecnología |
|------|------------|
| CMS | WordPress 6.0+ |
| E-commerce | WooCommerce 8.0+ |
| Parent theme | Storefront |
| Lenguaje | PHP 8.0+ |
| Frontend | HTML5, CSS3 (custom), JavaScript vanilla |
| Versionado | Git + GitHub |
| CI/CD | GitHub Actions (PHP, CSS, JS, Playwright) |

---

## 📁 Estructura del repositorio

```
fagua-bike-theme/
├── .github/
│   └── workflows/         # GitHub Actions: CI + tests visuales
├── assets/
│   ├── branding/          # SVGs del logo FAGUA
│   ├── css/               # theme.css, components.css, shop.css
│   ├── favicon/           # favicon.svg, apple-touch-icon.svg
│   ├── icons/             # iconos SVG por categoría
│   ├── images/            # JPG + WebP responsive
│   └── js/                # theme.js
├── inc/                   # Helpers, setup, AJAX, WooCommerce hooks
│   └── catalog/           # JSON con productos sembrados
├── template-parts/        # Secciones de la home + header/footer
│   ├── components/        # search, drawer, toast
│   ├── section-*.php      # 11 secciones de la home
│   ├── header.php
│   └── footer.php
├── woocommerce/           # Overrides de templates WC
├── tests/                 # Playwright e2e + PHPUnit
├── archive-product.php    # Archivo de productos (catálogo)
├── front-page.php         # Home
├── functions.php          # Bootstrap del tema
├── header.php             # Header
├── footer.php             # Footer
├── index.php              # Fallback
├── single-product.php     # Producto individual
├── style.css              # Metadata WP
├── package.json           # Dependencias Node
├── composer.json          # Dependencias PHP
├── .gitignore             # Exclusiones profesionales
├── .gitattributes         # Reglas de eol/binary
├── .editorconfig          # Configuración de editor
├── .eslintrc.json         # Lint JS
├── .stylelintrc.json      # Lint CSS
├── phpcs.xml.dist         # Lint PHP (WordPress standard)
├── phpstan.neon.dist      # Static analysis PHP
├── phpunit.xml.dist       # Tests PHP
├── CHANGELOG.md
├── LICENSE                # GPL-2.0-or-later
├── README.md
└── VERSION                # Espejo del header style.css
```

---

## 🛠️ Instalación local (LocalWP)

```bash
# 1. Clonar el repositorio
git clone https://github.com/JORMS-FA/fagua-bike-theme.git
cd fagua-bike-theme

# 2. Copiar el tema a LocalWP
cp -r . "/c/Users/fagua/Local Sites/bicicleteria-fagua/app/public/wp-content/themes/bicicleteria-fagua-theme/"

# 3. Activar desde WP Admin → Apariencia → Temas
# 4. Activar WooCommerce y sembrar el catálogo
```

### Sembrar el catálogo (opcional, solo una vez)

Acceder a: `http://bicicleteriafagua.local:10016/?bf-seed=run`

---

## 🧪 Tests

### Linters (estáticos)

```bash
# PHP
composer install
composer phpcs
composer phpstan

# JavaScript
npm install
npm run lint:js

# CSS
npm run lint:css
```

### Tests E2E (Playwright)

```bash
npm install
npx playwright install --with-deps
npm run test:e2e
```

---

## 🚀 CI/CD (GitHub Actions)

Cada push y pull request ejecuta automáticamente:

| Job | Verifica |
|-----|----------|
| `php-lint` | Estilo PHP (WordPress Coding Standards) |
| `phpstan`  | Análisis estático PHP |
| `css-lint` | Estilo CSS (Stylelint + Standard) |
| `js-lint`  | Estilo JavaScript (ESLint) |
| `phpunit`  | Tests unitarios PHP |
| `playwright` | Tests visuales E2E en 3 viewports |

**Estado actual:** Ver [Actions](https://github.com/JORMS-FA/fagua-bike-theme/actions)

---

## 📦 Despliegue

El repositorio **no se despliega automáticamente** a un servidor. Cuando llegue el momento:

1. **Servidor PHP** (Hostinger, SiteGround, DigitalOcean, etc.)
2. **Pipeline manual** vía GitHub Actions: `workflow_dispatch`
3. **Método:** `rsync` o `scp` al servidor

```yaml
# Ejemplo de job de deploy (a personalizar)
- name: Deploy to production
  uses: burnett01/rsync-deployments@7.0.1
  with:
    switches: "-avz --delete --exclude='.git'"
    path: "./"
    remote_path: "/var/www/fagua.bike/public_html/wp-content/themes/bicicleteria-fagua-theme/"
    remote_host: ${{ secrets.FTP_HOST }}
    remote_user: ${{ secrets.FTP_USER }}
    remote_key: ${{ secrets.SSH_KEY }}
```

---

## 🔒 Lo que NO se versiona

| Elemento | Por qué |
|----------|---------|
| `wp-config.php` | Contiene credenciales de BD y salts |
| `wp-content/plugins/` | Se gestionan por composer o panel WP |
| `wp-content/uploads/` | Generado por WP, se respalda aparte |
| Base de datos | Migraciones con `wp db export/import` |
| `node_modules/`, `vendor/` | Dependencias regenerables |
| Archivos locales de LocalWP | Específicos del entorno |

---

## 🤝 Contribuir

1. Fork el repositorio
2. Crear rama: `git checkout -b feature/nueva-seccion`
3. Commit: `git commit -m "feat: agregar sección X"`
4. Push: `git push origin feature/nueva-seccion`
5. Abrir Pull Request

**Conventional Commits** preferido:
- `feat:` nueva funcionalidad
- `fix:` corrección
- `style:` formato (no afecta lógica)
- `refactor:` refactorización
- `docs:` documentación
- `test:` tests
- `chore:` tareas administrativas

---

## 📄 Licencia

GPL-2.0-or-later © 2024-2026 Bicicletería Fagua

Este tema es software libre: puedes redistribuirlo y/o modificarlo bajo los términos
de la GNU General Public License publicada por la Free Software Foundation, ya sea la
versión 2 de la Licencia o (a tu elección) cualquier versión posterior.

Ver [LICENSE](LICENSE) para más detalles.
