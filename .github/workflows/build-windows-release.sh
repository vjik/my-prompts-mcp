#!/bin/bash
set -eu

SPC_DIR="$RUNNER_TEMP/static-php-cli"
BUILDER_DIR="$RUNNER_TEMP/builder"
BOX="$RUNNER_TEMP/box"
SPC="$SPC_DIR/bin/spc"

# Box
curl -Lf -o "$BOX" https://github.com/box-project/box/releases/download/4.7.0/box.phar

# static-php-cli
mkdir -p "$SPC_DIR"
cd "$SPC_DIR"
git clone https://github.com/crazywhalecc/static-php-cli.git --depth=1 .
composer install

# Build micro.sfx
mkdir -p "$BUILDER_DIR"
cd "$BUILDER_DIR"
"$SPC" download php-src,micro --with-php=8.5.4 --for-libs=zlib --prefer-pre-built
"$SPC" doctor --auto-fix
"$SPC" build --build-micro 'phar'

# Build PHAR
cd "$GITHUB_WORKSPACE"
composer install --no-dev --optimize-autoloader
php "$BOX" compile

# Combine binary
"$SPC" micro:combine \
  --with-micro="$BUILDER_DIR/buildroot/bin/micro.sfx" \
  --output="$GITHUB_WORKSPACE/build/$BINARY_NAME" \
  "$GITHUB_WORKSPACE/build/my-prompts-mcp.phar"
