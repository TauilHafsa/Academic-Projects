# Vélocité Architecture Documentation

## System Overview

Vélocité is a modern bike rental platform built with Laravel, using a role-based access control system to provide different capabilities to clients, partners, agents, and administrators. The application follows Laravel's MVC (Model-View-Controller) architecture pattern with the addition of service layers for business logic.

## Technology Stack

- **Backend Framework**: Laravel 10.x
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze (with customizations for roles)
- **PHP Version**: PHP 8.1+

## Directory Structure

The application follows Laravel's standard directory structure with some custom additions:

```
velocite/
├── app/
│   ├── Console/             # Console commands
│   ├── Exceptions/          # Exception handlers
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   │   ├── Admin/       # Admin-specific controllers
│   │   │   ├── Agent/       # Agent-specific controllers
│   │   │   ├── Client/      # Client-specific controllers
│   │   │   ├── Partner/     # Partner-specific controllers
│   │   ├── Middleware/      # Request middleware
│   │   └── Requests/        # Form requests & validation
│   ├── Models/              # Eloquent models
│   ├── Providers/           # Service providers
│   └── Services/            # Business logic services
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── public/                  # Publicly accessible files
├── resources/
│   ├── css/                 # CSS source files
│   ├── js/                  # JavaScript source files
│   └── views/
│       ├── admin/           # Admin dashboard views
│       ├── agent/           # Agent dashboard views
│       ├── auth/            # Authentication views
│       ├── bikes/           # Bike-related views
│       ├── client/          # Client dashboard views
│       ├── components/      # Reusable Blade components
│       ├── layouts/         # Layout templates
│       ├── partner/         # Partner dashboard views
│       └── rentals/         # Rental-related views
├── routes/                  # Route definitions
└── tests/                   # Test cases
```

## Core Components

### Authentication & Authorization

The authentication system uses Laravel Breeze with custom modifications to support multiple user roles. The key components include:

1. **User Model**: Extended with a `role` field to distinguish between user types.
2. **RoleMiddleware**: Middleware to restrict access to routes based on user roles.
3. **Custom Guards**: Role-specific authentication guards.

Authorization is implemented using Laravel's Gates and Policies to control access to resources based on user roles and ownership.

### Role-Based Access

The application defines four primary user roles:

1. **Client**: Users who rent bikes
2. **Partner**: Bike owners who list their bikes for rent
3. **Agent**: Support staff who manage partners and rentals
4. **Admin**: System administrators with full access

Each role has its own set of controllers, views, and route definitions to provide a tailored experience.

### Database Layer

The database layer uses Laravel's Eloquent ORM with models that follow single responsibility principles. Key aspects include:

1. **Migrations**: Define the database schema in a version-controlled manner.
2. **Models**: Implement business rules and relationships.
3. **Seeders**: Provide initial data for development and testing.

### Request Handling

The application follows the standard Laravel request lifecycle:

1. Routes direct requests to appropriate controllers
2. Middleware performs authentication and authorization checks
3. Form requests validate input data
4. Controllers orchestrate the response, using services for complex business logic
5. Views render the HTML output with Blade templates

### Service Layer

The service layer contains business logic that would otherwise complicate controllers. Services are organized by domain:

- `BikeService`: Manages bike listings and availability
- `RentalService`: Handles rental creation, modification, and completion
- `NotificationService`: Manages user notifications
- `RatingService`: Processes user and bike ratings
- `PaymentService`: Handles payment processing for rentals and premium listings

### Agent Communication System

The agent communication system provides tools for support agents to facilitate communication between clients and partners:

- `AgentController`: Manages agent communication and moderation features
- `RentalComment` Model: Extended with moderation capabilities
- `RentalEvaluation` Model: Manages structured feedback collection
- Comment moderation workflow: Approve, edit, or reject user comments
- Visibility controls: Agents can direct messages to specific parties

### Frontend Architecture

The frontend uses Blade templates with Tailwind CSS for styling. Key aspects include:

1. **Layouts**: Base templates that define the overall page structure
2. **Components**: Reusable Blade components for UI elements
3. **Role-Specific Views**: Dedicated view directories for each user role

## Key Design Patterns

### Repository Pattern

The application employs the repository pattern to abstract database operations:

```php
// Interface definition
interface BikeRepositoryInterface
{
    public function findAvailableBikes($filters);
    public function findByOwner($ownerId);
    // ...
}

// Implementation
class EloquentBikeRepository implements BikeRepositoryInterface
{
    public function findAvailableBikes($filters)
    {
        return Bike::where('is_available', true)
            // Apply other filters
            ->get();
    }
    // ...
}
```

### Service Pattern

Services encapsulate business logic and provide a clean API for controllers:

