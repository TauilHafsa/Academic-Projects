# Vélocité Rental System

This document provides a detailed overview of the rental system implemented in the Vélocité platform, focusing on the rental workflow, backend architecture, and user interfaces for both clients and partners.

## Overview

The Vélocité rental system enables clients to book bikes from partners for specific date ranges, with a complete workflow from request to completion, including features like cancellation, rating, and communication between parties.

## Core Components

### Models

1. **Rental**
   - Stores rental information (dates, pricing, status)
   - Manages the entire rental lifecycle with statuses:
     - `pending`: Initial request awaiting partner approval
     - `confirmed`: Approved by partner, awaiting pickup
     - `ongoing`: Bike has been picked up and is in use
     - `completed`: Rental has been completed and bike returned
     - `cancelled`: Cancelled by the client before confirmation
     - `rejected`: Declined by the partner
   - Tracks pricing, security deposits, and pickup notes
   - Includes cancellation timestamps and reasons

2. **BikeRating**
   - Allows clients to rate bikes after rental completion
   - Stores numeric ratings (1-5) and text reviews
   - Updates bike's average rating and rating count

3. **RentalComment**
   - Facilitates communication between client and partner
   - Supports threaded conversations about rental details
   - Timestamps all communications

4. **Notification**
   - Tracks system notifications for users
   - Associates with various notification types:
     - Rental requests
     - Status changes
     - New comments
     - Ratings
   - Stores read/unread status

### Controllers

1. **RentalController** (for clients)
   - Handles all client-side rental operations:
     - Viewing rental history and details
     - Creating rental requests
     - Cancelling rentals
     - Rating completed rentals
   - Implements availability checking to prevent double bookings
   - Calculates rental prices based on duration

2. **PartnerRentalController** (for partners)
   - Manages partner-side rental operations:
     - Viewing and filtering incoming rental requests
     - Approving or rejecting requests
     - Starting and completing rentals
     - Adding comments and communicating with renters

3. **ClientDashboardController** and **PartnerDashboardController**
   - Display rental statistics and summaries
   - Show upcoming and recent rental activity
   - Provide quick access to rental management

## Features

### For Clients (Renters)

1. **Rental Request Creation**
   - Selection of rental dates with availability checking
   - Real-time price calculation based on duration
   - Optional notes for pickup arrangements
   - Confirmation of request submission

2. **Rental Management**
   - View all rentals with status filtering
   - Access detailed rental information
   - Cancel pending or confirmed rentals
   - Add comments and communicate with bike owner

3. **Rating System**
   - Rate bikes after completed rentals
   - Provide written reviews of rental experience
   - View average ratings and review counts

### For Partners (Bike Owners)

1. **Rental Request Management**
   - View all incoming rental requests
   - Filter by status (pending, confirmed, ongoing, etc.)
   - Approve or reject requests with reason
   - View detailed renter information

2. **Active Rental Management**
   - Mark rentals as started when bike is picked up
   - Mark rentals as completed when bike is returned
   - Communicate with renter through comments
   - View renter history and ratings

3. **Dashboard Analytics**
   - Track rental statistics by status
   - View monthly earnings from rentals
   - Monitor upcoming and active rentals

### System Features

1. **Availability Management**
   - Prevents double bookings with conflict detection
   - Validates date ranges for logical consistency
   - Handles availability calendar updates
   - Checks conflicts with existing rentals

2. **Notification System**
   - Sends notifications for rental status changes
   - Alerts users to new comments or messages
   - Notifies of upcoming rental dates
   - Prompts for ratings after completion

3. **Price Calculation**
   - Calculates total price based on daily rate × duration
   - Handles security deposit tracking
   - Supports deposit return confirmation

## Frontend Views

### Client Views

1. **Rentals Index**
   - List of all client rentals with filtering
   - Status indicators and key information
   - Action buttons based on rental status
   - Sorting and pagination

2. **Rental Detail**
   - Complete rental information
   - Bike details with image
   - Status updates and history
   - Rating form for completed rentals
   - Communication system for messages

