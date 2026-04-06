#!/bin/bash
# ============================================
# SHURIZA STORE - Deployment Script
# ============================================

set -e

# Configuration - EDIT THESE!
APP_DIR="/var/www/shuriza-store"
REPO_URL="https://github.com/shuriza/shuriza-store.git"
BRANCH="main"
DOMAIN="shurizastore.my.id"  # Ganti dengan domain Anda

# Database config
DB_NAME="shuriza_store"
DB_USER="root"
DB_PASS="your_secure_password_here"  # Ganti dengan password MySQL Anda

echo "🚀 Starting deployment..."

# Clone or pull repository
if [ -d "$APP_DIR/.git" ]; then
    echo "📥 Pulling latest changes..."
    cd $APP_DIR
    git fetch origin
    git reset --hard origin/$BRANCH
else
    echo "📥 Cloning repository..."
    rm -rf $APP_DIR/*
    git clone -b $BRANCH $REPO_URL $APP_DIR
    cd $APP_DIR
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies and build assets
echo "📦 Installing NPM dependencies..."
npm ci
echo "🔨 Building assets..."
npm run build

# Setup environment file
if [ ! -f "$APP_DIR/.env" ]; then
    echo "⚙️ Creating .env file..."
    cp .env.example .env
fi

# Generate app key if not exists
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Update .env for production
echo "⚙️ Updating .env for production..."
sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env
# Remove MySQL credentials (not needed for SQLite)
sed -i "/DB_HOST=/d" .env
sed -i "/DB_PORT=/d" .env
sed -i "/DB_DATABASE=/d" .env
sed -i "/DB_USERNAME=/d" .env
sed -i "/DB_PASSWORD=/d" .env


# Create SQLite database file if not exists
echo "🗄️ Creating SQLite database..."
touch storage/app/database.sqlite
chmod 666 storage/app/database.sqlite

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force || true


# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force

# Set permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

# Setup Nginx
echo "🌐 Configuring Nginx..."
cp $APP_DIR/deploy/nginx-site.conf /etc/nginx/sites-available/shuriza-store
sed -i "s|yourdomain.com|$DOMAIN|g" /etc/nginx/sites-available/shuriza-store

# Enable site
ln -sf /etc/nginx/sites-available/shuriza-store /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
nginx -t && systemctl reload nginx

echo ""
echo "✅ Deployment complete!"
echo ""
echo "🔒 To enable SSL, run:"
echo "   certbot --nginx -d $DOMAIN -d www.$DOMAIN"
echo ""
echo "🌐 Your site is now available at:"
echo "   http://$DOMAIN"
echo ""
