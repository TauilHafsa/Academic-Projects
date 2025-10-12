# Vélocité Multi-Role Authentication System

This document provides a comprehensive overview of the role-based authentication system implemented in the Vélocité bike rental platform.

## Overview

Vélocité implements a multi-role authentication system that supports four user roles:

1. **Client** - Users who rent bikes
2. **Partner** - Bike owners who list their bikes for rent
3. **Agent** - Customer support staff who moderate listings and manage disputes
4. **Admin** - System administrators with full access to all features

Each role has distinct permissions, dashboards, and functionality specific to their needs.

## System Components

### 1. User Model

The `User` model is extended with a `role` field that stores the user's role. The model provides helper methods for role verification.

```php
// In App\Models\User.php

// Check if user has a specific role
public function hasRole(string $role): bool
{
    return $this->role === $role;
}

// Check if user has any of the specified roles
public function hasAnyRole(array $roles): bool
{
    return in_array($this->role, $roles);
}

// Get the appropriate dashboard route based on user role
public function getDashboardRoute(): string
{
    return match($this->role) {
        'client' => 'client.dashboard',
        'partner' => 'partner.dashboard',
        'agent' => 'agent.dashboard',
        'admin' => 'admin.dashboard',
        default => 'home',
    };
}
```

### 2. Role-Based Middleware

The `CheckRole` middleware ensures users can only access routes appropriate for their role:

```php
// In App\Http\Middleware\CheckRole.php

public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (!$request->user()) {
        return redirect()->route('login');
    }

    foreach ($roles as $role) {
        if ($request->user()->hasRole($role)) {
            return $next($request);
        }
    }

    return redirect()->route('dashboard')
        ->with('error', 'You do not have permission to access this area.');
}
```

This middleware is registered in `app/Http/Kernel.php` as 'role' for easy use in routes.

### 3. Authentication Controllers

#### Login Controller

The `AuthenticatedSessionController` handles login requests and redirects users to role-specific dashboards. It also validates that users are attempting to log in with the correct role.

#### Registration Controllers

Separate registration controllers handle the different registration processes:

- `ClientRegistrationController` - For client registration
- `PartnerRegistrationController` - For partner registration

Admin and agent accounts are typically created by existing admins through an administrative interface.

### 4. Routes

Role-specific routes are protected using the `role` middleware:

```php
// In routes/web.php

// Client routes
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
        ->name('client.dashboard');
    // Additional client routes...
});

// Partner routes
Route::middleware(['auth', 'role:partner'])->group(function () {
    Route::get('/dashboard/partner', [PartnerDashboardController::class, 'index'])
        ->name('partner.dashboard');
    // Additional partner routes...
});

// Similar groups for agent and admin routes...
```

## User Profiles

Each user has an associated profile managed through the `UserProfile` model, which extends the basic user information with:

- Profile picture
- Address information
- Additional contact details
- Role-specific fields

## Authentication Flow

### Registration

1. User selects their role (client or partner) on the registration selection page
2. User completes the role-specific registration form
3. Account is created with the appropriate role
4. User is automatically logged in and redirected to their role-specific dashboard

### Login

1. User visits the login page
2. User selects their account role (client, partner, agent, or admin)
3. User enters their credentials
4. System validates credentials and confirms the role matches
5. User is redirected to their role-specific dashboard

## Blade Templates

### Login View

The login view (`resources/views/auth/login.blade.php`) includes:
- Role selection tabs
- Role description information
- Standard login form
- Links to registration pages

### Registration Views

Role-specific registration forms:
- `resources/views/auth/client-register.blade.php` - Client registration
- `resources/views/auth/partner-register.blade.php` - Partner registration

## Security Considerations

1. Route protection using middleware ensures users can only access appropriate pages
2. Role validation during login prevents users from logging in with incorrect roles
3. Controller methods validate permissions before processing actions
4. Redirects are based on user role to ensure appropriate access

## Frontend Integration

The navigation component (`resources/views/layouts/navigation.blade.php`) adapts to display role-appropriate links based on the authenticated user's role.

## Development Guidelines

When expanding the system:

1. Use the `role` middleware to protect new routes
2. Check user roles in controllers for additional permission verification
3. Use the User model helper methods (`hasRole()`, `hasAnyRole()`) for role checks
4. Consider creating role-specific policies for complex permission structures

## Testing

The authentication system includes test cases for:
- User registration with various roles
- Login validation
- Route access control
- Role-based redirections

Run tests with: `php artisan test --filter AuthTest` 
