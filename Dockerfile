FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/storage \
    && mkdir -p /var/www/html/public/uploads /var/www/html/app/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/app/uploads

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
