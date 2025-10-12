# Vélocité Bike Management System

This document provides a detailed overview of the bike management system implemented in the Vélocité platform, focusing on the partner dashboard and bike listing functionality.

## Overview

The Vélocité bike management system allows partners (bike owners) to list their bikes for rent, manage availability, handle rental requests, and upgrade to premium listings. The system is fully integrated with the multi-role authentication system and includes comprehensive validation and security features.

## Core Components

### Models

1. **Bike**
   - Stores basic bike information (brand, model, year, etc.)
   - Pricing data (hourly, daily, weekly rates)
   - Location information
   - Availability status
   - Rating and review aggregates
   - Relationships to images, availabilities, rentals

2. **BikeImage**
   - Stores image paths for bike photos
   - Supports multiple images per bike
   - Designates a primary image for listings
   - Configurable sort order

3. **BikeAvailability**
   - Date-based availability records
   - Allows calendar-based availability management
   - Prevents conflicts with active rentals

4. **PremiumListing**
   - Premium listing upgrades for bikes
   - Supports different tiers (Featured, Spotlight, Promoted)
   - Time-based validity (7, 14, or 30 days)
   - Status tracking

5. **Rental**
   - Tracks rental requests and bookings
   - Status management (pending, confirmed, ongoing, completed, cancelled)
   - Date ranges and pricing
   - Links to both bike owners and renters

### Controllers

1. **BikeController**
   - Full CRUD operations for bike listings
   - Image upload and management
   - Availability toggling
   - Policy-based access control

2. **PartnerDashboardController**
   - Dashboard statistics and metrics
   - Recent and popular bike listings
   - Rental request overview

3. **SearchController**
   - Handles all customer-facing search and discovery operations
   - Index: Search results view with comprehensive filtering
   - Map: Interactive map view showing bike locations
   - ApplyFilters: Private method to handle filtering logic for search queries

### Policies

1. **BikePolicy**
   - Ensures partners can only manage their own bikes
   - Controls access to specific bike operations
   - Enforces business rules (e.g., maximum 5 active listings)

## Features

### Bike Listing Management

Partners can create, view, update, and delete their bike listings with the following features:

1. **Listing Creation**
   - Comprehensive bike details
   - Multiple image uploads (up to 5 images)
   - Primary image selection
   - Pricing options (hourly, daily, weekly)
   - Location information

2. **Listing Management**
   - Edit all bike details
   - Add/remove images
   - Change primary image
   - Toggle availability status (active/archived)

3. **Listing Limitations**
   - Maximum 5 active listings per partner
   - Validation of required fields
   - Image size and type restrictions

### Availability Management

The system includes a calendar-based availability management system:

1. **Calendar Interface**
   - Visual monthly calendar
   - Date range selection
   - Bulk status updates
   - Clear indication of unavailable and booked dates

2. **Conflict Prevention**
   - Cannot mark dates as unavailable if they have confirmed rentals
   - Past dates are automatically unavailable
   - Validation of date selections

### Premium Listings

Partners can upgrade their listings to premium status:

1. **Premium Tiers**
   - Featured: Basic premium visibility (€9.99 - €29.99)
   - Spotlight: Enhanced visibility + homepage feature (€14.99 - €49.99)
   - Promoted: Maximum visibility + featured everywhere (€19.99 - €69.99)

2. **Duration Options**
   - 7 days
   - 14 days
   - 30 days

3. **Premium Benefits**
   - Increased visibility in search results
   - Premium badge on listings
   - Performance analytics
   - Higher conversion rates

### Bike Search and Discovery

Customers can search and discover bikes through:

1. **Comprehensive Search**
   - Filtering options
   - Interactive map view
   - Featured bikes section
   - Category browsing
   - Advanced filtering by price, rating, and electric status
   - Sorting options by price, rating, and newness

### Partner Dashboard

Partners have access to a comprehensive dashboard:

1. **Statistics Overview**
   - Total and active bike count
   - Pending and active rental counts
   - Total and monthly earnings

