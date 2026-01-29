FROM php:8.2-apache

# Apache rewrite (safe)
RUN a2enmod rewrite

# Copy all files
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
