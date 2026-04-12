#!/bin/sh
set -eu

# Dependencies
apk add --no-cache git linux-headers curl

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Box
curl -Lf -o /usr/local/bin/box https://github.com/box-project/box/releases/download/4.7.0/box.phar
chmod +x /usr/local/bin/box

# static-php-cli
mkdir -p /build-tools/static-php-cli
cd /build-tools/static-php-cli
git clone https://github.com/crazywhalecc/static-php-cli.git --depth=1 .
composer install
chmod +x bin/spc
ln -s /build-tools/static-php-cli/bin/spc /usr/local/bin/spc

# Build micro.sfx
mkdir /builder && cd /builder
spc download php-src,micro --with-php=8.5.4 --for-libs=zlib --prefer-pre-built
spc doctor --auto-fix
spc build --build-micro 'phar'

# Build PHAR
cd /app && box compile

# Combine binary
spc micro:combine \
  --with-micro=/builder/buildroot/bin/micro.sfx \
  --output=/app/build/$BINARY_NAME \
  /app/build/my-prompts-mcp.phar
