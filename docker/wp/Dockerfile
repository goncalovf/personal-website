FROM wordpress:7.1.2-php8.2-apache

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Development tooling dependencies
RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		bash less default-mysql-client git zip unzip \
		curl pv \
		msmtp libz-dev libmemcached-dev \
	&& rm -rf /var/lib/apt/lists/* \
    && pecl install xdebug memcache \
    && docker-php-ext-enable xdebug memcache;

# Include our custom config for PHP and Xdebug.
COPY php.ini /usr/local/etc/php/conf.d/

# Install Composer dependencies and add vendor/bin to the path.
COPY composer.json .
RUN composer install
ENV PATH /var/www/html/vendor/bin:$PATH

WORKDIR /var/www/html/

# MySQL options file for WP CLI commands.
COPY --chown=www-data:www-data --chmod=600 .my.cnf /var/www/.my.cnf

# Register a directory to be used as a volume.
RUN mkdir -p /volume.d
VOLUME /volume.d
