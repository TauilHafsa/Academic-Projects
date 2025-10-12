# Vélocité Database Documentation

## Overview

The Vélocité database is designed to support a comprehensive bike rental platform with multiple user roles, rental management, ratings, and premium listings. The database structure follows Laravel's Eloquent ORM conventions and uses modern database design principles.

## Database Schema

### Users and Authentication

#### `users` Table
Stores all user accounts with their roles and authentication information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | User's email address (unique) |
| email_verified_at | timestamp | When email was verified |
| password | varchar(255) | Hashed password |
| role | enum | User role: 'client', 'partner', 'agent', or 'admin' |
| remember_token | varchar(100) | Token for "remember me" functionality |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

#### `user_profiles` Table
Extends the user model with additional profile information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| user_id | bigint unsigned | Foreign key to users.id |
| profile_picture | varchar(255) | Path to profile image (nullable) |
| phone_number | varchar(20) | Contact number (nullable) |
| address | text | User's address (nullable) |
| city | varchar(100) | User's city |
| bio | text | User's bio or description (nullable) |
| average_rating | decimal(3,2) | Average rating received (default 0) |
| rating_count | int | Number of ratings received (default 0) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

### Bike Management

#### `bike_categories` Table
Categorizes bikes by type for better organization and search.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| name | varchar(100) | Category name |
| description | text | Category description (nullable) |
| icon | varchar(255) | Path to category icon (nullable) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

#### `bikes` Table
Core table storing all bikes available for rent.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| owner_id | bigint unsigned | Foreign key to users.id (bike owner) |
| category_id | bigint unsigned | Foreign key to bike_categories.id |
| title | varchar(100) | Bike listing title |
| description | text | Detailed description |
| brand | varchar(50) | Bike brand |
| model | varchar(50) | Bike model |
| year | year | Manufacturing year |
| color | varchar(30) | Bike color |
| frame_size | varchar(20) | Frame size (nullable) |
| condition | enum | Condition: 'new', 'like_new', 'good', 'fair' |
| hourly_rate | decimal(10,2) | Hourly rental rate |
| daily_rate | decimal(10,2) | Daily rental rate |
| weekly_rate | decimal(10,2) | Weekly rental rate (nullable) |
| security_deposit | decimal(10,2) | Required security deposit (nullable) |
| location | varchar(100) | Pickup/dropoff location |
| latitude | decimal(10,8) | Latitude coordinates (nullable) |
| longitude | decimal(11,8) | Longitude coordinates (nullable) |
| is_electric | boolean | Whether bike is electric (default false) |
| is_available | boolean | Whether bike is available (default true) |
| average_rating | decimal(3,2) | Average rating (default 0) |
| rating_count | int | Number of ratings (default 0) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |
| deleted_at | timestamp | Soft delete timestamp (nullable) |

#### `bike_images` Table
Stores multiple images for each bike.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| bike_id | bigint unsigned | Foreign key to bikes.id |
| image_path | varchar(255) | Path to stored image |
| is_primary | boolean | Whether this is the main image (default false) |
| sort_order | int | Display order (default 0) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

#### `bike_availabilities` Table
Defines specific dates when bikes are available or unavailable.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| bike_id | bigint unsigned | Foreign key to bikes.id |
| date | date | Specific date |
| is_available | boolean | Whether bike is available on this date (default true) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

### Rental Management

#### `rentals` Table
Core table tracking all bike rentals.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| bike_id | bigint unsigned | Foreign key to bikes.id |
| renter_id | bigint unsigned | Foreign key to users.id (person renting) |
| status | enum | Status: 'pending', 'confirmed', 'cancelled', 'completed', 'rejected' |
| start_date | datetime | Rental start time |
| end_date | datetime | Rental end time |
| total_price | decimal(10,2) | Total rental cost |
| security_deposit | decimal(10,2) | Security deposit amount (nullable) |
| is_deposit_returned | boolean | Whether deposit was returned (default false) |
| pickup_notes | text | Special instructions for pickup (nullable) |
| cancellation_reason | text | Reason for cancellation if applicable (nullable) |
| cancelled_at | datetime | When rental was cancelled (nullable) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |
| deleted_at | timestamp | Soft delete timestamp (nullable) |

### Ratings and Reviews

