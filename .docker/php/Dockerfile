FROM php:7.4.6-fpm

RUN apt-get update && apt-get upgrade -y && apt-get autoremove -y && apt-get clean -qqy

RUN pecl channel-update pecl.php.net

RUN apt-get install libpq-dev -qqy
RUN apt-get install libgeoip-dev -qqy
RUN apt-get install wget -qqy

RUN docker-php-ext-install bcmath pdo pdo_mysql


RUN apt-get install -y libzip-dev
RUN apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip

RUN apt-get install libpng-dev zlib1g-dev -qqy \
    && docker-php-ext-install gd

# Install recommended extensions for Symfony
RUN apt-get update && apt-get install -y \
        libicu-dev \
    && docker-php-ext-install \
        intl \
        opcache

RUN docker-php-ext-configure intl
RUN docker-php-ext-enable \
        intl \
        opcache

RUN apt-get update && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    apt-get install -y libssl-dev

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

# Permission fix
RUN usermod -u 1000 www-data

WORKDIR /code
ADD . /code
RUN chown -R www-data:www-data /code
