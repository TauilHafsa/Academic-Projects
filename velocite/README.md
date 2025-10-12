<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Vélocité Bike Rental Platform

Vélocité is a comprehensive bike rental platform that connects bike owners (partners) with people who want to rent bikes (clients).

## Features

### For Bike Owners (Partners)
- Manage up to 5 bike listings
- Upload multiple images for each bike
- Set hourly, daily, and weekly rates
- Manage availability with an interactive calendar
- Upgrade listings to premium status for better visibility
- Track rental requests and earnings
- Process rental approvals and manage active rentals
- Rate renters after completed rentals
- Communicate with renters through the platform

### For Renters (Clients)
- Search for bikes with comprehensive filtering options:
  - By location, category, price range, and rating
  - Using keywords to find specific bikes
- View bikes on an interactive map
- Browse by categories
- Sort search results by price, rating, or newest listings
- View detailed bike information with image galleries
- Check bike availability calendar
- Request bike rentals for specific dates
- Manage all rental requests in one place
- Rate bikes after completed rentals
- Communicate with bike owners

### Rental System
- Complete rental lifecycle management:
  - Request > Approval > Pickup > Return > Rating
- Status tracking (pending, confirmed, ongoing, completed, cancelled, rejected)
- Calendar integration to prevent double bookings
- Price calculation based on rental duration
- Detailed rental information for both parties
- Cancellation/rejection with reason tracking
- In-platform communication between renter and bike owner
- Post-rental rating and review system
- Notifications for all rental status changes

### Notification System
- Real-time notifications for important events
- Notification bell with unread count
- Dropdown notification list
- Dedicated notifications page with filtering options
- Toast notifications for immediate alerts
- Notification types:
  - Rental status changes
  - New comments
  - Rating submissions
  - Booking confirmations
  
For more details, see [Notification System Documentation](docs/notification-system.md).

### For Administrators
- Manage user accounts
- Approve partner applications
- Monitor platform activity
- Generate reports

## Tech Stack
- Laravel (PHP Framework)
- Tailwind CSS
- Alpine.js
- Blade Templates
- MySQL Database
- Leaflet.js for maps

## Installation

1. Clone the repository
   ```
   git clone https://github.com/yourusername/velocite.git
   ```

2. Install dependencies
   ```
   composer install
   npm install
   ```

3. Copy the environment file
   ```
   cp .env.example .env
   ```

4. Generate an application key
   ```
   php artisan key:generate
   ```

5. Set up your database in the .env file
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=velocite
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Run migrations and seed the database
   ```
   php artisan migrate --seed
   ```

7. Create a symbolic link for storage
   ```
   php artisan storage:link
   ```

8. Compile assets
   ```
   npm run dev
   ```

9. Start the development server
   ```
   php artisan serve
   ```

## Documentation
For more detailed documentation, please check:
- [Bike Management](docs/bike-management.md)
- [User Management](docs/user-management.md)
- [Rental System](docs/rental-system.md)
- [Notification System](docs/notification-system.md)
- [Changes Log](docs/CHANGES.md)

## License
The Vélocité platform is open-sourced software licensed under the [MIT license](LICENSE.md).

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
