#!/bin/bash
# ============================================
# SHURIZA STORE - Server Setup Script
# DigitalOcean Ubuntu 22.04 LTS
# ============================================

set -e

echo "🚀 Starting server setup..."

# Update system
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install essential packages
echo "📦 Installing essential packages..."
apt install -y curl wget git unzip software-properties-common

# Add PHP repository
echo "📦 Adding PHP repository..."
add-apt-repository -y ppa:ondrej/php

# Install Nginx
echo "🌐 Installing Nginx..."
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# Install PHP 8.3 and extensions
echo "🐘 Installing PHP 8.3..."
apt install -y php8.3-fpm php8.3-cli php8.3-common php8.3-mysql \
    php8.3-xml php8.3-curl php8.3-gd php8.3-mbstring php8.3-zip \
    php8.3-bcmath php8.3-intl php8.3-readline php8.3-opcache

# Install MySQL
echo "🗄️ Installing MySQL..."
apt install -y mysql-server
systemctl enable mysql
systemctl start mysql

# Secure MySQL
echo "🔒 Securing MySQL..."
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_secure_password_here';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "FLUSH PRIVILEGES;"

# Create database
echo "🗄️ Creating database..."
mysql -u root -p'your_secure_password_here' -e "CREATE DATABASE shuriza_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Install Composer
echo "🎼 Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js 20 LTS
echo "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Install Certbot for SSL
echo "🔒 Installing Certbot..."
apt install -y certbot python3-certbot-nginx

# Configure PHP-FPM
echo "⚙️ Configuring PHP-FPM..."
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 64M/' /etc/php/8.3/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 64M/' /etc/php/8.3/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/8.3/fpm/php.ini
systemctl restart php8.3-fpm

# Create web directory
echo "📁 Creating web directory..."
mkdir -p /var/www/shuriza-store
chown -R www-data:www-data /var/www/shuriza-store

# Configure firewall
echo "🔥 Configuring firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

echo ""
echo "✅ Server setup complete!"
echo ""
echo "📝 IMPORTANT - Save these credentials:"
echo "   MySQL Root Password: your_secure_password_here"
echo "   Database Name: shuriza_store"
echo ""
echo "🔄 Next steps:"
echo "   1. Change MySQL password in this script before running"
echo "   2. Run: bash server-setup.sh"
echo "   3. Then run: bash deploy.sh"
