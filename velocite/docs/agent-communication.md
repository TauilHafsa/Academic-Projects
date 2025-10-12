# Agent Communication Management System

This document provides a comprehensive overview of the agent communication management system in the Vélocité bike rental platform.

## Overview

The agent communication management system allows support agents to facilitate communication between clients (bike renters) and partners (bike owners), manage disputes, and moderate comments. The system provides agents with tools to efficiently manage rental-related communications and ensure a positive experience for all users.

## Key Features

### 1. Rental Management Dashboard

Agents have access to a dedicated rental management interface that allows them to:

- View all rentals within their assigned city
- Filter rentals by status, search terms, etc.
- Access detailed rental information
- Facilitate communication between clients and partners
- Send evaluation forms for dispute resolution

### 2. Comment Management

Agents can manage communications by:

- Adding comments to rental threads
- Controlling comment visibility (visible to both parties, client only, or partner only)
- Adding private notes visible only to other agents and administrators

### 3. Evaluation Forms

Agents can create and send structured evaluation forms to gather information in specific scenarios:

- **Service Feedback**: General feedback about the rental experience
- **Dispute Resolution**: When there's a disagreement between parties
- **Damage Assessment**: When there's a claim of damage to the bike

### 4. Comment Moderation

Agents have moderation capabilities:

- Review pending comments for inappropriate content
- Approve comments that meet community guidelines
- Edit comments that need minor modifications
- Reject comments that violate platform rules with explanatory notes

## Technical Implementation

### Database Schema

The agent communication system utilizes several database tables:

#### RentalComments Table
- Standard fields: rental_id, user_id, content, is_private
- Moderation fields: is_moderated, moderated_by, moderated_at, moderation_status, moderation_notes, original_content
- Agent fields: agent_comment, agent_comment_visibility

#### RentalEvaluations Table
- Tracks evaluation forms sent by agents
- Contains fields for evaluation type, message, status, response, etc.

### Controller Structure

The system is built around the `AgentController` class that contains methods for:

- Rental listing and detailed view (`rentals`, `showRental`)
- Comment management (`createComment`, `storeComment`)
- Evaluation form creation (`createEvaluationForm`, `sendEvaluationForm`)
- Comment moderation (`moderateComments`, `approveComment`, `rejectComment`, `editComment`, `updateComment`)

### Routes and Views

The UI is built with clean, accessible Blade templates and follows the platform's design system:

- `/agent/rentals`: Lists all rentals for communication management
- `/agent/rentals/{id}`: Detailed rental view with communication history
- `/agent/rentals/{rentalId}/comments/create`: Form to add agent comments
- `/agent/rentals/{rentalId}/evaluation/create`: Form to create evaluation forms
- `/agent/moderate/comments`: Interface for comment moderation
- `/agent/comments/{id}/edit`: Interface for editing comments during moderation

## User Flow

### Facilitating Communication

1. Agent navigates to the rental management dashboard
2. Agent selects a rental that needs intervention
3. Agent views the communication history between parties
4. Agent adds a comment, selecting appropriate visibility
5. Notifications are sent to the relevant parties

### Moderating Comments

1. Agent navigates to the comment moderation interface
2. Agent reviews pending comments awaiting moderation
3. Agent can approve, edit, or reject each comment
4. If editing, agent can provide moderation notes
5. If rejecting, agent must provide a reason for rejection
6. Notifications inform users of moderation actions

### Sending Evaluation Forms

1. Agent navigates to the evaluation form creation page
2. Agent selects the evaluation type and recipients
3. Agent adds a message explaining the purpose
4. System sends notifications with links to complete the evaluation
5. Parties submit their responses, which agents can review

## Security and Access Control

The agent functionality is secured in several ways:

- All agent routes are protected by middleware that verifies the user has the 'agent' role
- City-based scope restriction ensures agents can only access rentals in their assigned city
- Agents can see all communications, including private comments
- The system logs all moderation actions with timestamps and agent identifiers

## User Experience Considerations

The agent interface is designed to:

- Provide clear visibility of pending tasks (moderation queue)
- Offer quick access to communication tools
- Support efficient workflows through intuitive navigation
- Ensure agents have contextual information when moderating or facilitating

## Future Enhancements

Potential future improvements to the system include:

- Advanced filtering and sorting options for the moderation queue
- Automated content moderation assistance using ML
- Comment templates for common scenarios
- Performance metrics and reporting for agent activities
- Integration with a ticketing system for complex issues

## API Integration

The agent communication system provides internal API endpoints that support:

- Comment notifications
- Evaluation form delivery
- Moderation status updates

## System Requirements

The agent communication system requires:

- Laravel 9.0+
- MySQL 5.7+ or MariaDB 10.3+
- Modern browser with JavaScript enabled
- User accounts with the 'agent' role assigned 
