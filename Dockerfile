FROM php:8.4-fpm AS builder

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
       curl \
       git \
       unzip \
       libzip-dev \
       libicu-dev \
       libonig-dev \
       libxml2-dev \
       libjpeg-dev \
       libpng-dev \
       libfreetype6-dev \
       libmagickwand-dev \
       libcurl4-openssl-dev \
       libpq-dev \
       pkg-config \
       autoconf \
       build-essential \
  && docker-php-ext-configure intl \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
       pdo_pgsql \
       pgsql \
       mbstring \
       xml \
       zip \
       curl \
       bcmath \
       intl \
       gd \
       pcntl \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer


FROM php:8.4-fpm

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
       libzip5 \
       libjpeg62-turbo \
       libpng16-16t64 \
       libfreetype6 \
       libicu76 \
       libpq5 \
  && rm -rf /var/lib/apt/lists/*

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /var/www/html
COPY . .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
