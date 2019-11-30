# Use the newest php version 7.4 YEAH
FROM php:7.4-cli
# Update our image packages
RUN apt-get update
# Install git
RUN apt-get install -y git
# Install zip and unzip for composer
RUN apt-get install zip unzip
# Install Composer globally
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Install X-Debug
RUN pecl install xdebug-2.8.0 && docker-php-ext-enable xdebug
# Expose port 9000 for X-Debug
EXPOSE 9000
# Set the default work-dir
WORKDIR /opt/project
