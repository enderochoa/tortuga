FROM debian:jessie

RUN apt-get -y update
RUN apt-get -y install apache2 php5 libapache2-mod-php5 php5-mysqlnd php5-curl php5-gd php5-intl php-pear php5-imagick php5-imap php5-mcrypt php5-memcache php5-pspell php5-recode php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl
RUN apt-get -y install mc
RUN a2enmod rewrite

RUN mkdir -p /var/www/html/tortuga

WORKDIR /var/www/html/tortuga

COPY ./container/php.ini /etc/php5/apache2/php.ini2
COPY ./container/apache2.conf /etc/apache2/apache2.conf2
COPY . .

COPY ./init.sh /opt/docker/provision/entrypoint.d/30-init.sh

EXPOSE 80