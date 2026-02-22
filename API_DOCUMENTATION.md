# Handyman Service API Documentation

## Overview
This document provides comprehensive documentation for the Handyman Service Platform API. The API is built with Laravel and uses Laravel Sanctum for authentication.

**Base URL:** `http://127.0.0.1:8000/api`

**Production URL:** Update this with your production domain

## Authentication

The API uses **Bearer Token** authentication via Laravel Sanctum. After successful login, you'll receive an `api_token` that must be included in the Authorization header for protected endpoints.

### Headers for Authenticated Requests
```
Authorization: Bearer {your_api_token}
Accept: application/json
```

## Quick Start

### 1. Import Postman Collection
Import the `Handyman_Service_API.postman_collection.json` file into Postman.

### 2. Set Environment Variables
- `base_url`: `http://127.0.0.1:8000/api`
- `auth_token`: Will be auto-populated after login

### 3. Login
Use the Login endpoint with default credentials:
- **Email:** `admin@admin.com`
- **Password:** Check your database seeder or documentation

The auth token will be automatically saved to the collection variable.

## API Endpoints Overview

### 📁 Authentication (7 endpoints)
- `POST /register` - Register new user
- `POST /login` - User login
- `POST /social-login` - Social media login (Google, Facebook, Apple)
- `POST /forgot-password` - Request password reset
- `GET /logout` - Logout user (requires auth)
- `POST /check-referral` - Validate referral code
- `POST /check-field` - Check username availability

### 👤 User Profile (5 endpoints)
- `GET /user-detail` - Get current user details
- `POST /update-profile` - Update user profile
- `POST /change-password` - Change password
- `GET /user-wallet-balance` - Get wallet balance
- `POST /delete-account` - Delete user account

### 🏷️ Categories & Services (9 endpoints)
- `GET /category-list` - List all categories
- `GET /subcategory-list` - List subcategories by category
- `GET /service-list` - List all services
- `POST /service-detail` - Get service details
- `GET /top-rated-service` - Get top-rated services
- `POST /service-reviews` - Get service reviews
- `POST /save-favourite` - Add service to favorites
- `POST /delete-favourite` - Remove from favorites
- `GET /user-favourite-service` - Get user's favorite services

### 📅 Bookings (7 endpoints)
- `POST /booking-save` - Create new booking
- `GET /booking-list` - List user bookings
- `POST /booking-detail` - Get booking details
- `POST /booking-update` - Update booking
- `POST /booking-action` - Perform action on booking
- `POST /save-booking-rating` - Rate a booking
- `GET /booking-status` - Get available booking statuses

### 💳 Payments & Wallet (7 endpoints)
- `POST /save-payment` - Process payment
- `GET /payment-list` - List payments
- `GET /payment-history` - Get payment history
- `POST /wallet-top-up` - Add funds to wallet
- `GET /wallet-history` - Get wallet transaction history
- `POST /withdraw-money` - Withdraw from wallet
- `GET /payment-gateways` - Get available payment gateways

### 🏪 Provider Management (7 endpoints)
- `GET /provideraddress-list` - List provider addresses
- `POST /save-provideraddress` - Save provider address
- `GET /provider-document-list` - List provider documents
- `POST /provider-document-save` - Upload provider document
- `GET /provider-payout-list` - List provider payouts
- `POST /save-favourite-provider` - Add provider to favorites
- `GET /user-favourite-provider` - Get favorite providers

### 📊 Dashboard & Analytics (4 endpoints)
- `GET /dashboard-detail` - Get dashboard overview
- `GET /provider-dashboard` - Provider-specific dashboard
- `GET /handyman-dashboard` - Handyman-specific dashboard
- `GET /admin-dashboard` - Admin dashboard

### 🛠️ Common Utilities (10 endpoints)
- `POST /configurations` - Get app configurations
- `POST /country-list` - List countries
- `POST /state-list` - List states by country
- `POST /city-list` - List cities by state
- `GET /slider-list` - Get promotional sliders
- `GET /coupon-list` - List available coupons
- `GET /type-list` - Get type lists
- `GET /tax-list` - Get tax information
- `GET /zones` - Get service zones
- `POST /contact-us` - Submit contact form

