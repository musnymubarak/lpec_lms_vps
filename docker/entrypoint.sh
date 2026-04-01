#!/bin/bash
set -e

echo "🚀 Starting Moodle container..."

# Ensure moodledata directories exist with correct permissions
echo "📁 Setting up moodledata directories..."
mkdir -p /var/www/moodledata/{cache,localcache,temp,sessions,lock,filedir,trashdir}
chown -R www-data:www-data /var/www/moodledata
chmod -R 770 /var/www/moodledata

# Ensure local cache dir exists
mkdir -p /tmp/moodle-local-cache
chown www-data:www-data /tmp/moodle-local-cache

# Moodle code permissions are set during build.
echo "🔒 Skipping file permission checks to speed up boot..."

# Start cron daemon in background
echo "⏰ Starting cron daemon..."
service cron start

# Start Nginx in background
echo "🌐 Starting Nginx..."
nginx

# Start PHP-FPM in foreground (keeps container running)
echo "✅ Starting PHP-FPM..."
php-fpm --nodaemonize
