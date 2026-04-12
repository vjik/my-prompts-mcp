#!/bin/sh
set -eu

# Box
curl -Lf -o /usr/local/bin/box https://github.com/box-project/box/releases/download/4.7.0/box.phar
chmod +x /usr/local/bin/box

# static-php-cli
mkdir -p /tmp/build-tools/static-php-cli
cd /tmp/build-tools/static-php-cli
git clone https://github.com/crazywhalecc/static-php-cli.git --depth=1 .
composer install
chmod +x bin/spc
ln -s /tmp/build-tools/static-php-cli/bin/spc /usr/local/bin/spc

# Build micro.sfx
mkdir /tmp/builder && cd /tmp/builder
spc download php-src,micro --with-php=8.5.4 --for-libs=zlib,libiconv,libxml2 --prefer-pre-built
spc doctor --auto-fix
spc build --build-micro 'phar'

# Build PHAR
cd "$GITHUB_WORKSPACE"
composer install --no-dev --no-plugins --optimize-autoloader
box compile

# Combine binary
spc micro:combine \
  --with-micro=/tmp/builder/buildroot/bin/micro.sfx \
  --output="$GITHUB_WORKSPACE/build/$BINARY_NAME" \
  "$GITHUB_WORKSPACE/build/my-prompts-mcp.phar"
