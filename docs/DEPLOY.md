# 🚀 Guía de despliegue

Esta guía explica cómo desplegar **Bicicletería Fagua** en un servidor PHP
(WordPress + WooCommerce) usando **GitHub Actions** para automatizar
los deploys.

## 🏗️ Arquitectura

```
┌─────────────────┐
│  Repositorio    │  ← SOLO el código del tema
│  GitHub         │
│  (main/develop) │
└────────┬────────┘
         │ git push
         ▼
┌─────────────────────────────────────────────┐
│  GitHub Actions                             │
│  ──────────────                             │
│  1. Lint (PHP/JS/CSS/PHPStan)               │
│  2. Deploy vía FTP (SamKirkland action)     │
│  3. Health check                            │
└────────┬────────────────────────────────────┘
         │ FTP
         ▼
┌─────────────────┐    ┌──────────────────┐
│  Servidor       │    │  MySQL           │
│  PHP 8.0+       │◄──►│  (remoto o local)│
│  - WP Core      │    └──────────────────┘
│  - Plugins      │
│  - Theme (este) │
│  - Uploads      │
└─────────────────┘
```

## ✅ Lo que se versiona vs lo que NO

| En el repo | En el servidor |
|------------|----------------|
| Código del tema (PHP, CSS, JS, imágenes) | WordPress core |
| `wp-config.php.template` | `wp-config.php` (generado) |
| `.github/workflows/deploy.yml` | Base de datos MySQL |
| Documentación y CI | Uploads, plugins, wp-admin, wp-includes |

## 🔧 Setup paso a paso (15 min)

### 1. Elige un hosting PHP

Recomendado: **InfinityFree** (gratis, sin tarjeta, FTP, MySQL, SSL).

Otros soportados: cualquier hosting con PHP 8.0+ y acceso FTP.

| Hosting | Costo | URL permanente | MySQL | FTP | SSL |
|---------|-------|----------------|-------|-----|-----|
| **InfinityFree** | Gratis | ✅ | ✅ | ✅ | ✅ |
| 000webhost | Gratis | ✅ | ✅ | ✅ | ✅ |
| AwardSpace | Gratis | ✅ | ✅ | ✅ | ✅ |
| Hostinger | ~$3/mes | ✅ dominio | ✅ | ✅ | ✅ |

### 2. Crear cuenta y obtener credenciales

1. Regístrate en [infinityfree.com](https://www.infinityfree.com/)
2. Crea una cuenta de hosting → obtienes subdominio `tunombre.infinityfreeapp.com`
3. En el panel de control:
   - **Cuentas FTP**: anota hostname, usuario y password
   - **Bases de datos MySQL**: crea una BD y anota host, nombre, usuario, password
4. Opcional: añade un dominio personalizado (`.com`, `.co`, etc.)

### 3. Instalar WordPress en el servidor

Tienes 3 opciones:

**A) Softaculous (1-click, recomendado)**
- En el panel de InfinityFree → Softaculous → WordPress
- Install: completa el wizard
- Anota el usuario admin y password

**B) Manual vía FTP**
- Descarga WordPress de [wordpress.org/download](https://wordpress.org/download/)
- Sube los archivos (excepto `wp-content/`) a `htdocs/` vía FTP
- Visita tu sitio y completa el wizard

**C) Aislado (avanzado)**
- Sube solo `wp-content/themes/bicicleteria-fagua-theme/`
- El resto de WP lo instalas manualmente

### 4. Configurar `wp-config.php`

En el servidor, crea `wp-config.php` (NO en el repo) basándote en
`wp-config.php.template` que está versionado.

**Forma rápida con SSH (si tienes acceso):**

```bash
cd htdocs/
export DB_NAME=fagua_bike
export DB_USER=tu_usuario
export DB_PASSWORD=tu_password
export DB_HOST=sql123.infinityfree.com
export BF_SITE_URL=https://tunombre.infinityfreeapp.com
export BF_ENV=production

# Copiar plantilla y rellenar
cp wp-content/themes/bicicleteria-fagua-theme/wp-config.php.template wp-config.php
chmod 600 wp-config.php
```

**Forma manual:**

