FROM php:7.4-apache

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        && docker-php-ext-install zip pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

RUN chown -R www-data:www-data /var/www/html

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN a2enmod proxy
RUN a2enmod proxy_http

EXPOSE 80

CMD ["apache2-foreground"]