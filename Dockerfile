FROM php:8.1

ENV APP_DIR /app

RUN apt-get update \
  && apt-get install -y libzip-dev zip \
  && docker-php-ext-install zip \
  && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Adds the application code to the image
ADD . ${APP_DIR}

# Define current working directory.
WORKDIR ${APP_DIR}

RUN composer install

EXPOSE 8080
