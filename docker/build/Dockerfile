FROM php:8.1-apache

RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y \
    curl zip zlib1g-dev locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip libpq-dev nodejs npm wget \
    apt-transport-https lsb-release ca-certificates \
    && docker-php-ext-configure  \
    intl \
    && docker-php-ext-install  \
    pdo pdo_mysql pdo_pgsql opcache intl zip calendar dom mbstring gd xsl \
    && apt-get autoremove -y \
    && a2enmod rewrite \
    && npm install -g n && n stable

RUN curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer

COPY ["000-symfony.conf", "/etc/apache2/sites-available/000-default.conf"]

# Boot
COPY ["install.sh", "/usr/local/bin/"]
RUN echo "\n### Shell utils ###\n" \
    && chmod +x /usr/local/bin/install.sh
CMD ["/usr/local/bin/install.sh"]


RUN npm install --global yarn
WORKDIR /var/www/html