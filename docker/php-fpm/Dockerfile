FROM php:7.4.6-fpm
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.4-mysql \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
    && apt-get -y install git wget libsodium-dev zip unzip libzip-dev \
    libpcre3-dev sqlite3 libsqlite3-dev \
     libjpeg-dev libpng-dev \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-install \
    sodium \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    zip \
    gd


#RUN pecl install xdebug \
#    && docker-php-ext-enable xdebug

ENV         COMPOSER_HOME=/var/composer
COPY        ./composer-install /tmp/composer-install
RUN         chmod +x /tmp/composer-install && \
            /tmp/composer-install && \
            rm /tmp/composer-install
RUN         mkdir -p /var/composer && \
            chown -R www-data:www-data /var/composer
VOLUME      /var/composer

RUN         mkdir -p /etc/msmtp
COPY        ./.msmtprc /etc/msmtp/.msmtprc
RUN         chown www-data:www-data /etc/msmtp/.msmtprc \
            && chmod 600 /etc/msmtp/.msmtprc
