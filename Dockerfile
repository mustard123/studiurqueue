FROM php:7.2-apache AS dev
RUN a2enmod rewrite
WORKDIR /studiurQueue
RUN apt-get update && apt-get install --yes curl wget vim zip unzip libzmq3-dev libzmq5 git && pecl install zmq-1.1.3 && docker-php-ext-enable zmq.so && docker-php-ext-install pdo_mysql
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet && ./composer.phar --version
RUN wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.2/install.sh | bash && export NVM_DIR="$HOME/.nvm" &&  [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && nvm install 12.7.0
ENV PATH /root/.nvm/versions/node/v12.7.0/bin/:${PATH}

# install modules before copying src to optimize the use of dockers cache
COPY ./ng-studiur-queue/package*.json ./ng-studiur-queue/
WORKDIR /studiurQueue/ng-studiur-queue
RUN npm install
WORKDIR /studiurQueue/backend
COPY ./backend/composer.json ./backend/composer.lock ./
RUN ./../composer.phar install


# now copy source files and build
COPY ./ng-studiur-queue /studiurQueue/ng-studiur-queue
WORKDIR /studiurQueue/ng-studiur-queue
RUN npm run build -- --prod --output-hashing=none
WORKDIR /
COPY ./backend /studiurQueue/backend
RUN mkdir -p /var/www/html/backend
RUN cp -a /studiurQueue/ng-studiur-queue/dist/studiur-queue/. /var/www/html/  && cp -a /studiurQueue/backend/. /var/www/html/backend

COPY docker_multiprocess_start.sh /studiurQueue
ENTRYPOINT ["/studiurQueue/docker_multiprocess_start.sh"]

EXPOSE 7777
EXPOSE 80



FROM dev AS build
RUN npm run build -- --prod --output-hashing=none


FROM php:7.2-apache AS prod
RUN apt-get install libzmq3-dev libzmq5 && pecl install zmq-1.1.3 && docker-php-ext-enable zmq.so && docker-php-ext-install pdo_mysql
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"