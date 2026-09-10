#!/bin/sh
# Prepara o Brandify após um deploy em hosting compartilhado (sem SSH
# root, sem Composer global garantido). Rode a partir da raiz do
# projeto:
#   sh bin/deploy-setup.sh
#
# O que faz, em ordem:
#   1. Localiza um PHP >= 8.4 (o composer.lock atual exige isso, mesmo
#      o composer.json pedindo ^8.3 — pacotes recentes do Symfony já
#      travaram em >=8.4.1). Aceita override via PHP_BIN.
#   2. Instala o Composer localmente (composer.phar) se não houver um
#      `composer` global disponível.
#   3. composer install --no-dev --optimize-autoloader
#   4. php artisan key:generate (só se APP_KEY estiver vazio no .env)
#   5. php artisan migrate --force
#   6. php artisan storage:link (só se o link ainda não existir)
#   7. Limpa e reconstrói os caches (config, route, view)

set -eu

cd "$(dirname "$0")/.."

MIN_PHP_VERSION="80400"

# --- 1. Localizar PHP ---

version_id() {
    "$1" -r 'echo PHP_VERSION_ID;' 2>/dev/null
}

find_php() {
    if [ -n "${PHP_BIN:-}" ]; then
        echo "$PHP_BIN"
        return 0
    fi

    if command -v php >/dev/null 2>&1; then
        v="$(version_id php)"
        if [ -n "$v" ] && [ "$v" -ge "$MIN_PHP_VERSION" ]; then
            echo "php"
            return 0
        fi
    fi

    # cPanel (EA4) costuma expor versões alternativas assim.
    for candidate in /opt/cpanel/ea-php*/root/usr/bin/php; do
        [ -x "$candidate" ] || continue
        v="$(version_id "$candidate")"
        if [ -n "$v" ] && [ "$v" -ge "$MIN_PHP_VERSION" ]; then
            echo "$candidate"
            return 0
        fi
    done

    return 1
}

PHP_BIN="$(find_php)" || {
    echo "Nenhum PHP >= 8.4 encontrado no PATH nem em /opt/cpanel/ea-php*." >&2
    echo "Descubra o caminho certo (ex: ls /opt/cpanel | grep php) e rode de novo com:" >&2
    echo "  PHP_BIN=/caminho/para/php sh bin/deploy-setup.sh" >&2
    exit 1
}

echo "Usando PHP: ${PHP_BIN} ($("$PHP_BIN" -r 'echo PHP_VERSION;'))"

# --- 2. Composer ---

if command -v composer >/dev/null 2>&1; then
    COMPOSER="composer"
elif [ -f composer.phar ]; then
    COMPOSER="${PHP_BIN} composer.phar"
else
    echo "Composer não encontrado. Baixando composer.phar..."
    "$PHP_BIN" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    "$PHP_BIN" composer-setup.php
    "$PHP_BIN" -r "unlink('composer-setup.php');"
    COMPOSER="${PHP_BIN} composer.phar"
fi

# --- 3. Dependências ---

echo "Instalando dependências (composer install --no-dev)..."
$COMPOSER install --no-dev --optimize-autoloader --no-interaction

# --- 4. APP_KEY ---

if [ -f .env ] && ! grep -q '^APP_KEY=.\+' .env; then
    echo "APP_KEY ausente. Gerando..."
    "$PHP_BIN" artisan key:generate --force
fi

# --- 5. Migrations ---

echo "Rodando migrations pendentes..."
"$PHP_BIN" artisan migrate --force

# --- 6. Storage link ---

if [ ! -e public/storage ]; then
    echo "Criando link public/storage -> storage/app/public..."
    "$PHP_BIN" artisan storage:link
else
    echo "public/storage já existe, nada a fazer."
fi

# --- 7. Caches ---

echo "Reconstruindo caches (config, route, view)..."
"$PHP_BIN" artisan config:clear
"$PHP_BIN" artisan route:clear
"$PHP_BIN" artisan view:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

echo "Deploy pronto."