```php
class RentalService
{
    public function createRental(User $renter, Bike $bike, array $data)
    {
        // Check availability
        // Calculate price
        // Create rental record
        // Send notifications
        // ...
    }
    
    public function completeRental(Rental $rental)
    {
        // Update rental status
        // Process security deposit return
        // ...
    }
}
```

### Observer Pattern

Laravel's observer pattern is used to respond to model events:

```php
class RentalObserver
{
    public function created(Rental $rental)
    {
        // Send notifications
        // Update bike availability
        // ...
    }
    
    public function updated(Rental $rental)
    {
        // React to status changes
        // ...
    }
}
```

## Data Flow

1. **Bike Listing Flow**:
   - Partner creates a bike listing
   - Partners sets availability and pricing
   - Bike becomes available in the marketplace

2. **Rental Process Flow**:
   - Client searches for available bikes
   - Client requests a rental for specific dates
   - Partner receives notification and approves/rejects
   - Client receives confirmation
   - Client picks up and returns bike
   - Both parties leave ratings

3. **Admin Management Flow**:
   - Admin monitors platform activity
   - Admin manages user accounts and resolves disputes
   - Admin reviews and moderates content

4. **Agent Communication Flow**:
   - Agent reviews rentals requiring intervention
   - Agent facilitates communication between clients and partners
   - Agent sends evaluation forms for dispute resolution
   - Agent moderates comments for quality control
   - Agent monitors and resolves issues during the rental lifecycle

## Security Considerations

The application implements several security measures:

1. **Authentication**: Secure login with password hashing and remember tokens
2. **Authorization**: Role-based access control and resource ownership checks
3. **CSRF Protection**: Cross-site request forgery protection on all forms
4. **XSS Prevention**: Content escaping in Blade templates
5. **SQL Injection Prevention**: Use of query builders and prepared statements
6. **Validation**: Input validation using form requests
7. **Sensitive Data**: Proper handling of sensitive data (payments, personal information)

## Scalability Considerations

The architecture is designed with scalability in mind:

1. **Database Optimization**: Indexing and query optimization
2. **Caching**: Strategic caching of frequently accessed data
3. **Queue System**: Background processing for non-critical tasks
4. **Horizontal Scaling**: Stateless design that allows for multiple application servers

## Testing Strategy

The application includes multiple levels of testing:

1. **Unit Tests**: Test individual components in isolation
2. **Feature Tests**: Test complete features and user flows
3. **Browser Tests**: Test UI interactions with Laravel Dusk
4. **API Tests**: Test API endpoints for mobile integration

## Deployment Architecture

The recommended deployment architecture includes:

1. **Web Servers**: Multiple application servers behind a load balancer
2. **Database**: Primary database with read replicas for scale
3. **Cache**: Redis for session storage and caching
4. **Queue**: Redis or database for background job processing
5. **Storage**: Cloud storage for user uploads (bike images)

## Extension Points

The architecture provides several extension points:

1. **Service Providers**: Register new services and bindings
2. **Middleware**: Add new request processing steps
3. **Event Listeners**: React to system events
4. **Commands**: Add new console commands
5. **Policies**: Define new authorization rules
6. **API Resources**: Define new API response transformations

## 8. Admin Dashboard System

The Admin Dashboard provides comprehensive administrative capabilities for managing the Vélocité platform.

### 8.1 Admin Controller Components

- **AdminController**: Core controller that provides administrative functionality for managing users, bikes, categories, and analytics.
  - User management (CRUD operations)
  - Bike management and oversight
  - Category management
  - System statistics and reports

### 8.2 Admin Dashboard Features

#### 8.2.1 User Management
- User listing with search and role filtering
- User creation with profile information
- User editing and deletion
- Role assignment (client, partner, agent, admin)

#### 8.2.2 Bike Management
- Bike listing with search and filtering
- Bike editing and status control
- Integration with bike categories
- Performance metrics tracking

#### 8.2.3 Category Management
- Category listing with bike counts
- Category creation and editing
- Category deletion (with dependency checks)

#### 8.2.4 Statistics and Analytics
- User statistics (counts, roles, growth)
- Bike statistics (availability, categories)
- Rental statistics (status, completion rates)
- Monthly rental trend visualization

#### 8.2.5 Business Reports
- Revenue metrics and visualizations
- Top user rankings (renters and partners)
- Top performing bikes
- Geographic distribution analysis

### 8.3 Admin Interface Security

- Role-based middleware protection (`role:admin`)
- Data validation and sanitization
- Dependency checks before destructive operations
- Cross-user protection (preventing admins from deleting themselves)

### 8.4 Implementation Details

- Dashboard views using Tailwind CSS with responsive design
- Interactive charts and visualizations
- Efficient database queries with relationship loading
- Real-time statistics calculation

### 8.5 Integration with Other Systems

- User system integration for comprehensive user management
- Bike management system integration for bike oversight
- Rental system integration for performance metrics
- Comment and evaluation monitoring for quality control 
