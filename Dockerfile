FROM php:8.5.4-cli-alpine3.22

# Dependencies
RUN apk add git linux-headers

# PHP extensions
RUN docker-php-ext-install sockets

# Composer
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

# Box
RUN curl -Lf -o /usr/local/bin/box "https://github.com/box-project/box/releases/download/4.7.0/box.phar" \
    && chmod +x /usr/local/bin/box

# static-php-cli
RUN mkdir -p /build-tools/static-php-cli \
    && cd /build-tools/static-php-cli \
    && git clone https://github.com/crazywhalecc/static-php-cli.git --depth=1 . \
    && composer install \
    && chmod +x bin/spc \
    && ln -s /build-tools/static-php-cli/bin/spc /usr/local/bin/spc

# Prepare static-php-cli
RUN mkdir /builder \
    && cd /builder \
    && spc download \
      php-src,micro \
      --with-php=8.5.4 \
      --for-libs=zlib \
      --prefer-pre-built \
    && spc doctor --auto-fix \
    && spc build --build-micro "phar"

WORKDIR /app
