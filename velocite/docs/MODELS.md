# Vélocité Models Documentation

## Overview

The Vélocité application follows Laravel's Eloquent ORM architecture, with models representing database tables and their relationships. This document provides detailed information about each model, including their attributes, relationships, and custom methods.

## Core Models

### User Model

**File:** `app/Models/User.php`

The User model is the central model for authentication and user management. It extends Laravel's Authenticatable class.

**Attributes:**
- `name` - User's full name
- `email` - User's email address (unique)
- `password` - Hashed password
- `role` - User's role in the system (client, partner, agent, admin)

**Relationships:**
- `profile()` - One-to-one relationship with UserProfile
- `bikes()` - One-to-many relationship with Bike (as owner)
- `rentals()` - One-to-many relationship with Rental (as renter)
- `bikeRatings()` - One-to-many relationship with BikeRating (ratings given)
- `givenRatings()` - One-to-many relationship with UserRating (as rater)
- `receivedRatings()` - One-to-many relationship with UserRating (as rated)
- `notifications()` - One-to-many relationship with Notification
- `comments()` - One-to-many relationship with Comment

**Methods:**
- `hasRole($role)` - Checks if user has a specific role
- `getAverageRatingAttribute()` - Accessor to get user's average rating

**Usage Example:**
```php
// Check if user is an admin
if ($user->hasRole('admin')) {
    // Admin-specific code
}

// Get user's profile information
$city = $user->profile->city;

// Get bikes owned by the user
$userBikes = $user->bikes;
```

### UserProfile Model

**File:** `app/Models/UserProfile.php`

Extends the User model with additional profile information.

**Attributes:**
- `user_id` - Foreign key to User
- `profile_picture` - Path to profile image
- `phone_number` - Contact number
- `address` - User's address
- `city` - User's city
- `bio` - User's bio or description
- `average_rating` - Average rating received
- `rating_count` - Number of ratings received

**Relationships:**
- `user()` - Belongs to User

**Usage Example:**
```php
// Get the profile for a user
$profile = User::find(1)->profile;

// Update profile information
$profile->update([
    'phone_number' => '+33 1 23 45 67 89',
    'city' => 'Paris'
]);
```

### Bike Model

**File:** `app/Models/Bike.php`

Represents a bike available for rent in the system.

**Attributes:**
- `owner_id` - Foreign key to User (owner)
- `category_id` - Foreign key to BikeCategory
- `title` - Bike listing title
- `description` - Detailed description
- `brand` - Bike brand
- `model` - Bike model
- `year` - Manufacturing year
- `color` - Bike color
- `frame_size` - Frame size
- `condition` - Bike condition (new, like_new, good, fair)
- `hourly_rate` - Hourly rental rate
- `daily_rate` - Daily rental rate
- `weekly_rate` - Weekly rental rate
- `security_deposit` - Required security deposit
- `location` - Pickup/dropoff location
- `latitude` - Latitude coordinates
- `longitude` - Longitude coordinates
- `is_electric` - Whether bike is electric
- `is_available` - Whether bike is available for rent
- `average_rating` - Average rating received
- `rating_count` - Number of ratings received

**Relationships:**
- `owner()` - Belongs to User
- `category()` - Belongs to BikeCategory
- `images()` - Has many BikeImage
- `primaryImage()` - Has one BikeImage (where is_primary is true)
- `ratings()` - Has many BikeRating
- `rentals()` - Has many Rental
- `availabilities()` - Has many BikeAvailability
- `premiumListings()` - Has many PremiumListing

**Traits:**
- Uses `SoftDeletes` for recoverable deletions

**Usage Example:**
```php
// Get the owner of a bike
$owner = Bike::find(1)->owner;

// Get the primary image of a bike
$mainImage = $bike->primaryImage;

// Check if bike is electric
if ($bike->is_electric) {
    // Electric bike specific code
}

// Get all rentals for this bike
$rentals = $bike->rentals;
```

### BikeCategory Model

**File:** `app/Models/BikeCategory.php`

Categorizes bikes by type for better organization and search.

**Attributes:**
- `name` - Category name
- `description` - Category description
- `icon` - Path to category icon

**Relationships:**
- `bikes()` - Has many Bike

**Usage Example:**
```php
// Get all bikes in a category
$mountainBikes = BikeCategory::where('name', 'Mountain Bike')->first()->bikes;

// Count bikes in each category
$categories = BikeCategory::withCount('bikes')->get();
```

### Rental Model

**File:** `app/Models/Rental.php`

Tracks bike rental transactions between users.

