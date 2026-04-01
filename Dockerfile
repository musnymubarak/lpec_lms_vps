# Moodle 5.0.1 - PHP 8.3 FPM + Nginx
# Using PHP 8.3 to match the cPanel environment exactly
FROM php:8.3-fpm-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    cron \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libldap2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libsodium-dev \
    libwebp-dev \
    libxslt1-dev \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions required by Moodle
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo_mysql \
        xml \
        mbstring \
        curl \
        zip \
        gd \
        intl \
        soap \
        ldap \
        opcache \
        bcmath \
        sodium \
        exif \
        xsl



# Copy custom PHP config
COPY ./docker/php/moodle.ini /usr/local/etc/php/conf.d/moodle.ini

# Copy Nginx config
COPY ./docker/nginx/moodle.conf /etc/nginx/sites-available/default

# Create moodledata directory
RUN mkdir -p /var/www/moodledata \
    && chown -R www-data:www-data /var/www/moodledata \
    && chmod -R 770 /var/www/moodledata

# Copy Moodle source code
COPY --chown=www-data:www-data ./lms.lpec.lk /var/www/html

# Set proper permissions on Moodle code
RUN find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

# Setup Moodle cron job (runs every minute)
RUN echo "* * * * * www-data /usr/local/bin/php /var/www/html/admin/cli/cron.php > /dev/null 2>&1" > /etc/cron.d/moodle-cron \
    && chmod 0644 /etc/cron.d/moodle-cron \
    && crontab -u www-data /etc/cron.d/moodle-cron

# Copy entrypoint script
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port 80 (Nginx inside container)
EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
