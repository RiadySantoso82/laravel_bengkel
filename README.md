# Aplikasi Manajemen Bengkel

Aplikasi manajemen bengkel berbasis Laravel 8 dengan MySQL.

## Requirements

- PHP 7.4+
- Composer
- MySQL

## Instalasi

1. Clone repositori:
   ```
   git clone <repo-url>
   cd bengkel
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Copy file environment:
   ```
   copy .env.example .env
   ```

4. Isi konfigurasi database di `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=root
   DB_PASSWORD=password
   ```

5. Generate key:
   ```
   php artisan key:generate
   ```

## Migration & Seeder

Jalankan migration untuk membuat tabel:
```
php artisan migrate
```

Jalankan seeder untuk mengisi data awal (termasuk user default):
```
php artisan db:seed
```

Atau jalankan keduanya sekaligus:
```
php artisan migrate --seed
```

### User Default (setelah seeding)

| Role | Username | Password |
|------|----------|----------|
| Admin/Owner | `admin` | `admin123` |
| Kasir/Front Office | `kasir` | `kasir123` |
| Mekanik | `mekanik` | `mekanik123` |

## Menjalankan Aplikasi

```
php artisan serve
```

Akses di `http://localhost:8000`.
