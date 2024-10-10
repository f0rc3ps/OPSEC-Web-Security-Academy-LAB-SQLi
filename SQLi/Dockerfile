FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

#COPY ssl/server.key /etc/ssl/private/server.key
#COPY ssl/server.crt /etc/ssl/certs/server.pem

# Set the default user for the MySQL image
USER mysql

# Set environment variables for the new user
ENV MYSQL_USER=root
ENV MYSQL_PASSWORD="tarator"
ENV MYSQL_DATABASE="scrm_db"

# Run the following commands to create the new user and grant them the necessary permissions
RUN mysql -u root -p -e "CREATE USER '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD';"
RUN mysql -u root -p -e "GRANT ALL PRIVILEGES ON $MYSQL_DATABASE.* TO '$MYSQL_USER'@'%';"
RUN mysql -u root -p -e "FLUSH PRIVILEGES;"

# Expose port 3306 to allow connections to the database
EXPOSE 3306

# Start the MySQL server when the container is run
CMD ["mysqld"]

RUN mkdir -p /var/www/html/scrm/
ADD scrm /war/www/html/scrm/

RUN echo "ServerName localhost:80" >> /etc/apache2/apache2.conf
RUN sed -i 's/ssl-cert-snakeoil/server/' /etc/apache2/sites-available/default-ssl.conf
RUN a2enmod rewrite
RUN a2enmod headers
# RUN a2enmod ssl
# RUN a2ensite default-ssl

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
