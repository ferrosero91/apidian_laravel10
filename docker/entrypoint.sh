#!/bin/bash
set -e

echo "==> APIDIAN Community Edition - Iniciando..."

# 1. Copiar .env.example si no existe .env
if [ ! -f ".env" ]; then
    echo "==> Creando .env desde .env.example"
    cp .env.example .env
fi

# 2. Instalar dependencias PHP (DEBE ir antes de cualquier artisan)
echo "==> Ejecutando composer install"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 3. Generar APP_KEY si no tiene formato base64
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "==> Generando APP_KEY"
    php artisan key:generate --force
    if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
        echo "    Fallback: generando key manualmente"
        KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
        sed -i.bak "s|^APP_KEY=.*|APP_KEY=$KEY|" .env 2>/dev/null || \
        echo "APP_KEY=$KEY" >> .env
        rm -f .env.bak
    fi
    echo "    APP_KEY: $(grep '^APP_KEY=' .env | head -c 30)..."
fi

# 4. Descomprimir storage.zip si no existe el esqueleto
if [ ! -d "storage/app/public" ]; then
    if [ -f "/tmp/storage.zip" ]; then
        echo "==> Descomprimiendo storage.zip desde /tmp"
        unzip -o /tmp/storage.zip -d .
        rm -f /tmp/storage.zip
    elif [ -f "storage.zip" ]; then
        echo "==> Descomprimiendo storage.zip"
        unzip -o storage.zip -d .
    fi
fi

# 5. Permisos
echo "==> Estableciendo permisos"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 6. Storage link
echo "==> Creando storage:link"
php artisan storage:link --force 2>/dev/null || true

# 7. Ejecutar urn_on
if [ -f "urn_on.sh" ]; then
    echo "==> Ejecutando urn_on.sh"
    chmod +x urn_on.sh && ./urn_on.sh
fi

# 8. Esperar MariaDB
echo "==> Esperando MariaDB..."
MAX_RETRIES=30
RETRY_COUNT=0
until php -r "
    try {
        new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        echo 'connected';
    } catch (Exception \$e) { exit(1); }
" 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    [ $RETRY_COUNT -ge $MAX_RETRIES ] && echo "==> ERROR: MariaDB no disponible" && exit 1
    sleep 3
done
echo "==> MariaDB conectada"

# 9. Migraciones
echo "==> Ejecutando migraciones"
php artisan migrate --force

# 10. Seeders
echo "==> Verificando seeders"
php artisan db:seed --force 2>/dev/null || true

# 11. Limpiar cache (NO regeneramos config:cache para evitar problemas con APP_KEY)
echo "==> Limpiando cache"
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# 12. Cache de vistas (NO config cache)
echo "==> Cache de vistas"
php artisan view:cache 2>/dev/null || true

echo "==> APIDIAN listo. Iniciando servicios..."

# 13. Iniciar PHP-FPM en background
php-fpm -D

# 14. Iniciar Nginx en foreground
exec nginx -g 'daemon off;'
