#!/bin/sh
# Ajusta as permissões do projeto em hosting compartilhado (sem chown,
# apenas chmod). Rode a partir da raiz do projeto:
#   sh bin/fix-permissions.sh
#
# Valores lidos de .env-security (veja .env-security.example). Sem esse
# arquivo, usa os padrões abaixo.

set -eu

cd "$(dirname "$0")/.."

if [ -f .env-security ]; then
    # shellcheck disable=SC1091
    . ./.env-security
fi

DIR_MODE="${DIR_MODE:-755}"
FILE_MODE="${FILE_MODE:-644}"
WRITABLE_DIR_MODE="${WRITABLE_DIR_MODE:-775}"
EXECUTABLE_FILE_MODE="${EXECUTABLE_FILE_MODE:-755}"

echo "Ajustando permissões do projeto..."

echo "  Diretórios -> ${DIR_MODE}, arquivos -> ${FILE_MODE}"
find . \
    -path ./vendor -prune -o \
    -path ./node_modules -prune -o \
    -path ./.git -prune -o \
    -path ./storage -prune -o \
    -path ./bootstrap/cache -prune -o \
    -type d -exec chmod "${DIR_MODE}" {} + -o \
    -type f -exec chmod "${FILE_MODE}" {} +

echo "  storage/ e bootstrap/cache/ -> ${WRITABLE_DIR_MODE} (graváveis pelo PHP)"
find storage bootstrap/cache -type d -exec chmod "${WRITABLE_DIR_MODE}" {} +
find storage bootstrap/cache -type f -exec chmod "${FILE_MODE}" {} +

echo "  artisan -> ${EXECUTABLE_FILE_MODE} (executável)"
chmod "${EXECUTABLE_FILE_MODE}" artisan

echo "Permissões ajustadas."
