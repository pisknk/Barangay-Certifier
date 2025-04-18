# Tenant Users API Documentation

This document describes the implementation of the Tenant Users CRUD API, which allows management of users within each tenant's database.

## Database Structure

Each tenant has its own database with a `tenant_users` table that contains the following schema:

- `id` - Auto-incrementing primary key
- `name` - User's full name
- `email` - User's email address (unique)
- `password` - Hashed password
- `role` - User role (admin or user)
- `position` - User's position (optional)
- `phone` - User's phone number (optional)
- `remember_token` - Token for "remember me" functionality
- `created_at` - Timestamp of creation
- `updated_at` - Timestamp of last update

## API Endpoints

### Tenant User CRUD Operations

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/users` | GET | Get all users in the tenant |
| `/api/users` | POST | Create a new user in the tenant |
| `/api/users/{id}` | GET | Get a specific user by ID |
| `/api/users/{id}` | PUT | Update a user's information |
| `/api/users/{id}` | DELETE | Delete a user by ID |

### Testing Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/test/users` | GET | Test tenant users functionality |
| `/api/test/create-user` | POST | Create a test user for development |
| `/api/health-check` | GET | Check API health and tenant identification |

## Testing with Postman

1. Import the `postman_collection.json` file into Postman
2. Set the `tenant_domain` variable to your tenant's domain (e.g., `yourtenant.localhost:8000`)
3. Test the API health by sending a GET request to `/api/health-check`
4. Create a test user using the `/api/test/create-user` endpoint
5. Perform CRUD operations using the provided endpoints

### Sample Requests

#### Create User
```json
POST /api/users
{
    "name": "Neil MArc Bayron",
    "email": "nbayron29@gmail.com",
    "password": "password123",
    "role": "user",
    "position": "Barangay TAnod",
    "phone": "9876543210"
}
```

#### Update User
```json
PUT /api/users/1
{
    "name": "John Doe Updated",
    "position": "Senior Staff",
    "phone": "9876543211"
}
```

## Implementation Details

The Tenant Users API is implemented with the following components:

1. **TenantUser Model** - Eloquent model for tenant users
2. **TenantUserController** - Controller handling CRUD operations
3. **TenantUserRequest** - Form request class for validation
4. **Tenant API Routes** - API routes defined in `routes/tenant.php`
5. **Test Controller** - Helper endpoints for testing functionality

## Next Steps

- Implement authentication for the API
- Add pagination to the user listing
- Implement role-based permissions
- Create a user interface for tenant user management 