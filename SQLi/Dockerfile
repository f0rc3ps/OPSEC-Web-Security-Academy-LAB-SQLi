FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

COPY ssl/server.key /etc/ssl/private/server.key
COPY ssl/server.crt /etc/ssl/certs/server.pem

RUN echo "ServerName localhost:80" >> /etc/apache2/apache2.conf
RUN sed -i 's/ssl-cert-snakeoil/server/' /etc/apache2/sites-available/default-ssl.conf
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod ssl
RUN a2ensite default-ssl

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
