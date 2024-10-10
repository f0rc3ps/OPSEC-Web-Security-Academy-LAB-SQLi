# ub1nu11 v 2024.1
FROM debian:latest
ENV DEBIAN_FRONTEND noninteractive
# RUN apt-get update && apt-get install -y --no-install-recommends apt-utils
MAINTAINER "@nu11secur1ty version 4.0"

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
RUN apt-get install mariadb-server -y
RUN echo 'mariadb-server mariadb-server/root_password  password mypassword' | debconf-set-selections
RUN echo 'mariadb-server mariadb-server/root_password_again password mypassword' | debconf-set-selections

# Disable the default Apache site config
# Install your site's Apache configuration and activate SSL

# Update and packages
RUN apt update -y
RUN apt dist-upgrade -y
RUN apt install -y nmap \
vim \
net-tools 
RUN apt update -y
RUN apt dist-upgrade -y

# Clean Evironment
RUN rm -rf /var/www/html/*

# mgmt
RUN mkdir -p /var/www/html/scrm/
# copy folders
WORKDIR /var/www/html/scrm/
COPY php-scrm.zip /var/www/html/scrm/

# Start apache
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