**Attributes:**
- `bike_id` - Foreign key to Bike
- `renter_id` - Foreign key to User (person renting)
- `status` - Status of rental (pending, confirmed, cancelled, completed, rejected)
- `start_date` - Rental start time
- `end_date` - Rental end time
- `total_price` - Total rental cost
- `security_deposit` - Security deposit amount
- `is_deposit_returned` - Whether deposit was returned
- `pickup_notes` - Special instructions for pickup
- `cancellation_reason` - Reason for cancellation if applicable
- `cancelled_at` - When rental was cancelled

**Relationships:**
- `bike()` - Belongs to Bike
- `renter()` - Belongs to User
- `bikeRating()` - Has one BikeRating
- `userRatings()` - Has many UserRating
- `comments()` - Has many Comment

**Methods:**
- `owner()` - Get the bike owner through the bike relationship

**Traits:**
- Uses `SoftDeletes` for recoverable deletions

**Usage Example:**
```php
// Check if rental is active
if ($rental->status == 'confirmed') {
    // Active rental code
}

// Get the bike associated with a rental
$bike = $rental->bike;

// Get the renter
$renter = $rental->renter;

// Get the owner of the rented bike
$owner = $rental->owner();
```

## Rating Models

### BikeRating Model

**File:** `app/Models/BikeRating.php`

Stores ratings and reviews for bikes after rentals.

**Attributes:**
- `rental_id` - Foreign key to Rental
- `bike_id` - Foreign key to Bike
- `user_id` - Foreign key to User (reviewer)
- `rating` - Rating value (1-5)
- `review` - Written review

**Relationships:**
- `rental()` - Belongs to Rental
- `bike()` - Belongs to Bike
- `user()` - Belongs to User

**Usage Example:**
```php
// Get the average rating for a bike
$averageRating = BikeRating::where('bike_id', $bikeId)->avg('rating');

// Get all reviews for a bike with user information
$reviews = BikeRating::with('user')->where('bike_id', $bikeId)->get();
```

### UserRating Model

**File:** `app/Models/UserRating.php`

Stores ratings between users (renters rating owners and vice versa).

**Attributes:**
- `rental_id` - Foreign key to Rental
- `rater_id` - Foreign key to User (person giving rating)
- `rated_user_id` - Foreign key to User (person being rated)
- `rating` - Rating value (1-5)
- `review` - Written review

**Relationships:**
- `rental()` - Belongs to Rental
- `rater()` - Belongs to User (rater)
- `ratedUser()` - Belongs to User (rated)

**Usage Example:**
```php
// Get the average rating for a user
$averageRating = UserRating::where('rated_user_id', $userId)->avg('rating');

// Get all reviews received by a user
$receivedReviews = UserRating::with('rater')
    ->where('rated_user_id', $userId)
    ->get();
```

## Supporting Models

### BikeImage Model

**File:** `app/Models/BikeImage.php`

Stores images for bikes with ordering and primary image designation.

**Attributes:**
- `bike_id` - Foreign key to Bike
- `image_path` - Path to stored image
- `is_primary` - Whether this is the main image
- `sort_order` - Display order

**Relationships:**
- `bike()` - Belongs to Bike

**Usage Example:**
```php
// Get all images for a bike, ordered
$images = BikeImage::where('bike_id', $bikeId)
    ->orderBy('sort_order')
    ->get();
    
// Set an image as the primary image
BikeImage::where('bike_id', $bikeId)->update(['is_primary' => false]);
$image->update(['is_primary' => true]);
```

### BikeAvailability Model

**File:** `app/Models/BikeAvailability.php`

Defines specific dates when bikes are available or unavailable.

**Attributes:**
- `bike_id` - Foreign key to Bike
- `date` - Specific date
- `is_available` - Whether bike is available on this date

**Relationships:**
- `bike()` - Belongs to Bike

**Usage Example:**
```php
// Check if a bike is available on a specific date
$isAvailable = BikeAvailability::where('bike_id', $bikeId)
    ->where('date', $requestedDate)
    ->where('is_available', true)
    ->exists();
    
// Set a date range as unavailable
$startDate = new DateTime('2023-07-01');
$endDate = new DateTime('2023-07-15');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($startDate, $interval, $endDate);

foreach ($dateRange as $date) {
    BikeAvailability::updateOrCreate(
        ['bike_id' => $bikeId, 'date' => $date->format('Y-m-d')],
        ['is_available' => false]
    );
}
```

### Comment Model

**File:** `app/Models/Comment.php`

Allows communication between renters and owners about rentals.

**Attributes:**
- `user_id` - Foreign key to User (comment author)
- `rental_id` - Foreign key to Rental
- `content` - Comment content
- `parent_id` - Self-reference for threaded comments
- `is_private` - Whether comment is private

**Relationships:**
- `user()` - Belongs to User
- `rental()` - Belongs to Rental
- `parent()` - Belongs to Comment (parent)
- `replies()` - Has many Comment (children)

**Traits:**
- Uses `SoftDeletes` for recoverable deletions