### 🏬 Shops (5 endpoints)
- `GET /shop-list` - List all shops
- `GET /shop-detail/{id}` - Get shop details
- `POST /shop-create` - Create new shop
- `POST /shop-update/{id}` - Update shop
- `POST /shop-delete/{id}` - Delete shop

### 📝 Post Job Requests (6 endpoints)
- `GET /get-post-job` - List job requests
- `POST /get-post-job-detail` - Get job request details
- `POST /save-post-job` - Create job request
- `POST /save-bid` - Submit bid on job
- `GET /get-bid-list` - List bids
- `GET /post-job-status` - Get job statuses

### 📰 Blogs (2 endpoints)
- `GET /blog-list` - List blog posts
- `POST /blog-detail` - Get blog post details

### 🔔 Notifications (1 endpoint)
- `POST /notification-list` - Get user notifications

### 🎁 Loyalty Points (2 endpoints)
- `GET /loyalty-history` - Get loyalty point history
- `GET /get-earn-points` - Get earn points by service

### 🎫 Help Desk (3 endpoints)
- `GET /helpdesk-list` - List support tickets
- `POST /helpdesk-save` - Create support ticket
- `GET /helpdesk-detail` - Get ticket details

## Common Request Examples

### Register New User
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -F "first_name=John" \
  -F "last_name=Doe" \
  -F "username=johndoe" \
  -F "email=john@example.com" \
  -F "password=password123" \
  -F "user_type=user" \
  -F "contact_number=1234567890"
```

### Login
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -F "email=admin@admin.com" \
  -F "password=yourpassword"
```

### Get Service List (with pagination)
```bash
curl -X GET "http://127.0.0.1:8000/api/service-list?per_page=10&page=1"
```

### Create Booking (Authenticated)
```bash
curl -X POST http://127.0.0.1:8000/api/booking-save \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "service_id=1" \
  -F "provider_id=2" \
  -F "date=2026-02-15" \
  -F "time=10:00" \
  -F "address=123 Main St" \
  -F "description=Service description"
```

## User Types

The API supports three user types:
- **user** - Regular customers who book services
- **provider** - Service providers who offer services
- **handyman** - Workers who perform the services
- **admin** - System administrators

## Response Format

### Success Response
```json
{
  "status": true,
  "message": "Success message",
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "status": false,
  "message": "Error message",
  "errors": {
    // Validation errors if applicable
  }
}
```

## Pagination

List endpoints support pagination with the following parameters:
- `per_page` - Number of items per page (default: 10)
- `page` - Page number (default: 1)

Paginated responses include:
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 5,
  "per_page": 10,
  "total": 50
}
```

## File Uploads

For endpoints that accept file uploads (images, documents):
- Use `multipart/form-data` content type
- Maximum file size: Check server configuration
- Supported formats: jpg, jpeg, png, pdf (varies by endpoint)

## Payment Gateways

Supported payment methods:
- **Cash** - Cash on delivery
- **Stripe** - Credit/debit cards
- **Razorpay** - Indian payment gateway
- **PayPal** - PayPal payments
- **Wallet** - Internal wallet system
- **PhonePe** - Indian UPI payments

## Booking Statuses

- `pending` - Booking created, awaiting acceptance
- `accept` - Provider accepted the booking
- `on_going` - Service in progress
- `complete` - Service completed
- `cancelled` - Booking cancelled

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Rate Limiting

API requests may be rate-limited. Check response headers:
- `X-RateLimit-Limit` - Maximum requests allowed
- `X-RateLimit-Remaining` - Remaining requests
- `Retry-After` - Seconds to wait before retry

## Testing

### Using Postman
1. Import the collection
2. Set environment variables
3. Run the Login request
4. Token will be auto-saved
5. Test other endpoints

### Using cURL
See example requests above. Replace `YOUR_TOKEN` with actual token from login response.

## Support

For API support or questions:
- Check the Postman collection for detailed examples
- Review Laravel logs: `storage/logs/laravel.log`
- Contact: [Your support email]

## Version History

- **v1.0** - Initial API release
- Current version includes 100+ endpoints across 14 categories

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Postman Documentation](https://learning.postman.com/)

---

**Last Updated:** February 12, 2026
