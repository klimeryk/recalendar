FROM php:7.4-cli as recalendar
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . /usr/src/recalendar
WORKDIR /usr/src/recalendar
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install
RUN php ./generate.php

FROM scratch AS export-stage
COPY --from=recalendar /usr/src/recalendar/ReCalendar.pdf .
