FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Очистка кеша apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание директории приложения
WORKDIR /var/www

# Копирование файлов приложения
COPY . .

# Создание необходимых папок Laravel
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache

# Установка прав
RUN chown -R www-data:www-data /var/www && \
    chown -R www-data:www-data /var/www && \
    chown -R www-data:www-data storage && \
    chmod -R 775 bootstrap/cache &&\
    chmod -R 775 storage


# Порт PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
