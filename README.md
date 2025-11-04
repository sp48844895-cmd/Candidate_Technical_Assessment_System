# Candidate Technical Assessment System - Setup Guide

## Prerequisites

- PHP >= 8.2
- Composer

## Installation Steps

1. **Clone the repository**
   ```bash
   git clone "https://github.com/sp48844895-cmd/Candidate_Technical_Assessment_System.git"
   cd Candidate_Technical_Assessment_System
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** (edit `.env` file)
   
   **For SQLite (default):**
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   ```
   
   **For MySQL:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=assessment_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
   
   **For PostgreSQL:**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=assessment_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed database with sample questions**
   ```bash
   php artisan db:seed
   ```

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Set permissions** (Linux/Mac)
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    - Open browser and navigate to `http://localhost:8000`

## Troubleshooting

### Storage Link Issues
If file uploads don't work:
```bash
php artisan storage:link
```

### Database Issues
If migrations fail:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Clear Cache
If you encounter any issues:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Ensure storage directory is writable
7. Set up proper web server (Apache/Nginx)
8. Configure SSL certificate
