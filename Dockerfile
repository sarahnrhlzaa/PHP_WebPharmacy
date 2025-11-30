# # Menggunakan PHP 8.2 dengan Apache
# FROM php:8.2-apache
# # Install MySQL extensions yang dibutuhkan
# RUN docker-php-ext-install mysqli pdo pdo_mysql
# # Enable Apache mod_rewrite (untuk URL rewriting)
# RUN a2enmod rewrite
# # Set working directory
# WORKDIR /var/www/html
# # Set proper permissions
# RUN chown -R www-data:www-data /var/www/html \
# && chmod -R 755 /var/www/html
# # Expose port 80
# EXPOSE 80
# # Command untuk menjalankan nginx
# CMD ["nginx", "-g", "daemon off;"]


# PHP-FPM untuk Nginx
FROM php:8.2-fpm
# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql
# Set working directory
WORKDIR /var/www/myphpapp
# Set proper permissions
RUN chown -R www-data:www-data /var/www/myphpapp \
    && chmod -R 755 /var/www/myphpapp

CMD ["php-fpm"]