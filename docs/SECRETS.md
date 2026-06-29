# Secretos de GitHub Actions

Este repositorio usa **GitHub Actions Secrets** para almacenar credenciales
de despliegue. **NUNCA** subas credenciales al código.

## 🔐 Secretos requeridos

Configúralos en: **GitHub → tu repo → Settings → Secrets and variables → Actions → New repository secret**

### Deploy (FTP)

| Nombre del secreto | Descripción | Ejemplo |
|--------------------|-------------|---------|
| `FTP_SERVER` | Hostname del servidor FTP | `ftpupload.net` |
| `FTP_USERNAME` | Usuario FTP | `epiz_12345678` |
| `FTP_PASSWORD` | Password del FTP | (texto plano) |
| `FTP_SERVER_DIR` | Carpeta remota destino | `htdocs/wp-content/themes/bicicleteria-fagua-theme/` |

### URL de producción (opcional, para health check)

| Nombre del secreto | Descripción | Ejemplo |
|--------------------|-------------|---------|
| `PRODUCTION_URL` | URL pública del sitio en producción | `https://faguabike.infinityfreeapp.com` |

## 🌐 Variables de entorno (no secretas)

Configúralas en: **Settings → Variables → Actions → New variable**

| Nombre | Descripción | Ejemplo |
|--------|-------------|---------|
| `STAGING_URL` | URL de staging para health check | `https://staging.faguabike...` |

## 🌍 Configuración de Environments

Ve a **Settings → Environments** y crea:

### `staging`
- **Sin protección** (deploys automáticos en push a `develop`)
- URL: tu sitio de staging

### `production`
- **Required reviewers:** 1 (tú mismo)
- **Deployment branches:** solo `main`
- **Variables secretas de entorno (opcional):**
  - `FTP_SERVER` (puede ser distinto al de staging)
  - `FTP_USERNAME`
  - `FTP_PASSWORD`
  - `FTP_SERVER_DIR`
  - `PRODUCTION_URL`

> Los secrets a nivel de environment **sobrescriben** los del repositorio
> si tienen el mismo nombre. Útil para separar staging y producción.

## 🔄 Cómo rotar un secreto

1. Ve a tu hosting y genera un nuevo password
2. GitHub → Settings → Secrets → `FTP_PASSWORD` → **Update**
3. Ejecuta un deploy manual para probar
4. Si todo OK, listo

## ⚠️ Qué NUNCA hacer

- ❌ No commitear credenciales en el código
- ❌ No subir `.env` al repo
- ❌ No pegar passwords en issues de GitHub
- ❌ No compartir secrets por chat/email

## 📋 Checklist de setup

- [ ] Cuenta de hosting creada
- [ ] Credenciales FTP anotadas
- [ ] Base de datos MySQL creada
- [ ] WordPress instalado en el servidor
- [ ] WooCommerce + Storefront activados
- [ ] `wp-config.php` configurado en el servidor
- [ ] Secretos FTP agregados a GitHub
- [ ] Environment `production` creado
- [ ] Primer deploy de prueba ejecutado
- [ ] Health check pasó ✅

---

¿Necesitas ayuda? **fagua.bike@gmail.com**
