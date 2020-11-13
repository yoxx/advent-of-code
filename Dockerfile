# Use the newest php version 8.0-dev YEAH
FROM keinos/php8-jit
# Install as ROOT
USER root
# Update our image packages
RUN apk update
# Install git
RUN apk add git
# Install zip and unzip for composer
RUN apk add zip unzip
# Get PHPIZE DEPS for runnning PECL on Alpine images
RUN apk add --no-cache $PHPIZE_DEPS
# Install Composer globally
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Set the default work-dir
WORKDIR /opt/project
# Install X-Debug
RUN pecl install xdebug-3.0.0beta1 && docker-php-ext-enable xdebug
# Revert back to nobody
USER nobody
# Expose port 9000 for X-Debug
EXPOSE 9000
# Set the default work-dir
WORKDIR /opt/project
