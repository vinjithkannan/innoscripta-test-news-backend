# FROM php:8.0-alpine3.13
# Copy composer-7.2.lock and composer.json
# COPY composer-7.2.lock composer.json /var/www/
FROM php:8.3.0-fpm-alpine3.18
# Set working directory
WORKDIR /var/www

# Install dev dependencies
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    curl-dev \
    imagemagick-dev \
    libtool \
    libxml2-dev \
    postgresql-dev \
    sqlite-dev

# Install production dependencies
RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    g++ \
    gcc \
    git \
    icu-dev \
    icu-libs \
    imagemagick \
    libc-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    make \
    mysql-client \
    nodejs \
    npm \
    oniguruma-dev \
    yarn \
    openssh-client \
    postgresql-libs \
    rsync \
    zlib-dev

RUN apk add --update linux-headers

# Install PECL and PEAR extensions
RUN pecl install \
    redis \
    imagick \
    xdebug

# Enable PECL and PEAR extensions
RUN docker-php-ext-enable \
    redis \
    imagick \
    xdebug

# Configure php extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install php extensions
RUN docker-php-ext-install \
    bcmath \
    calendar \
    curl \
    exif \
    gd \
    intl \
    mbstring \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    pcntl \
    soap \
    xml \
    zip

# Install composer
ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# Install PHP_CodeSniffer
RUN composer global require "squizlabs/php_codesniffer=*"


# Cleanup dev dependencies
RUN apk del -f .build-deps

# Add user for laravel application
# RUN groupadd -g 1000 www
# RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

ADD ./php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy existing application directory permissions
# COPY --chown=www:www . /var/www

# Change current user to www
# USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000 80 443 22
 # 80 443
CMD ["php-fpm"]
# , "nginx", "-g", "daemon off;"