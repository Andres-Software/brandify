# syntax=docker/dockerfile:1
# brandify (Laravel 13 + Filament 4) — arm64/amd64.
# nginx + php-fpm no mesmo container, orquestrados pelo supervisord.
# O .env NÃO entra na imagem: vem por EnvironmentFile do quadlet (ver infra/).

# ---------- estágio 1: assets (vite + tailwind 4) ----------
FROM docker.io/node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ---------- estágio 2: dependências php ----------
# composer:2 acompanha o PHP mais recente (não fixa minor) — um composer.lock
# gerado em PHP 8.4 pode falhar o install quando a imagem avançar de versão
# (platform_check do Composer trava). Por isso instala o composer DENTRO da
# própria imagem runtime (PHP fixo), em vez de usar composer:2 como estágio.
FROM docker.io/php:8.4-fpm-alpine AS vendor
COPY --from=docker.io/library/composer:2 /usr/bin/composer /usr/bin/composer
# zip: composer:2 vem com a extensão pronta; a imagem php:*-fpm-alpine não —
# filament/actions (export de planilhas) exige em resolve-time e runtime.
RUN apk add --no-cache libzip-dev \
 && docker-php-ext-install zip
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --no-scripts --prefer-dist \
      --no-interaction --ignore-platform-req=ext-intl
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------- estágio 3: runtime ----------
FROM docker.io/php:8.4-fpm-alpine

# intl: obrigatória para o Filament; zip: filament/actions (export de planilhas)
RUN apk add --no-cache nginx supervisor libpq icu-libs libzip \
 && apk add --no-cache --virtual .build postgresql-dev icu-dev libzip-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo_pgsql pcntl opcache bcmath intl zip \
 && apk del .build

COPY docker/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

WORKDIR /var/www/html
COPY --from=vendor /app .
COPY --from=assets /app/public/build ./public/build

# publica os assets do Filament (css/js do painel) na imagem
RUN php artisan filament:assets

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 8080
# Healthcheck fica no quadlet (HealthCmd → /up)
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
