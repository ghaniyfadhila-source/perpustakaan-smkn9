FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql gd \
    && a2enmod rewrite \
    && a2dismod mpm_event mpm_worker || true \
    && rm -rf /var/lib/apt/lists/*

ENV PORT=8080
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/storage \
    && mkdir -p /var/www/html/public/uploads /var/www/html/app/uploads \
               /var/www/html/storage/digital_works/covers /var/www/html/storage/digital_works/pdfs \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/app/uploads

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

CMD ["docker-entrypoint.sh"]
