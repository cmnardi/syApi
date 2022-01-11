FROM php:7.3.25-fpm-alpine

RUN apk --update --no-cache add git

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; -- bin/console doctrine:migrations:migrate ;  php-fpm 
EXPOSE 9000