3. **Rental Creation**
   - Date selection with availability checking
   - Price calculator showing total cost
   - Bike details summary
   - Special instructions field

### Partner Views

1. **Rentals Management Index**
   - Comprehensive list of all rentals for partner's bikes
   - Status filtering with count badges
   - Quick action buttons for common operations
   - Search and filter capabilities

2. **Rental Detail**
   - Complete rental information
   - Renter details and history
   - Action buttons based on rental status
   - Communication thread
   - Rating information when available

3. **Dashboard Integration**
   - Rental statistics overview
   - Recent and pending rental requests
   - Alerts for required actions

## Workflow Examples

### Client Rental Process

1. Client searches for available bikes
2. Selects a bike and clicks "Rent This Bike"
3. Chooses rental dates and sees price calculation
4. Submits rental request
5. Receives notification when request is approved/rejected
6. Picks up bike at start of rental period
7. Returns bike at end of rental period
8. Receives prompt to rate experience
9. Submits rating and review

### Partner Rental Management Process

1. Partner receives notification of new rental request
2. Reviews request details and renter information
3. Approves or rejects the request
4. If approved, meets with renter for pickup
5. Marks rental as "ongoing" once bike is picked up
6. Maintains communication with renter as needed
7. Receives bike back at end of rental period
8. Marks rental as "completed"
9. Views renter's rating and feedback

## Database Schema

The rental system uses the following database tables:

1. **rentals**
   - Primary key and foreign keys to bikes and users
   - Start and end dates for rental period
   - Status field tracking the rental lifecycle
   - Pricing information including total price and security deposit
   - Notes fields for pickup instructions and cancellation reasons
   - Timestamps for creation, updates, and cancellation

2. **bike_ratings**
   - Links to rentals, bikes, and users tables
   - Rating value (1-5)
   - Text review
   - Timestamps

3. **rental_comments**
   - Links to rentals and users tables
   - Comment content
   - Privacy flag for internal notes
   - Timestamps

4. **notifications**
   - Links to users and related models (polymorphic)
   - Type identifier for notification category
   - Content and link data
   - Read/unread status
   - Timestamps

## API Endpoints

The system provides the following API endpoints:

### Client Endpoints

- `GET /rentals` - View all rentals
- `GET /rentals/create` - Create rental form
- `POST /rentals` - Submit rental request
- `GET /rentals/{id}` - View specific rental
- `POST /rentals/{id}/cancel` - Cancel rental
- `POST /rentals/{id}/rate` - Rate completed rental

### Partner Endpoints

- `GET /partner/rentals` - View all rentals
- `GET /partner/rentals/{id}` - View specific rental
- `POST /partner/rentals/{id}/approve` - Approve rental request
- `POST /partner/rentals/{id}/reject` - Reject rental request
- `POST /partner/rentals/{id}/start` - Mark rental as started
- `POST /partner/rentals/{id}/complete` - Mark rental as completed
- `POST /partner/rentals/{id}/comment` - Add comment to rental

## Integration with Other Systems

The rental system integrates with:

1. **Bike Management System**
   - Checks bike availability
   - Updates bike rating statistics
   - Links rentals to bike details

2. **User Management System**
   - Provides renter and owner information
   - Updates user statistics and history
   - Enforces role-based access control

3. **Notification System**
   - Handles all rental-related notifications
   - Manages read/unread status
   - Provides links to relevant actions

4. **Search System**
   - Incorporates availability data into search results
   - Shows rental history and ratings in bike details
   - Updates search ranking based on rental popularity

## Security and Validation

1. **Access Control**
   - Clients can only view and manage their own rentals
   - Partners can only manage rentals for their own bikes
   - Status changes are validated against allowed transitions

2. **Data Validation**
   - Date ranges are validated for consistency
   - Pricing calculations are verified
   - Rating values are constrained to valid range
   - Comments are filtered for inappropriate content

3. **Status Integrity**
   - Prevents invalid status transitions
   - Ensures chronological integrity of rental lifecycle
   - Maintains consistency between related models 
