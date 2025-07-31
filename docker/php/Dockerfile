# Multi-stage build para otimização
FROM php:8.3-fpm-alpine AS base

# Instalar dependências do sistema
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    icu-dev \
    postgresql-dev \
    mysql-client \
    supervisor \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        sockets

# Instalar Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configurar usuário não-root
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de configuração PHP
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Instalar healthcheck
RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

# Stage de desenvolvimento
FROM base AS development

# Instalar Xdebug para desenvolvimento
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurar Xdebug
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

USER www

# Stage de produção
FROM base AS production

# Copiar aplicação
COPY --chown=www:www . /var/www/html

# Instalar dependências do Composer (otimizado para produção)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# Otimizar Laravel para produção
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan event:cache

# Configurar permissões
RUN chown -R www:www /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www

EXPOSE 9000

CMD ["php-fpm"]