1. Abre `wp-config.php.template` con un editor
2. Reemplaza los valores con tus credenciales reales
3. Genera las secret keys en [api.wordpress.org/secret-key/1.1/salt/](https://api.wordpress.org/secret-key/1.1/salt/)
4. Sube el archivo como `wp-config.php` vía FTP

### 5. Instalar WooCommerce + Storefront

En el panel WP (`tudominio/wp-admin`):

1. **Plugins → Añadir nuevo**
2. Instala y activa:
   - **WooCommerce** (plugin de e-commerce)
   - **Storefront** (parent theme de tu tema personalizado)
3. Completa el wizard de WooCommerce (país: Colombia, moneda: COP)

### 6. Activar el tema de Bicicletería Fagua

El tema llega automáticamente cuando configures el deploy de GitHub Actions.
Mientras tanto, puedes subirlo manualmente:

1. `Apariencia → Temas`
2. `Subir tema → Seleccionar archivo`
3. Sube un .zip con todo el contenido de este repositorio

### 7. Configurar secretos en GitHub

Ve a tu repo en GitHub → **Settings → Secrets and variables → Actions**

**Secretos requeridos** (en `New repository secret`):

| Nombre | Valor | Ejemplo |
|--------|-------|---------|
| `FTP_SERVER` | Hostname del FTP | `ftpupload.net` o `sql123.infinityfree.com` |
| `FTP_USERNAME` | Usuario FTP | `epiz_12345678` |
| `FTP_PASSWORD` | Password FTP | (el que te dio el panel) |
| `FTP_SERVER_DIR` | Carpeta destino | `htdocs/wp-content/themes/bicicleteria-fagua-theme/` |
| `PRODUCTION_URL` | URL pública del sitio | `https://tunombre.infinityfreeapp.com` |

**Variables (opcionales)** → **Settings → Variables → Actions → New variable**:

| Nombre | Valor | Uso |
|--------|-------|-----|
| `STAGING_URL` | `https://staging.tunombre...` | Health check de staging |

### 8. Activar environments en GitHub

Ve a **Settings → Environments**:

1. Crea `staging` (sin protección) — para deploys automáticos desde `develop`
2. Crea `production` (con required reviewers) — para deploys desde `main`

## 🔄 Flujo de deploy

```
git push origin develop  ──►  CI pasa  ──►  Deploy a staging (auto)
git push origin main     ──►  CI pasa  ──►  Deploy a production (auto)
                                          o manual desde GitHub UI
```

### Deploy manual

1. GitHub → tu repo → **Actions**
2. Selecciona "Deploy" en el sidebar izquierdo
3. Click **Run workflow**
4. Elige entorno: `staging` o `production`
5. Click **Run workflow**

## 🔍 Health check

Después de cada deploy a producción, GitHub Actions verifica que el
sitio responda HTTP 200. Si falla:

1. Revisa el log del workflow
2. Verifica que el FTP subió los archivos
3. Comprueba que el tema se activó en WP Admin

## 📦 Estructura del deploy

El workflow sube **solo el tema** a:
`htdocs/wp-content/themes/bicicleteria-fagua-theme/`

Excluye automáticamente:
- `node_modules/`, `vendor/`
- `.github/`, `tests/`
- Archivos de configuración de linter
- Lockfiles, logs, cache
- Documentación

## 🚨 Troubleshooting

### El deploy falla con "530 Login authentication failed"
- Verifica `FTP_USERNAME` y `FTP_PASSWORD`
- En InfinityFree el username suele tener prefijo `epiz_`

### El sitio muestra "TEMA ROTO" después del deploy
- Verifica que Storefront (parent theme) esté instalado
- Revisa la consola del navegador (F12) por errores PHP

### Cambios no se ven después del deploy
- El servidor puede tener caché. Prueba:
  - `Ctrl+Shift+R` (hard refresh)
  - Vaciar caché del plugin WP (si usas WP Super Cache, LiteSpeed Cache, etc.)
  - En el FTP, verifica que los archivos se actualizaron

### Playwright e2e falla
- Configura `BASE_URL` en los settings de GitHub → Variables
- O usa el workflow manual con la URL de producción

## 📞 Contacto

¿Problemas? Escríbeme a **fagua.bike@gmail.com** o abre un issue en GitHub.
