FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Allow .htaccess to override
RUN echo "<Directory /var/www/html/> \
    AllowOverride All \
</Directory>" >> /etc/apache2/apache2.conf

COPY . /var/www/html/

EXPOSE 80