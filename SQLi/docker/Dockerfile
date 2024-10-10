# ub1nu11 v 2024.1
FROM debian:latest
ENV DEBIAN_FRONTEND noninteractive
# RUN apt-get update && apt-get install -y --no-install-recommends apt-utils
MAINTAINER "@nu11secur1ty version 4.0"

# Ubuntu LAMP stack with Apache, MariaDB, PHP, and SSL
ENV DEBIAN_FRONTEND noninteractive

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Install Apache, SSL, PHP, and some PHP modules

RUN apt update && apt install -y apache2 \
 openssl \
 php \
 php-cli \
 php-apcu \
 less \
 vim \
 gcc

# Install MariaDB and set default root password

RUN echo 'mariadb-server mariadb-server/root_password  password mypassword' | debconf-set-selections
RUN echo 'mariadb-server mariadb-server/root_password_again password mypassword' | debconf-set-selections
RUN apt-get install mariadb-server -y

# Disable the default Apache site config
# Install your site's Apache configuration and activate SSL

# ADD your_apache.conf /etc/apache2/sites-available/
RUN a2dissite 000-default
RUN a2enmod ssl

# Remove APT files
RUN apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Update and packages
RUN apt update -y
RUN apt dist-upgrade -y
RUN apt install -y nmap \
vim \
net-tools 
RUN apt update -y
RUN apt dist-upgrade -y

EXPOSE 443 8080

# Clean Evironment
RUN rm -rf /var/www/html/*

# mgmt
# RUN mkdir -p /var/www/html/mgmt/
# ADD /env/mgmt /var/www/html/mgmt/
# copy folders
COPY /env/mgmt/. /var/www/html/

# users
COPY /sec/makesec.sh /

# Protect 
# RUN chmod +x ./makesec.sh
RUN touch /etc/apache2/secret
COPY /sec/protect /etc/apache2/secret
ADD .htaccess /var/www/html/
RUN rm /etc/apache2/apache2.conf
ADD apache2.conf /etc/apache2/apache2.conf

# Manual protect
COPY credocred.sh /credocred.sh

# Apache
COPY 000-default.conf /etc/apache2/sites-enabled/
COPY 000-default.conf /etc/apache2/sites-available/
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
