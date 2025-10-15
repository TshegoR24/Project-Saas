# Budget SaaS

A comprehensive budget management SaaS application built with Laravel that helps users track expenses, manage subscriptions, and monitor payments.

## 🚀 Features

- **User Authentication & Authorization** - Secure user registration, login, and profile management
- **Expense Tracking** - Record and categorize personal expenses with date tracking
- **Subscription Management** - Track recurring subscriptions with different billing cycles (monthly/yearly)
- **Payment Tracking** - Monitor payment status and upcoming payments
- **Dashboard Analytics** - Visual overview of spending patterns, subscription costs, and financial summaries
- **Responsive Design** - Modern UI built with Tailwind CSS and Alpine.js

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: Blade templates with Tailwind CSS
- **JavaScript**: Alpine.js for interactive components
- **Database**: SQLite (development)
- **Build Tool**: Vite
- **Authentication**: Laravel Breeze

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Project-Saas/budget-saas
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # The SQLite database file should already exist
   # If not, create it:
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

## 🚀 Running the Application

### Development Mode
```bash
# Start all services (Laravel server, queue worker, logs, and Vite)
composer run dev

# Or start services individually:
php artisan serve          # Laravel server (http://localhost:8000)
php artisan queue:listen   # Queue worker
php artisan pail           # Log viewer
npm run dev               # Vite dev server
```

### Production Mode
```bash
# Build optimized assets
npm run build

# Start Laravel server
php artisan serve
```

## 📁 Project Structure

```
budget-saas/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   │   ├── Auth/            # Authentication controllers
│   │   ├── DashboardController.php
│   │   ├── ExpenseController.php
│   │   ├── SubscriptionController.php
│   │   └── PaymentController.php
│   ├── Models/              # Eloquent models
│   │   ├── User.php
│   │   ├── Expense.php
│   │   ├── Subscription.php
│   │   └── Payment.php
│   └── Policies/            # Authorization policies
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/            # Database seeders
│   └── database.sqlite     # SQLite database
├── resources/
│   ├── views/              # Blade templates
│   │   ├── auth/           # Authentication views
│   │   ├── components/     # Reusable components
│   │   ├── dashboard.blade.php
│   │   ├── expenses/       # Expense management views
│   │   ├── subscriptions/  # Subscription views
│   │   └── payments/       # Payment views
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript files
├── routes/
│   ├── web.php             # Web routes
│   └── auth.php            # Authentication routes
└── tests/                  # Test suites
```

## 🗄️ Database Schema

### Users
- `id`, `name`, `email`, `password`, `remember_token`
- `email_verified_at`, `created_at`, `updated_at`

### Expenses
- `id`, `user_id`, `category`, `amount`, `date`
- `created_at`, `updated_at`

### Subscriptions
- `id`, `user_id`, `name`, `amount`, `billing_cycle`
- `next_due_date`, `created_at`, `updated_at`

### Payments
- `id`, `user_id`, `subscription_id`, `amount`, `status`
- `created_at`, `updated_at`

## 🎯 Key Features Explained

### Expense Management
- Create, read, update, and delete expenses
- Categorize expenses for better organization
- Filter expenses by category, date range, and amount
- Monthly expense summaries

### Subscription Tracking
- Add recurring subscriptions with billing cycles
- Support for monthly and yearly billing
- Track next due dates
- Calculate monthly subscription costs

### Dashboard Analytics
- Monthly expense totals
- Subscription cost breakdown
- Recent expense history
- Upcoming payments
- Expense category charts

### User Authentication
- Secure registration and login
- Email verification
- Password reset functionality
- Profile management

## 🧪 Testing

```bash
# Run all tests
composer run test

# Run tests with coverage
php artisan test --coverage
```

## 🔒 Security Features

- CSRF protection
- SQL injection prevention via Eloquent ORM
- XSS protection with Blade templating
- Password hashing
- Route middleware for authentication
- Authorization policies for data access

## 📝 API Endpoints

### Authentication Routes
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout
- `POST /forgot-password` - Password reset request
- `POST /reset-password` - Password reset

### Application Routes
- `GET /dashboard` - Main dashboard
- `GET /profile` - User profile
- `PATCH /profile` - Update profile
- `DELETE /profile` - Delete account

### Resource Routes
- `GET /expenses` - List expenses
- `POST /expenses` - Create expense
- `GET /expenses/{id}` - Show expense
- `PUT /expenses/{id}` - Update expense
- `DELETE /expenses/{id}` - Delete expense

Similar resource routes exist for `subscriptions` and `payments`.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

If you encounter any issues or have questions, please:

1. Check the [Laravel documentation](https://laravel.com/docs)
2. Search existing issues in the repository
3. Create a new issue with detailed information about your problem

## 🔄 Version History

- **v1.0.0** - Initial release with core budget management features
  - User authentication
  - Expense tracking
  - Subscription management
  - Payment tracking
  - Dashboard analytics

---

Built with ❤️ using Laravel
