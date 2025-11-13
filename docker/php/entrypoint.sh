#!/usr/bin/env sh

set -e

if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --no-progress
fi

if [ ! -d "vendor/twbs/bootstrap" ]; then
    echo "Installing Bootstrap via Composer..."
    if ! composer require --no-interaction twbs/bootstrap:^5.3; then
        echo "WARNING: Unable to install twbs/bootstrap. Please install it manually." >&2
    fi
fi

if [ ! -d "vendor" ]; then
    echo "Composer directory missing after install. Aborting."
    exit 1
fi

if [ ! -f "settings.php" ] && [ -f "settings.php.dist" ]; then
    echo "Copying default settings.php"
    cp settings.php.dist settings.php
fi

if [ -d "vendor/twbs/bootstrap/dist" ]; then
    mkdir -p web/assets/css web/assets/js
    cp -f vendor/twbs/bootstrap/dist/css/bootstrap.min.css web/assets/css/bootstrap.min.css
    cp -f vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js web/assets/js/bootstrap.bundle.min.js
fi

if [ ! -f "web/assets/css/fontawesome.min.css" ] || [ ! -d "web/assets/font/fontawesome/webfonts" ]; then
    FA_VERSION=${FONT_AWESOME_VERSION:-6.5.2}
    ARCHIVE="fontawesome-free-${FA_VERSION}-web"
    URL="https://github.com/FortAwesome/Font-Awesome/releases/download/${FA_VERSION}/${ARCHIVE}.zip"
    echo "Fetching Font Awesome ${FA_VERSION}..."
    TMP_DIR=$(mktemp -d)
    if curl -fsSL -o "${TMP_DIR}/fa.zip" "${URL}"; then
        unzip -q "${TMP_DIR}/fa.zip" -d "${TMP_DIR}"
        mkdir -p web/assets/font
        rm -rf web/assets/font/fontawesome
        mkdir -p web/assets/font/fontawesome
        cp -R "${TMP_DIR}/${ARCHIVE}/css" web/assets/font/fontawesome/
        cp -R "${TMP_DIR}/${ARCHIVE}/webfonts" web/assets/font/fontawesome/
    else
        echo "WARNING: Unable to download Font Awesome from ${URL}" >&2
    fi
    rm -rf "${TMP_DIR}"
fi

exec apache2-foreground

