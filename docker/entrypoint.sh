#!/bin/bash
set -e

echo "==> APIDIAN Community Edition - Iniciando..."

# 1. Copiar .env.example si no existe .env
if [ ! -f ".env" ]; then
    echo "==> Creando .env desde .env.example"
    cp .env.example .env
fi

# 2. Generar APP_KEY SIEMPRE antes de todo (necesario para artisan)
echo "==> Verificando APP_KEY"
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "==> Generando APP_KEY"
    php artisan key:generate --force
fi

# 3. Instalar dependencias PHP
echo "==> Ejecutando composer install"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 4. Descomprimir storage.zip si no existe el esqueleto
if [ ! -d "storage/app/public" ]; then
    if [ -f "/tmp/storage.zip" ]; then
        echo "==> Descomprimiendo storage.zip desde /tmp"
        unzip -o /tmp/storage.zip -d .
        rm -f /tmp/storage.zip
    elif [ -f "storage.zip" ]; then
        echo "==> Descomprimiendo storage.zip"
        unzip -o storage.zip -d .
    else
        echo "==> ADVERTENCIA: storage.zip no encontrado, saltando"
    fi
fi

# 5. Permisos
echo "==> Estableciendo permisos"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 6. Storage link
echo "==> Creando storage:link"
php artisan storage:link --force 2>/dev/null || true

# 7. Ejecutar urn_on (namespace URN para firma XML)
if [ -f "urn_on.sh" ]; then
    echo "==> Ejecutando urn_on.sh"
    chmod +x urn_on.sh
    ./urn_on.sh
fi

# 8. Esperar a que MariaDB este lista
echo "==> Esperando conexion a MariaDB..."
MAX_RETRIES=30
RETRY_COUNT=0
until php -r "
    try {
        new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        echo 'connected';
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "==> ERROR: MariaDB no disponible despues de $MAX_RETRIES intentos"
        exit 1
    fi
    echo "    Intento $RETRY_COUNT/$MAX_RETRIES - esperando 3s..."
    sleep 3
done
echo "==> MariaDB conectada"

# 9. Migraciones
echo "==> Ejecutando migraciones"
php artisan migrate --force

# 10. Seeders
echo "==> Verificando seeders"
php artisan db:seed --force 2>/dev/null || true

# 11. Limpiar toda la cache primero
echo "==> Limpiando cache"
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# 12. Re-generar cache (orden correcto: config primero)
echo "==> Generando cache"
php artisan config:cache
php artisan route:cache 2>/dev/null || echo "    route:cache omitido (rutas duplicadas)"
php artisan view:cache 2>/dev/null || true

echo "==> APIDIAN listo. Iniciando servicios..."

# 13. Iniciar PHP-FPM en background
php-fpm -D

# 14. Iniciar Nginx en foreground
exec nginx -g 'daemon off;'
