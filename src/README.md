# User Management API

A robust Laravel-based REST API for user management with some features including user registration, listing, filtering, and role-based permissions.

## 🚀 Features

- **User Registration** - Create new users with email verification
- **User Listing** - Paginated user list with advanced filtering
- **Search Functionality** - Search users by name or email
- **Sorting Options** - Sort by name, email, or creation date
- **Role-Based Permissions** - Administrator, Manager, and User roles
- **Order Tracking** - Track order counts per user
- **Email Notifications** - Automated welcome and admin notification emails
- **Rate Limiting** - Protection against API abuse
- **Input Validation** - Comprehensive request validation

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Laravel 11.x

## 🛠️ Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd <project-directory>
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Configure mail settings for email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
MAIL_ADMIN_EMAIL="admin@example.com"
```

### 4. Database setup

```bash
# Run migrations
php artisan migrate

# Seed database with test data
php artisan db:seed
```

### 5. Start the development server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 📡 API Endpoints

### 1. Create User

Create a new user account with automatic email notifications.

**Endpoint:** `POST /api/users`

**Rate Limit:** 10 requests per minute

**Request Body:**

```json
{
  "email": "john.doe@example.com",
  "password": "SecurePass123!@#",
  "name": "John Doe"
}
```

**Validation Rules:**

- `email`: Required, valid email format (RFC + DNS validation), unique, max 255 characters
- `password`: Required, minimum 8 characters, must contain uppercase, lowercase, number, and special character
- `name`: Required, 3-50 characters, letters and spaces only

**Success Response (201):**

```json
{
  "id": 123,
  "email": "john.doe@example.com",
  "name": "John Doe",
  "created_at": "2024-11-25T12:34:56Z"
}
```

**Error Response (422):**

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["Password must contain at least one uppercase letter."]
  }
}
```

### 2. Get Users

Retrieve a paginated list of active users with filtering and sorting options.

**Endpoint:** `GET /api/users`

**Rate Limit:** 60 requests per minute

**Query Parameters:**

| Parameter | Type    | Required | Default      | Description                           |
|-----------|---------|----------|--------------|---------------------------------------|
| `search`  | string  | No       | -            | Search by name or email               |
| `page`    | integer | No       | 1            | Page number for pagination            |
| `sortBy`  | string  | No       | `created_at` | Sort by: `name`, `email`, `created_at`|

**Example Requests:**

```bash
# Get first page
GET /api/users

# Search users
GET /api/users?search=john

# Sort by name
GET /api/users?sortBy=name

# Combined filters
GET /api/users?search=doe&sortBy=email&page=2
```

**Success Response (200):**

```json
{
  "page": 1,
  "users": [
    {
      "id": 123,
      "email": "john.doe@example.com",
      "name": "John Doe",
      "role": "user",
      "created_at": "2024-11-25T12:34:56Z",
      "orders_count": 10,
      "can_edit": true
    },
    {
      "id": 124,
      "email": "jane.smith@example.com",
      "name": "Jane Smith",
      "role": "manager",
      "created_at": "2024-11-24T11:20:30Z",
      "orders_count": 5,
      "can_edit": false
    }
  ]
}
```

**Pagination:** 15 users per page

## 👥 User Roles & Permissions

The system supports three user roles with different permission levels:

### Administrator
- Can edit **any user**

### Manager
- Can only edit users with role `user`
- Cannot edit administrators or other managers

### User
- Can only edit **themselves**

The `can_edit` field in the API response indicates whether the current user has permission to edit each specific user.

## 📧 Email Notifications

When a new user is created, the system automatically sends:

1. **Welcome Email** - Sent to the new user confirming account creation
2. **Admin Notification** - Sent to system administrator about new registration

Email notifications are queued for asynchronous processing to improve API response time.

## 🗄️ Database Schema

### Users Table

| Column      | Type         | Description                          |
|-------------|--------------|--------------------------------------|
| `id`        | BIGINT       | Primary key, auto-increment          |
| `email`     | VARCHAR(255) | Unique email address                 |
| `password`  | VARCHAR(255) | Hashed password                      |
| `name`      | VARCHAR(255) | User's full name                     |
| `role`      | ENUM         | `user`, `manager`, `administrator`   |
| `active`    | BOOLEAN      | Account status (default: true)       |
| `created_at`| TIMESTAMP    | Account creation timestamp           |

### Orders Table

| Column      | Type         | Description                          |
|-------------|--------------|--------------------------------------|
| `id`        | BIGINT       | Primary key, auto-increment          |
| `user_id`   | BIGINT       | Foreign key to users.id              |
| `created_at`| TIMESTAMP    | Order creation timestamp             |

## 🧪 Testing with Seeded Data

The database seeder creates comprehensive test data:

- **1 Administrator** - Full system access
- **3 Managers** - Medium-level permissions
- **15 Active Users** - Standard accounts
- **3 Inactive Users** - For testing filters
- **Variable Orders** - 3-30 orders per user based on role

**Run seeder:**

```bash
php artisan db:seed
```

## 🏗️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── UserController.php       # API endpoints
│   ├── Requests/
│   │   ├── CreateUserRequest.php        # User creation validation
│   │   └── GetUsersRequest.php          # User listing validation
│   └── Resources/
│       ├── UserResource.php             # Single user response
│       └── UserCollectionResource.php   # User list response
├── Models/
│   ├── User.php                         # User model with scopes
│   └── Order.php                        # Order model
├── Services/
│   └── UserService.php                  # Business logic layer
└── Mail/
    ├── WelcomeNewUser.php               # Welcome email
    └── NewUserNotification.php          # Admin notification

database/
├── migrations/
│   ├── xxxx_create_users_table.php
│   └── xxxx_create_orders_table.php
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── UserSeeder.php
│   └── OrderSeeder.php
└── factories/
    ├── UserFactory.php
    └── OrderFactory.php
```

## 🔒 Security Features

- **Password Hashing** - Bcrypt encryption for all passwords
- **Rate Limiting** - Prevents API abuse (10 req/min for registration, 60 req/min for listing)
- **Input Validation** - Comprehensive validation on all inputs
- **SQL Injection Protection** - Eloquent ORM with parameter binding
- **XSS Protection** - Output escaping via API resources
- **Email Validation** - RFC and DNS validation
- **Password Complexity** - Enforced strong password requirements

## 🎯 Best Practices Implemented

- **Repository Pattern** - Clean separation of concerns
- **Service Layer** - Business logic isolated from controllers
- **Query Scopes** - Reusable query builders in models
- **API Resources** - Consistent JSON response formatting
- **Form Requests** - Dedicated validation classes
- **Database Transactions** - Ensuring data integrity
- **Queue Jobs** - Asynchronous email processing
- **Logging** - Comprehensive error and activity logging
- **Rate Limiting** - API abuse prevention

## 🐛 Error Handling

All API endpoints return consistent error responses:

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**Server Error (500):**
```json
{
  "success": false,
  "message": "Failed to create user. Please try again later.",
  "error": "Detailed error message (only in debug mode)"
}
```

## 📝 Example Usage

### Using cURL

**Create a user:**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "SecurePass123!@#",
    "name": "Test User"
  }'
```

**Get users:**
```bash
curl -X GET "http://localhost:8000/api/users?search=john&sortBy=name&page=1"
```

### Using Postman

1. Import the endpoints into Postman
2. Set base URL: `http://localhost:8000/api`
3. Use the examples above as request templates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request


---

**Built with ❤️ using Laravel**
