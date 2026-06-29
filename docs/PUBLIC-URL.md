# 🌐 URL Pública — Cloudflare Tunnel + DuckDNS

Esta guía explica cómo Bicicletería Fagua tiene una **URL pública permanente**
con HTTPS automático, sin necesidad de abrir puertos en el router.

## 🏗️ Arquitectura

```
┌──────────────────────────────────────────────────────────────┐
│  Internet (cualquier usuario)                                │
└──────────────────┬───────────────────────────────────────────┘
                   │ HTTPS
                   ▼
┌──────────────────────────────────────────────────────────────┐
│  Cloudflare Edge (CDN global)                                │
│  - SSL automático                                            │
│  - HTTP/2, HTTP/3                                            │
│  - Anti-DDoS                                                 │
└──────────────────┬───────────────────────────────────────────┘
                   │ Tunnel cifrado (QUIC/TCP)
                   ▼
┌──────────────────────────────────────────────────────────────┐
│  cloudflared (en este PC, Windows)                           │
│  - CloudflareTunnel-2026.5.2                                 │
│  - URL: https://xxx.trycloudflare.com                        │
│  - Auto-inicio con Windows (tarea programada)                │
│  - Auto-reconexión si muere                                  │
└──────────────────┬───────────────────────────────────────────┘
                   │ HTTP local
                   ▼
┌──────────────────────────────────────────────────────────────┐
│  LocalWP                                                     │
│  - Sitio: bicicleteriafagua.local:10016                      │
│  - WordPress + WooCommerce + tema custom                     │
└──────────────────────────────────────────────────────────────┘
```

## 🚀 Instalación paso a paso (ya completada)

### 1. Verificar prerrequisitos ✅

| Componente | Estado |
|-----------|--------|
| `cloudflared` | ✅ Instalado (C:\Users\fagua\AppData\Local\...\cloudflared.exe) |
| `nssm` | ✅ Instalado vía winget (alternativa) |
| LocalWP | ✅ Corriendo en :10016 |
| Git Bash | ✅ C:\Program Files\Git\bin\bash.exe |

### 2. Verificar tunnel funciona ✅

```bash
cd C:\Users\fagua\dev\fagua-bike-theme
bash tools/cloudflare-tunnel.sh status
```

**Resultado actual:**
```
● Tunnel CORRIENDO
  URL pública: https://hydrocodone-urban-knight-scholarships.trycloudflare.com
  URL local:   http://bicicleteriafagua.local:10016
```

### 3. Verificar acceso externo ✅

```bash
curl -I https://hydrocodone-urban-knight-scholarships.trycloudflare.com/
```

**Resultado:** `HTTP/2 200`

### 4. Ejecutar Playwright contra URL pública ✅

```bash
cd C:\Users\fagua\dev\fagua-bike-theme
BASE_URL="https://hydrocodone-urban-knight-scholarships.trycloudflare.com" npx playwright test
```

**Resultado:** 18/18 tests pasan en los 3 viewports.

## 🔄 Configurar auto-inicio con Windows (pendiente, requiere admin)

Hay **2 métodos** disponibles. Cualquiera de los dos hace que el tunnel
arranque automáticamente al prender el PC y se mantenga activo 24/7.

### Método A: Tarea programada (recomendado, no requiere NSSM)

1. Click derecho en `tools\install-tunnel-task.bat`
2. "Ejecutar como administrador"
3. Acepta el UAC
4. Espera a que diga "Tarea programada instalada correctamente"