2. **Bike Listings**
   - Recent bikes added
   - Most popular bikes (based on rental count)
   - Quick access to bike management

3. **Rental Management**
   - Recent rental requests
   - Status indicators
   - Quick actions for rental management

## Frontend Views

The frontend interface includes several key views:

1. **Bike Index**
   - Grid of bike listings
   - Status indicators (active/archived, premium)
   - Rating display
   - Quick actions for management

2. **Bike Detail**
   - Image gallery
   - Complete bike specifications
   - Pricing information
   - Rental request management
   - Quick actions (edit, availability, archive, premium)

3. **Bike Forms**
   - Create form with image upload
   - Edit form with image management
   - Availability calendar
   - Premium upgrade options

## Workflow Examples

### Adding a New Bike

1. Partner accesses the "Add New Bike" page
2. Completes the bike details form
3. Uploads bike images and selects a primary image
4. Submits the form
5. System validates the data
6. Creates the bike listing with default availability
7. Redirects to the bike detail page

### Managing Availability

1. Partner accesses the availability calendar for a specific bike
2. Selects dates or date ranges
3. Marks them as available or unavailable
4. System validates against existing rentals
5. Updates the availability records
6. Displays confirmation message

### Upgrading to Premium

1. Partner chooses to upgrade a bike listing
2. Selects premium tier (Featured, Spotlight, Promoted)
3. Chooses duration (7, 14, or 30 days)
4. Reviews summary with pricing
5. Confirms upgrade
6. System creates premium listing record
7. Updates the bike's premium status

## API Endpoints

The system provides RESTful API endpoints for bike management:

1. **Bike Listings**
   - `GET /partner/bikes` - List all bikes
   - `POST /partner/bikes` - Create a new bike
   - `GET /partner/bikes/{bike}` - View a specific bike
   - `PUT /partner/bikes/{bike}` - Update a bike
   - `DELETE /partner/bikes/{bike}` - Delete a bike

2. **Availability Management**
   - `GET /partner/bikes/{bike}/availability` - View availability
   - `POST /partner/bikes/{bike}/availability` - Update availability

3. **Premium Listings**
   - `GET /partner/bikes/{bike}/premium` - Premium options
   - `POST /partner/bikes/{bike}/premium` - Create premium listing

## Database Schema

The bike management system uses the following database tables:

1. **bikes**
   - `id` - Primary key
   - `owner_id` - Foreign key to users table
   - `category_id` - Foreign key to bike_categories table
   - Basic bike details (title, description, specs)
   - Pricing fields
   - Location information
   - Status and rating fields
   - Timestamps and soft delete

2. **bike_images**
   - `id` - Primary key
   - `bike_id` - Foreign key to bikes table
   - `image_path` - Path to stored image
   - `is_primary` - Boolean flag for primary image
   - `sort_order` - Integer for display order

3. **bike_availabilities**
   - `id` - Primary key
   - `bike_id` - Foreign key to bikes table
   - `date` - The specific date
   - `is_available` - Boolean availability status

4. **premium_listings**
   - `id` - Primary key
   - `bike_id` - Foreign key to bikes table
   - `type` - Premium tier type
   - `start_date`, `end_date` - Validity period
   - `price` - Cost of the premium listing
   - `status` - Current status

## Security Considerations

The bike management system implements several security measures:

1. **Authentication**
   - All bike management routes require authentication
   - Role-based access control via middleware

2. **Authorization**
   - BikePolicy ensures partners can only manage their own bikes
   - Method-level authorization for specific actions

3. **Data Validation**
   - Comprehensive request validation
   - Image size and type validation
   - Date and availability conflict validation

4. **Database Integrity**
   - Transactions for atomic operations
   - Foreign key constraints
   - Soft deletes to preserve rental history

## Development Guidelines

When extending the bike management system:

1. Always check ownership via the BikePolicy
2. Use database transactions for multi-step operations
3. Validate all user inputs thoroughly
4. Consider rental dependencies when changing bike status
5. Follow the existing controller/view patterns for consistency

## Testing

The bike management system includes tests for:
```
php artisan test --filter BikeTest
``` 
