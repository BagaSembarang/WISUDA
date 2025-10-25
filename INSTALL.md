# Panduan Instalasi Lengkap

## Prasyarat

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache/Nginx dengan mod_rewrite enabled
- Composer (opsional)

## Langkah Instalasi

### 1. Setup Database

Buat database baru:
```sql
CREATE DATABASE wisuda_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import schema:
```sql
USE wisuda_db;
SOURCE database/schema.sql;
```

Atau via phpMyAdmin, import file `database/schema.sql`

### 2. Konfigurasi Database

Edit file `app/config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // sesuaikan password
define('DB_NAME', 'wisuda_db');
```

### 3. Install Dependencies

#### Cara Manual:

**SimpleXLSX** (untuk upload Excel):
```bash
cd vendor
git clone https://github.com/shuchkin/simplexlsx.git
```

Atau download dari: https://github.com/shuchkin/simplexlsx/archive/refs/heads/master.zip

**Endroid QR Code** (untuk generate QR):
```bash
composer require endroid/qr-code
```

Atau download manual dan letakkan di `vendor/endroid/`

**FPDF** (untuk generate PDF):
Download dari http://www.fpdf.org/en/download.php
Extract ke `vendor/fpdf/`

### 4. Setup Permissions

```bash
chmod 755 uploads/
chmod 644 .htaccess
```

### 5. Apache Configuration

Pastikan mod_rewrite enabled:
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

Edit `.htaccess` jika perlu menyesuaikan base path.

### 6. Akses Aplikasi

Buka browser:
```
http://localhost/WISUDA/
```

Login dengan:
- Username: `admin`
- Password: `admin123`

## Troubleshooting

### Error: Database connection failed
- Cek kredensial database di `app/config/database.php`
- Pastikan MySQL service running

### Error: Page not found
- Pastikan mod_rewrite Apache enabled
- Cek file `.htaccess` ada dan readable

### Error: Class not found
- Pastikan semua library di folder `vendor/` terinstall
- Cek autoloader di `index.php`

### Upload Excel tidak berfungsi
- Install library SimpleXLSX
- Cek permission folder `uploads/`

### QR Code tidak muncul
- Install library Endroid QR Code
- Cek GD extension PHP enabled

## Konfigurasi Tambahan

### Email (Opsional)
Edit `app/config/config.php` untuk setup email notifications

### WhatsApp Integration (Opsional)
Gunakan WhatsApp Business API atau third-party service

### Backup Database
```bash
mysqldump -u root -p wisuda_db > backup.sql
```

## Update

Untuk update aplikasi:
1. Backup database
2. Replace files (kecuali config)
3. Run migration jika ada
4. Clear cache browser

## Security

1. Ubah password default admin
2. Set `display_errors = 0` di production
3. Gunakan HTTPS
4. Backup database secara berkala
5. Update library secara berkala

## Support

Jika ada masalah, cek:
1. Error log Apache: `/var/log/apache2/error.log`
2. PHP error log
3. Browser console untuk JavaScript errors
