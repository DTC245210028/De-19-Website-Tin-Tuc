FROM php:8.3-fpm-alpine
RUN docker-php-ext-install pdo pdo_mysql \
 && addgroup -S appgroup && adduser -S appuser -G appgroup
WORKDIR /var/www/html
COPY --chown=appuser:appgroup . /var/www/html
USER appuser
EXPOSE 9000