**Usage Example:**
```php
// Get all top-level comments for a rental
$comments = Comment::whereNull('parent_id')
    ->where('rental_id', $rentalId)
    ->with('user', 'replies.user')
    ->get();
    
// Add a reply to a comment
Comment::create([
    'user_id' => auth()->id(),
    'rental_id' => $rentalId,
    'content' => 'This is a reply',
    'parent_id' => $parentCommentId
]);
```

### Notification Model

**File:** `app/Models/Notification.php`

Stores system and user notifications with polymorphic relationships.

**Attributes:**
- `user_id` - Foreign key to User (recipient)
- `type` - Notification type
- `notifiable_id` - ID of related model (polymorphic)
- `notifiable_type` - Type of related model (polymorphic)
- `content` - Notification content
- `is_read` - Whether notification has been read
- `link` - URL to navigate to

**Relationships:**
- `user()` - Belongs to User
- `notifiable()` - Polymorphic relationship to related model

**Methods:**
- `markAsRead()` - Mark notification as read

**Usage Example:**
```php
// Create a notification for a rental request
Notification::create([
    'user_id' => $bikeOwnerId,
    'type' => 'rental_request',
    'notifiable_id' => $rental->id,
    'notifiable_type' => get_class($rental),
    'content' => "{$renter->name} has requested to rent your bike",
    'link' => route('rentals.show', $rental->id)
]);

// Get unread notifications for the current user
$notifications = Notification::where('user_id', auth()->id())
    ->where('is_read', false)
    ->get();
    
// Mark notification as read
$notification->markAsRead();
```

### PremiumListing Model

**File:** `app/Models/PremiumListing.php`

Enables paid promotion of bike listings.

**Attributes:**
- `bike_id` - Foreign key to Bike
- `start_date` - When promotion starts
- `end_date` - When promotion ends
- `type` - Type of premium listing (featured, highlighted, top_search)
- `price` - Amount paid for premium listing
- `status` - Status of listing (active, expired, cancelled)

**Relationships:**
- `bike()` - Belongs to Bike

**Methods:**
- `isActive()` - Determine if the premium listing is currently active

**Usage Example:**
```php
// Create a new premium listing
PremiumListing::create([
    'bike_id' => $bikeId,
    'start_date' => now(),
    'end_date' => now()->addDays(30),
    'type' => 'featured',
    'price' => 29.99,
    'status' => 'active'
]);

// Get all active featured bikes
$featuredBikes = PremiumListing::where('type', 'featured')
    ->where('status', 'active')
    ->where('start_date', '<=', now())
    ->where('end_date', '>=', now())
    ->with('bike')
    ->get()
    ->pluck('bike');
    
// Check if a listing is active
if ($premiumListing->isActive()) {
    // Active listing code
}
```

## Model Best Practices

Throughout the implementation of the Vélocité models, the following best practices are followed:

1. **Type Hints**: All model relationships use proper PHP return type hints for better code completion and static analysis.

2. **Appropriate Casts**: Models use proper cast definitions to ensure data is correctly converted between database and PHP types.

3. **Mass Assignment Protection**: All models define `$fillable` arrays to protect against mass assignment vulnerabilities.

4. **Soft Deletes**: Models that benefit from recoverable deletion use the SoftDeletes trait.

5. **Clear Naming Conventions**: Relationship methods are named following Laravel conventions to ensure readability and consistency.

6. **Proper Relationship Definitions**: Correct relationship types (hasOne, hasMany, belongsTo, etc.) are used based on database schema.

7. **Accessors and Mutators**: Custom accessors and mutators are implemented where needed to provide computed attributes.

## Advanced Model Usage

### Eager Loading

To optimize performance, relationships should be eager loaded when needed:

```php
// Inefficient - N+1 queries
$bikes = Bike::all();
foreach ($bikes as $bike) {
    echo $bike->owner->name;
}

// Efficient - 2 queries
$bikes = Bike::with('owner')->get();
foreach ($bikes as $bike) {
    echo $bike->owner->name;
}
```

### Query Scopes

For common filtering operations, query scopes can be added to models:

```php
// In the Bike model
public function scopeAvailable($query)
{
    return $query->where('is_available', true);
}

public function scopeElectric($query)
{
    return $query->where('is_electric', true);
}

// Usage
$availableElectricBikes = Bike::available()->electric()->get();
```

### Custom Collections

Custom collection classes can be implemented for model-specific collection behavior:

```php
// In the Bike model
public function newCollection(array $models = [])
{
    return new BikeCollection($models);
}

// In BikeCollection.php
class BikeCollection extends Collection
{
    public function byPrice($order = 'asc')
    {
        return $this->sortBy('daily_rate', SORT_REGULAR, $order === 'desc');
    }
}

// Usage
$bikes = Bike::all()->byPrice('desc');
``` 
