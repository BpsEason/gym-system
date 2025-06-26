FROM php:8.2-fpm-alpine # 使用 PHP 8.2 FPM 搭配 Alpine Linux 基礎映像

# 安裝系統依賴和 PHP 擴展
# 注意：這些擴展是 Laravel 和此專案常見的依賴
RUN apk add --no-cache \
    nginx \
    mysql-client \
    git \
    curl \
    supervisor \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    libwebp-dev \
    imagemagick-dev \
    libxml2-dev \
    oniguruma-dev \
    libpq \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    gd \
    exif \
    pcntl \
    bcmath \
    sockets \
    opcache \
    intl \
    gmp \
    pdo_pgsql && \
    pecl install redis \
    xdebug && \
    docker-php-ext-enable redis \
    xdebug && \
    rm -rf /tmp/pear && \
    rm -rf /var/cache/apk/*

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 配置 PHP-FPM
COPY .docker/php/www.conf /etc/php82/php-fpm.d/www.conf
COPY .docker/php/php.ini /etc/php82/conf.d/custom-php.ini

# 設定工作目錄
WORKDIR /var/www/html

# 複製應用程式程式碼（這會在 docker-compose build 時由 volume 掛載，但為了構建映像，通常會在此處複製應用程式骨架）
# 在 docker-compose.yml 中使用 volume 掛載可以避免每次程式碼修改都重新構建映像
# COPY . /var/www/html

# 設定容器啟動時的命令
# 運行 PHP-FPM
CMD ["php-fpm"]
