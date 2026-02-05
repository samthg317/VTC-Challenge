FROM alpine:3.18

RUN apk --no-cache add php82 php82-fpm php82-mysqli php82-json php82-openssl php82-curl \
    php82-zlib php82-xml php82-phar php82-intl php82-dom php82-xmlreader php82-ctype php82-session \
    php82-mbstring php82-gd php82-iconv php82-simplexml php82-sodium php82-pdo php82-pdo_mysql php82-tokenizer \
    php82-fileinfo php82-zip imagemagick php82-pecl-imagick php82-pecl-xdebug php82-xmlwriter \
    nginx supervisor curl bash vim

RUN ln -s /usr/bin/php82 /usr/bin/php

# Configure nginx
COPY config/xdebug.ini /etc/php7/conf.d/xdebug.ini
COPY config/nginix/template/nginx.template.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY config/php.ini /etc/php7/conf.d/custom.ini

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Setup document root
RUN mkdir -p /var/www/html

# Add application
WORKDIR /var/www/html

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["sh", "./docker/run.sh"]
