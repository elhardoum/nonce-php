FROM composer:2.0.0-alpha1

WORKDIR /usr/src/app

RUN apk update && apk add git

RUN git clone https://github.com/elhardoum/nonce-php .

RUN composer install

WORKDIR /usr/src/app/tests

CMD ["sh", "-c", "sleep +inf"]
