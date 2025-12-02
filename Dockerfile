# Use the newest php version
FROM php:8.5-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug @composer
COPY ./config/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini
# Set the default work-dir
WORKDIR /opt/project
