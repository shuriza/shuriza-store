# 🚀 Deploy Shuriza Store ke DigitalOcean

## Persyaratan
- Akun DigitalOcean
- Domain (opsional, bisa pakai IP dulu)
- SSH client (Terminal/PuTTY)

---

## Step 1: Buat Droplet

1. Login ke [cloud.digitalocean.com](https://cloud.digitalocean.com)
2. Klik **Create** → **Droplets**
3. Pilih:
   - **Image:** Ubuntu 22.04 LTS
   - **Plan:** Basic $6/bulan (1GB RAM) atau lebih besar
   - **Region:** Singapore (SGP1)
   - **Authentication:** SSH Key atau Password
4. Klik **Create Droplet**
5. Catat IP Address

---

## Step 2: Setup Server

```bash
# SSH ke server
ssh root@YOUR_IP_ADDRESS

# Download dan jalankan setup script
curl -O https://raw.githubusercontent.com/shuriza/shuriza-store/main/deploy/server-setup.sh

# PENTING: Edit password MySQL dulu!
nano server-setup.sh
# Ganti "your_secure_password_here" dengan password yang kuat

# Jalankan script
chmod +x server-setup.sh
bash server-setup.sh
```

---

## Step 3: Deploy Aplikasi

```bash
# Download deploy script
curl -O https://raw.githubusercontent.com/shuriza/shuriza-store/main/deploy/deploy.sh

# PENTING: Edit konfigurasi dulu!
nano deploy.sh
# Ganti:
# - DOMAIN dengan domain Anda (atau IP address)
# - DB_PASS dengan password MySQL

# Jalankan deployment
chmod +x deploy.sh
bash deploy.sh
```

---

## Step 4: Setup SSL (Jika punya domain)

```bash
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## Step 5: Pointing Domain

Di DNS provider Anda, tambahkan:
- **A Record:** `@` → `YOUR_IP_ADDRESS`
- **A Record:** `www` → `YOUR_IP_ADDRESS`

---

## Maintenance Commands

```bash
# Re-deploy (update ke versi terbaru)
cd /var/www/shuriza-store
bash deploy/deploy.sh

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Lihat logs
tail -f /var/www/shuriza-store/storage/logs/laravel.log

# Restart services
systemctl restart php8.3-fpm
systemctl restart nginx
systemctl restart mysql
```

---

## Troubleshooting

### 502 Bad Gateway
```bash
systemctl status php8.3-fpm
systemctl restart php8.3-fpm
```

### Permission Denied
```bash
chown -R www-data:www-data /var/www/shuriza-store
chmod -R 775 /var/www/shuriza-store/storage
```

### Database Connection Error
```bash
# Test koneksi MySQL
mysql -u root -p -e "SELECT 1"

# Cek .env
cat /var/www/shuriza-store/.env | grep DB_
```

---

## Estimasi Biaya DigitalOcean

| Resource | Biaya/Bulan |
|----------|-------------|
| Droplet 1GB | $6 |
| Droplet 2GB (recommended) | $12 |
| Managed MySQL (opsional) | $15+ |
| Backup | +20% |

**Total minimum:** $6-12/bulan

---

## Kontak Support

Jika ada masalah, hubungi:
- WhatsApp: 6281234567890
- Email: support@yourdomain.com
