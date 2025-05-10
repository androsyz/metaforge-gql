FROM php:8.2-cli AS build

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install zip pcntl pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install zip pcntl pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=build /app /app

EXPOSE 5000

CMD ["php", "index.php", "start"]
