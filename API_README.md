# API Collection Files

This directory contains the complete Postman API documentation for the Handyman Service Platform.

## 📦 Files Included

1. **Handyman_Service_API.postman_collection.json**
   - Complete Postman collection with 100+ API endpoints
   - Organized into 14 categories
   - Includes request examples and auto-token management

2. **Handyman_Service_Local.postman_environment.json**
   - Pre-configured environment for local development
   - Base URL set to `http://127.0.0.1:8000/api`
   - Variables for auth token and test IDs

3. **API_DOCUMENTATION.md**
   - Comprehensive API documentation
   - Endpoint descriptions and examples
   - Authentication guide
   - Response format specifications

## 🚀 Quick Setup

### Step 1: Import into Postman

1. Open Postman
2. Click **Import** button
3. Drag and drop both JSON files:
   - `Handyman_Service_API.postman_collection.json`
   - `Handyman_Service_Local.postman_environment.json`

### Step 2: Select Environment

1. In Postman, click the environment dropdown (top right)
2. Select **"Handyman Service - Local"**

### Step 3: Login

1. Navigate to **Authentication** → **Login**
2. Update the credentials if needed:
   - Email: `admin@admin.com`
   - Password: (check your database or `.env` file)
3. Click **Send**
4. The `auth_token` will be automatically saved to your environment

### Step 4: Test Endpoints

All authenticated endpoints will now use the saved token automatically!

## 📚 API Categories

### Public Endpoints (No Authentication Required)
- Authentication (Register, Login, Social Login)
- Categories & Services (Browse)
- Blogs
- Common Utilities (Countries, States, Cities)
- Sliders & Coupons

### Protected Endpoints (Authentication Required)
- User Profile Management
- Bookings
- Payments & Wallet
- Provider Management
- Dashboard & Analytics
- Shops
- Post Job Requests
- Notifications
- Loyalty Points
- Help Desk

## 🔐 Authentication

The collection uses **Bearer Token** authentication. After logging in:

1. The token is automatically extracted from the login response
2. Saved to the `auth_token` environment variable
3. Applied to all protected endpoints

## 🧪 Testing Flow

### For Customers:
1. Register → Login → Browse Services → Create Booking → Make Payment → Rate Service

### For Providers:
1. Register (as provider) → Login → Add Services → Manage Bookings → View Earnings

### For Handymen:
1. Register (as handyman) → Login → View Assigned Jobs → Update Status → Complete Jobs

## 📝 Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `base_url` | API base URL | `http://127.0.0.1:8000/api` |
| `auth_token` | Authentication token | Auto-populated after login |
| `admin_email` | Admin email for testing | `admin@admin.com` |
| `test_user_id` | Test user ID | Set manually for testing |
| `test_service_id` | Test service ID | Set manually for testing |
| `test_booking_id` | Test booking ID | Set manually for testing |

## 🌐 Production Setup

To use with production:

1. Duplicate the environment
2. Rename to "Handyman Service - Production"
3. Update `base_url` to your production domain
4. Update credentials as needed

## 💡 Tips

- **Auto-save tokens**: The login request has a test script that automatically saves the auth token
- **Organized folders**: Endpoints are grouped by functionality for easy navigation
- **Request examples**: Each request includes sample data in the body/params
- **Descriptions**: Hover over parameters to see descriptions and allowed values

## 🐛 Troubleshooting

### Token Not Saving
- Check that you're using the correct environment
- Verify the login response contains `data.api_token`
- Manually copy token from response if needed

### 401 Unauthorized
- Ensure you've logged in and token is saved
- Check token hasn't expired
- Verify you're using the correct environment

### Connection Refused
- Ensure Laravel server is running: `php artisan serve`
- Check the `base_url` in your environment matches your server

## 📖 Additional Documentation

See `API_DOCUMENTATION.md` for:
- Detailed endpoint descriptions
- Request/response examples
- Error codes
- Rate limiting information
- Best practices

## 🔄 Updates

When the API changes:
1. Re-export the collection from Postman
2. Update the documentation
3. Commit changes to version control

---

**Need Help?**
- Review the API_DOCUMENTATION.md file
- Check Laravel logs: `storage/logs/laravel.log`
- Test endpoints in Postman with detailed error messages
