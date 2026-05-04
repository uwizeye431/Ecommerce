# ─────────────────────────────────────────────────────────────
# Stage: production image for ShopEasy (PHP 8.2 + Apache)
# ─────────────────────────────────────────────────────────────

# Base image: official PHP with Apache built in
FROM php:8.2-apache

# ── System dependencies ───────────────────────────────────────
# Install the PDO MySQL driver so PHP can talk to MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (needed for clean URLs via .htaccess)
RUN a2enmod rewrite

# ── Apache configuration ──────────────────────────────────────
# Allow .htaccess files to override Apache settings
RUN sed -i 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

# ── Application files ─────────────────────────────────────────
# Set the working directory inside the container
WORKDIR /var/www/html

# Copy all project files into the container
# (files listed in .dockerignore are excluded)
COPY . /var/www/html/

# Use the Docker-specific config instead of the XAMPP one
# This switches DB_HOST from 'localhost' to 'db' (the MySQL container name)
COPY includes/config.docker.php /var/www/html/includes/config.php

# ── File permissions ──────────────────────────────────────────
# Give Apache write access to the uploads folder
RUN chown -R www-data:www-data /var/www/html/assets/images \
    && chmod -R 755 /var/www/html/assets/images

# ── Expose port ───────────────────────────────────────────────
# Apache listens on port 80 inside the container
EXPOSE 80
