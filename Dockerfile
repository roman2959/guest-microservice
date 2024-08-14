# Используем официальный PHP образ с установленным FPM
FROM php:8.1-fpm

# Устанавливаем необходимые зависимости и расширения PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем файлы приложения в контейнер
COPY . /var/www

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Устанавливаем зависимости проекта
RUN composer install --optimize-autoloader --no-dev

# Открываем порт 9000 для PHP-FPM
EXPOSE 9000

# Запускаем PHP-FPM сервер
CMD ["php-fpm"]
