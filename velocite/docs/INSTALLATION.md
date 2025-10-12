# Vélocité Installation Guide

This document provides detailed instructions for setting up the Vélocité bike rental platform on your local development environment or production server.

## System Requirements

- PHP 8.1 or higher
- Composer 2.0 or higher
- MySQL 5.7 or higher (or MariaDB 10.3+)
- Node.js 16.0 or higher (with npm)
- Web server (Apache or Nginx)
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension
- Fileinfo PHP Extension

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/velocite.git
cd velocite
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Set Up Environment Configuration

Copy the example environment file and update it with your configuration:

```bash
cp .env.example .env
```

Edit the `.env` file and configure your database connection:

```
APP_NAME=Vélocité
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=velocite_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create the Database

Create a MySQL database for the application:

```sql
CREATE DATABASE velocite_db;
```

Alternatively, you can run the provided SQL file:

```bash
mysql -u root -p < database/velocite_db.sql
```

### 6. Run Database Migrations and Seed Data

```bash
php artisan migrate
php artisan db:seed
```

This will create all the necessary tables and load initial data, including:
- Default admin user (email: admin@velocite.com, password: password)
- Agent users for different cities
- Bike categories

### 7. Install Frontend Dependencies

```bash
npm install
```

### 8. Build Frontend Assets

```bash
npm run build
```

### 9. Configure Web Server

#### For Apache:

Ensure mod_rewrite is enabled and set up a virtual host:

```apache
<VirtualHost *:80>
    ServerName velocite.local
    DocumentRoot "/path/to/velocite/public"
    <Directory "/path/to/velocite/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### For Nginx:

```nginx
server {
    listen 80;
    server_name velocite.local;
    root /path/to/velocite/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 10. Set Up Storage Symbolic Link

```bash
php artisan storage:link
```

### 11. Set Directory Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 12. Start the Development Server (Optional)

For local development, you can use Laravel's built-in server:

```bash
php artisan serve
```

This will start a development server at `http://localhost:8000`.

## Post-Installation Configuration

### Setting Up Email

Update your `.env` file with your email configuration:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@velocite.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Setting Up Queue Worker (Optional)

For background processing, update your `.env` file:

```
QUEUE_CONNECTION=database
```

Run the queue worker:

```bash
php artisan queue:work
```

In production, you should use a process manager like Supervisor to keep the queue worker running.

### Scheduling Tasks (Optional)

To run the scheduler, add the following Cron entry to your server:

```
* * * * * cd /path/to/velocite && php artisan schedule:run >> /dev/null 2>&1
```

## Default User Accounts

After seeding the database, the following user accounts will be available:

### Admin Account
- Email: admin@velocite.com
- Password: password
- Role: admin

### Agent Accounts
- Email: agent1@velocite.com (Lyon)
- Email: agent2@velocite.com (Marseille)
- Email: agent3@velocite.com (Bordeaux)
- Password: password
- Role: agent

## Troubleshooting

### Database Connection Issues

- Verify your database credentials in the `.env` file
- Ensure MySQL/MariaDB service is running
- Check that your database user has the necessary permissions

### Permission Errors

If you encounter permission errors:

```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Composer Memory Limit

If Composer runs out of memory:

```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### "No application encryption key" Error

Run:

```bash
php artisan key:generate
```

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## Deployment Considerations

For production environments, ensure you set:

```
APP_ENV=production
APP_DEBUG=false
```

Also, optimize the application:

```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Remember to use HTTPS in production environments and configure proper database backups. 