#### `bike_ratings` Table
Stores ratings and reviews for bikes after rentals.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| rental_id | bigint unsigned | Foreign key to rentals.id |
| bike_id | bigint unsigned | Foreign key to bikes.id |
| user_id | bigint unsigned | Foreign key to users.id (reviewer) |
| rating | int | Rating value (1-5) |
| review | text | Written review (nullable) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

*Unique constraint on rental_id and user_id to prevent duplicate ratings.*

#### `user_ratings` Table
Stores ratings between users (renters rating owners and vice versa).

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| rental_id | bigint unsigned | Foreign key to rentals.id |
| rater_id | bigint unsigned | Foreign key to users.id (person giving rating) |
| rated_user_id | bigint unsigned | Foreign key to users.id (person being rated) |
| rating | int | Rating value (1-5) |
| review | text | Written review (nullable) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

*Unique constraint on rental_id, rater_id, and rated_user_id to prevent duplicate ratings.*

### Communication

#### `comments` Table
Allows communication between renters and owners about rentals.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| user_id | bigint unsigned | Foreign key to users.id (comment author) |
| rental_id | bigint unsigned | Foreign key to rentals.id |
| content | text | Comment content |
| parent_id | bigint unsigned | Self-reference for threaded comments (nullable) |
| is_private | boolean | Whether comment is private (default false) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |
| deleted_at | timestamp | Soft delete timestamp (nullable) |

#### `notifications` Table
Stores system and user notifications.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| user_id | bigint unsigned | Foreign key to users.id (recipient) |
| type | varchar(50) | Notification type (e.g., 'rental_request', 'message') |
| notifiable_id | bigint unsigned | ID of related model (polymorphic) |
| notifiable_type | varchar(255) | Type of related model (polymorphic) |
| content | text | Notification content |
| is_read | boolean | Whether notification has been read (default false) |
| link | varchar(255) | URL to navigate to (nullable) |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

### Premium Features

#### `premium_listings` Table
Enables paid promotion of bike listings.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| bike_id | bigint unsigned | Foreign key to bikes.id |
| start_date | datetime | When promotion starts |
| end_date | datetime | When promotion ends |
| type | enum | Type: 'featured', 'highlighted', 'top_search' |
| price | decimal(8,2) | Amount paid for premium listing |
| status | enum | Status: 'active', 'expired', 'cancelled' |
| created_at | timestamp | When record was created |
| updated_at | timestamp | When record was last updated |

## Relationships

### User Relationships
- User has one UserProfile
- User has many Bikes (as owner)
- User has many Rentals (as renter)
- User has many BikeRatings (ratings given)
- User has many UserRatings (as rater and as rated)
- User has many Notifications
- User has many Comments

### Bike Relationships
- Bike belongs to User (owner)
- Bike belongs to BikeCategory
- Bike has many BikeImages
- Bike has one primary BikeImage
- Bike has many BikeRatings
- Bike has many Rentals
- Bike has many BikeAvailabilities
- Bike has many PremiumListings

### Rental Relationships
- Rental belongs to Bike
- Rental belongs to User (renter)
- Rental has one BikeRating
- Rental has many UserRatings
- Rental has many Comments

### Rating Relationships
- BikeRating belongs to Rental
- BikeRating belongs to Bike
- BikeRating belongs to User
- UserRating belongs to Rental
- UserRating belongs to User (rater)
- UserRating belongs to User (rated)

### Other Relationships
- BikeAvailability belongs to Bike
- BikeImage belongs to Bike
- Comment belongs to User
- Comment belongs to Rental
- Comment belongs to Comment (parent)
- Comment has many Comments (replies)
- Notification belongs to User
- Notification belongs to a morphTo relationship (notifiable)
- PremiumListing belongs to Bike

## Indexes and Constraints

- Foreign key constraints on all relationship columns
- Unique indexes on user emails
- Unique composite index on bike_id and date in bike_availabilities
- Unique composite index on rental_id and user_id in bike_ratings
- Unique composite index on rental_id, rater_id, and rated_user_id in user_ratings

## Soft Deletes

The following models use soft deletes (adding a deleted_at column that marks records as deleted without actually removing them):
- Bike
- Rental
- Comment

This allows for data recovery and maintains referential integrity while still allowing "deletion" from the user perspective.

## Database Configuration

The database connection is configured in the `.env` file with the following settings:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=velocite_db
DB_USERNAME=root
DB_PASSWORD=
```

For production environments, a secure password should be set and potentially a different database user with appropriate permissions. 
