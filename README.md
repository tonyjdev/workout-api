# Workout API

API de Laravel 12 para gestionar rutinas y metricas de entrenamiento. Este documento explica como levantar el entorno local y como desplegar la aplicacion en un servidor Linux.

## Tecnologias clave
- Laravel 12 + PHP 8.3
- Sanctum para autenticacion basada en tokens
- Pest para pruebas
- Vite + Tailwind para activos front-end

## Requisitos previos
- PHP 8.3 con las extensiones `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`
- Composer 2.6+
- Node.js 20 LTS y npm 10 (Vite 7 requiere caracteristicas de Node 20)
- Base de datos: MySQL 8/Percona/MariaDB 10.5+, PostgreSQL 13+ o SQLite (archivo `database/database.sqlite`)
- Git y acceso al repositorio
- Opcional: Redis para colas/cache (configurable via `.env`)

## Configuracion local (desarrollo)

1. **Clonar el repositorio**
   ```bash
   git clone git@github.com:tu-organizacion/workout-api.git
   cd workout-api
   ```

2. **Variables de entorno**
   ```bash
   cp .env.example .env          # Linux/macOS
   copy .env.example .env        # Windows PowerShell
   ```
   Completa los valores de `APP_URL`, credenciales de base de datos y el `QUEUE_CONNECTION`. Para usar SQLite basta con habilitar `DB_CONNECTION=sqlite` y asegurarte de que exista `database/database.sqlite`.

3. **Carpetas y permisos de `storage`**
   Laravel necesita que `storage/` y `bootstrap/cache/` existan y sean escribibles:
   ```bash
   mkdir -p storage/app storage/framework/{cache,data,sessions,testing,views} storage/logs bootstrap/cache
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache   # o asigna permisos equivalentes en Windows
   ```

4. **Instalar dependencias y preparar la base de datos**
   La forma mas rapida es usar el script incluido:
   ```bash
   composer setup
   ```
   El comando instala dependencias PHP, crea `.env` si falta, genera la `APP_KEY`, ejecuta migraciones, instala dependencias Node y hace un `npm run build`.

   Si prefieres los pasos manuales:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   npm install
   npm run build
   ```

5. **Levantar el entorno de desarrollo**
   ```bash
   composer dev
   ```
   Ejecuta simultaneamente `php artisan serve`, `php artisan queue:listen --tries=1` y `npm run dev` con recarga en caliente. Si necesitas correrlos por separado:
   ```bash
   php artisan serve
   php artisan queue:listen --tries=1
   npm run dev
   ```

6. **Pruebas y calidad**
   ```bash
   php artisan test
   php artisan test --filter WorkoutFeature  # ejecutar pruebas especificas
   ./vendor/bin/pint                         # formateo PSR-12
   ```

7. **Re-seed rapido**
   ```bash
   php artisan migrate:fresh --seed
   ```

## Variables de entorno destacadas

| Variable | Descripcion |
|----------|-------------|
| `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL` | Nombre, entorno, modo debug y URL publica |
| `APP_KEY` | Generada con `php artisan key:generate`; necesaria para cifrado |
| `DB_*` | Credenciales y host de la base de datos |
| `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN` | Requerido si se consume desde un frontend diferente |
| `QUEUE_CONNECTION`, `REDIS_*` | Configuracion de colas/eventos |
| `FILESYSTEM_DISK`, `AWS_*` | Destinos para subir archivos si se usan discos externos |

## Scripts utiles
- `composer setup`: bootstrap completo (deps PHP, `.env`, key, migraciones, deps Node, build)
- `composer dev`: servidor HTTP + worker de colas + Vite watcher
- `npm run dev`: recarga en caliente para activos front
- `npm run build`: compilar activos para produccion
- `php artisan test --parallel`: ejecutar pruebas en paralelo cuando el entorno lo permita

## Guia de despliegue (Linux con PHP-FPM/Nginx o Apache)

1. **Preparar el servidor**
   - PHP 8.3 con las extensiones listadas arriba, PHP-FPM configurado y `proc_open` habilitado
   - Composer 2 y Node.js 20 para compilar activos
   - Servidor web apuntando a `public/`
   - Base de datos accesible y credenciales ya creadas

2. **Obtener codigo y dependencias**
   ```bash
   git pull --ff-only
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

3. **Variables de entorno y llaves**
   - Copia `.env.example` a `.env` (solo la primera vez) y configura valores productivos.
   - Genera la clave si aun no existe: `php artisan key:generate --force`.
   - Nunca subas `.env` al repositorio.

4. **Preparar `storage` y enlaces simbolicos**
   ```bash
   php artisan storage:link
   mkdir -p storage/app storage/framework/{cache,data,sessions,testing,views} storage/logs bootstrap/cache
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

5. **Migraciones y seeding**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force      # solo si necesitas datos base en produccion
   ```

6. **Optimizar la aplicacion**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan event:cache
   php artisan view:cache
   php artisan optimize
   ```

7. **Servicios en background**
   - **Colas**: configura Supervisor, systemd o PM2 para ejecutar `php artisan queue:work --tries=1 --max-time=3600`.
     ```ini
     [program:workout-api-queue]
     command=php /var/www/workout-api/artisan queue:work --tries=1 --timeout=60
     directory=/var/www/workout-api
     autostart=true
     autorestart=true
     user=www-data
     stdout_logfile=/var/log/supervisor/workout-api-queue.log
     stderr_logfile=/var/log/supervisor/workout-api-queue.log
     ```
   - **Scheduler**: agrega al cron del sistema:
     ```
     * * * * * php /var/www/workout-api/artisan schedule:run >> /dev/null 2>&1
     ```

8. **Verificaciones posteriores al despliegue**
   - Comprueba que el worker de colas esta activo (`php artisan queue:listen` muestra jobs nuevos).
   - Ejecuta `php artisan test --env=testing` si tu pipeline lo requiere.
   - Revisa logs en `storage/logs/laravel.log`.

## Resolucion de problemas
- Error de permisos: asegurate de que el usuario del servidor web tenga escritura en `storage/` y `bootstrap/cache/`.
- Assets desactualizados: vuelve a ejecutar `npm run build` y limpia el cache del navegador.
- `.env` perdido: recuperalo del administrador del entorno; nunca se versiona.

## Recursos adicionales
- Documentacion oficial de Laravel: https://laravel.com/docs/12.x
- Pest: https://pestphp.com/docs
- Vite: https://vitejs.dev/guide/
