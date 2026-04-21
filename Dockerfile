#hadolint global ignore=DL3059
FROM php:8.5.4-cli-alpine3.22

# Dependencies
# hadolint ignore=DL3018
RUN apk add --no-cache git linux-headers

# PHP extensions
RUN docker-php-ext-install sockets

# Composer
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

# Box
RUN curl -Lf -o /usr/local/bin/box "https://github.com/box-project/box/releases/download/4.7.0/box.phar" \
    && chmod +x /usr/local/bin/box

# static-php-cli
# hadolint ignore=DL3003
RUN mkdir -p /build-tools/static-php-cli \
    && cd /build-tools/static-php-cli \
    && git clone https://github.com/crazywhalecc/static-php-cli.git --depth=1 . \
    && composer install --no-dev \
    && chmod +x bin/spc \
    && ln -s /build-tools/static-php-cli/bin/spc /usr/local/bin/spc

# Prepare static-php-cli
# hadolint ignore=DL3003
RUN mkdir /builder \
    && cd /builder \
    && spc download \
      php-src,micro \
      --with-php=8.5.4 \
      --for-libs=zlib \
      --prefer-pre-built \
    && spc doctor --auto-fix \
    && spc build --build-micro "phar"

# mcpunit
RUN curl -Lo /tmp/mcpunit.tar.gz https://github.com/lee-to/mcpunit/releases/download/v1.3.0/mcpunit-x86_64-unknown-linux-musl.tar.gz \
    && tar -xzf /tmp/mcpunit.tar.gz -C /usr/local/bin mcpunit

WORKDIR /app
