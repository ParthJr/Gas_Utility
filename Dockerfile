# Official PHP image with Apache
FROM php:8.2-apache

# Install required system libraries and PHP extensions (MySQL, GD, ZIP)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite, headers and remoteip
RUN a2enmod rewrite headers remoteip

# Allow .htaccess directives to override Apache configuration and prevent leaking backend host
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && echo "UseCanonicalName Off" >> /etc/apache2/apache2.conf \
    && echo "UseCanonicalPhysicalPort Off" >> /etc/apache2/apache2.conf

# Render / Cloud platforms bind to dynamic $PORT (default 80 or 10000)
ENV PORT=80
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Set work directory
WORKDIR /var/www/html

# Copy all application files
COPY . /var/www/html/

# Set proper permissions for Apache user
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose ports
EXPOSE 80 10000

# Start Apache server in foreground
CMD ["apache2-foreground"]
