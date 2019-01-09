#!/bin/bash
service apache2 stop

rm /etc/php5/apache2/php.ini
cp /etc/php5/apache2/php.ini2 /etc/php5/apache2/php.ini
rm /etc/apache2/apache2.conf
cp /etc/apache2/apache2.conf2 /etc/apache2/apache2.conf

cd /var/www/html/tortuga
cp .htaccess_example .htaccess
chmod -R 777 /var/www/html/tortuga
service apache2 start