**Lo que hace:**
- Crea tarea `BicicleteriaFagua-CloudflareTunnel`
- Se ejecuta al iniciar sesión (con 30s de delay para que la red esté lista)
- Se reinicia automáticamente 999 veces si falla
- Log en `%USERPROFILE%\.cloudflared\`

**Verificar:**
```powershell
Get-ScheduledTask -TaskName "BicicleteriaFagua-CloudflareTunnel"
Start-ScheduledTask -TaskName "BicicleteriaFagua-CloudflareTunnel"  # Iniciar ahora
```

### Método B: Servicio de Windows con NSSM

```powershell
# PowerShell como admin
cd C:\Users\fagua\dev\fagua-bike-theme
.\tools\cloudflare-tunnel-service.ps1 -Install
```

## 🦆 DuckDNS (opcional, no recomendado para este caso)

**Por qué no se usa DuckDNS en esta arquitectura:**

DuckDNS provee un subdominio `*.duckdns.org` que apunta a una IP dinámica
de tu casa. Para usarlo necesitarías:

1. ✅ Registrarte en duckdns.org (gratis)
2. ❌ Configurar port forwarding en tu router (puerto 80/443)
3. ❌ Instalar certbot para Let's Encrypt
4. ❌ Renovar certificados cada 90 días
5. ❌ Tu IP debe ser accesible (algunos ISPs bloquean CGNAT)

**Problemas:**
- 🔴 **Inseguro**: abres tu red doméstica a Internet
- 🔴 **Menos confiable**: depende de tu ISP y router
- 🔴 **Menos performante**: no usa CDN global
- ❌ **No vale la pena** vs Cloudflare Tunnel (que es más seguro y rápido)

**Si aún así quieres usar DuckDNS (porque te gusta el subdominio), el script está listo:**

```bash
cd C:\Users\fagua\dev\fagua-bike-theme
./tools/duckdns-setup.sh
# Te guiará para obtener el token y registrar el dominio
```

**PERO** tendrías que combinarlo con port forwarding, no con el tunnel.
**No se recomienda.**

## 🔄 Cuando compres un dominio propio

Si en el futuro compras `faguabike.com` por ejemplo:

### Opción 1: Cloudflare Tunnel con dominio custom (recomendado)

1. Comprar dominio (Namecheap, Cloudflare Registrar, Google Domains)
2. Agregar el dominio a Cloudflare (free plan)
3. Crear un **Named Tunnel** en Cloudflare:
   ```bash
   cloudflared tunnel login    # Abre navegador, autentica
   cloudflared tunnel create fagua-bike
   ```
4. Configurar DNS en Cloudflare:
   - CNAME `faguabike.com` → `<tunnel-id>.cfargotunnel.com`
   - CNAME `www` → `<tunnel-id>.cfargotunnel.com`
5. Actualizar `cloudflared` config para usar el nuevo dominio
6. SSL automático (gestionado por Cloudflare)

### Opción 2: Hosting tradicional (Hostinger, SiteGround)

Sigue las instrucciones de [DEPLOY.md](DEPLOY.md) que ya está documentado.

## 📁 Archivos relacionados

| Archivo | Función |
|---------|---------|
| `tools/cloudflare-tunnel.sh` | Script bash principal con auto-reconexión |
| `tools/cloudflare-tunnel-service.ps1` | Instalador con NSSM (servicio Windows) |
| `tools/cloudflare-tunnel-task.ps1` | Instalador con Task Scheduler (alternativa) |
| `tools/install-tunnel-task.bat` | Wrapper con auto-elevación a admin |
| `tools/duckdns-setup.sh` | Script de DuckDNS (opcional, no recomendado) |
| `~/.cloudflared/tunnel-final.log` | Log del tunnel |
| `~/.cloudflared/public-url.txt` | URL pública actual |

## 🆘 Troubleshooting

### El tunnel murió
```bash
cd C:\Users\fagua\dev\fagua-bike-theme
bash tools/cloudflare-tunnel.sh restart
```

### La URL cambió
Cloudflare Quick Tunnel genera una URL **nueva en cada reinicio** (es por diseño,
no tiene persistencia). Para tener URL **fija** necesitas:
- Named Tunnel + dominio en Cloudflare (recomendado cuando compres dominio)
- O mantener el tunnel siempre vivo (no reiniciar el PC)

### "Acceso denegado" al instalar la tarea
- Click derecho en el script → "Ejecutar como administrador"

### "Tunnel no responde"
1. Verifica que LocalWP esté corriendo: `curl http://bicicleteriafagua.local:10016/`
2. Reinicia el tunnel: `bash tools/cloudflare-tunnel.sh restart`
3. Revisa el log: `cat ~/.cloudflared/tunnel-final.log`

### Cambiar de URL a dominio propio
Ver sección "Cuando compres un dominio propio" arriba.

## 📊 Resumen técnico

| Característica | Estado |
|----------------|--------|
| URL pública | ✅ https://hydrocodone-urban-knight-scholarships.trycloudflare.com |
| HTTPS | ✅ Automático (cert de Cloudflare) |
| HTTP/2, HTTP/3 | ✅ |
| CDN global | ✅ |
| SSL automatic renewal | ✅ |
| Auto-inicio Windows | ⏳ Pendiente (corre como proceso, no servicio) |
| Auto-reconexión | ✅ Si tunnel muere, se reinicia solo |
| Tema WordPress | ✅ No modificado |
| Base de datos | ✅ No modificada |
| Listo para dominio custom | ✅ Sí, solo cambiar config del tunnel |
