FROM lorisleiva/laravel-docker:7.4

RUN rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo -e "upload_max_filesize = 128M\npost_max_size = 256M" >> /usr/local/etc/php/conf.d/statamic.ini
RUN mkdir -p /composer/cache && chmod -R 777 /composer/cache

USER 1000:1000

EXPOSE 3000

ENV COMPOSER_MEMORY_LIMIT=-1

CMD php artisan serve --host=0.0.0.0 --port=3000
