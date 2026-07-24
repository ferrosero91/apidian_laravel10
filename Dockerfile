FROM php:8.2-fpm

# No interactividad
ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependencias del sistema: Nginx, Poppler, utils
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    poppler-utils \
    zip \
    unzip \
    git \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        soap \
        opcache

# Instalar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node.js 18 (para compilar assets si es necesario)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar PHP: localtime para Colombia, extensiones
RUN ln -sf /usr/share/zoneinfo/America/Bogota /etc/localtime \
    && echo "date.timezone=America/Bogota" > /usr/local/etc/php/conf.d/timezone.ini \
    && echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/upload.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/upload.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/upload.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/upload.ini \
    && echo "max_input_vars=5000" >> /usr/local/etc/php/conf.d/upload.ini

# Deshabilitar www-data pool por defecto y configurar uno nuevo
RUN rm -f /usr/local/etc/php-fpm.d/www.conf \
    && echo "[www]" > /usr/local/etc/php-fpm.d/www.conf \
    && echo "user = www-data" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "group = www-data" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_children = 25" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.start_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.min_spare_servers = 3" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_spare_servers = 10" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/www.conf

# Instalar dependencias Node.js y compilar assets
RUN npm install --legacy-peer-deps && npm run production

# Limpiar node_modules después del build para reducir tamaño
RUN rm -rf node_modules

# Directorios necesarios
RUN mkdir -p /var/log/nginx /run/nginx

# Copiar configuracion Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copiar entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html

# Copiar archivos de la aplicacion
COPY --chown=www-data:www-data . /var/www/html/

# Permisos iniciales
RUN chmod -R 775 storage bootstrap/cache 2>/dev/null || true \
    && chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Copiar storage.zip a /tmp para que sobreviva al volumen mount
RUN cp storage.zip /tmp/storage.zip 2>/dev/null || true

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
