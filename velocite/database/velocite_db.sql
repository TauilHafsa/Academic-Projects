-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS velocite_db;

-- Use the database
USE velocite_db;

-- Set character set and collation
ALTER DATABASE velocite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Note: The migrations and seeders will create all the required tables and populate initial data
-- To run migrations and seeders, execute the following commands in your Laravel project:
-- php artisan migrate
-- php artisan db:seed

-- If you prefer to use SQL directly, you can export the schema after migrations:
-- mysqldump -u root -p --no-data velocite_db > schema.sql

-- For the default admin user:
-- Email: admin@velocite.com
-- Password: password

-- For agent users:
-- Email: agent1@velocite.com (Lyon)
-- Email: agent2@velocite.com (Marseille)
-- Email: agent3@velocite.com (Bordeaux)
-- Password: password
