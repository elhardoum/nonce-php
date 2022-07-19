FROM composer:2.3.9

WORKDIR /usr/src/app

RUN apk update && apk add git

RUN git clone https://github.com/elhardoum/nonce-php .

RUN composer install

WORKDIR /usr/src/app/tests

CMD ["../vendor/bin/phpspec", "run", "-v", "--format", "progress"]
