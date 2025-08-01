# NFL Picks App

A competitive NFL game prediction application where users can create groups and compete to see who has the best record at picking weekly NFL game winners.

## Features

### Current Features (MVP - Phase 1)
- **User Authentication**: Registration, login, and profile management
- **Group Management**: Create and join groups of up to 20 users
- **Weekly Picks**: Submit predictions for current week's NFL games
- **Real-time Scoring**: Automatic scoring after games complete
- **Leaderboards**: Track performance within groups
- **Email Notifications**: Deadline reminders and weekly results

### Planned Features (Phase 2)
- **Mobile App**: Native mobile application with API backend
- **Historical Tracking**: Season-long statistics and history
- **Advanced Group Features**: Enhanced management and social features
- **Multiple Scoring Systems**: Different ways to calculate points
- **Playoff Brackets**: Tournament-style competitions

## Technology Stack

- **Backend**: Laravel 12 with PHP 8.3+
- **Frontend**: Laravel Livewire + Alpine.js + Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **API Integration**: ESPN NFL API via Laravel Saloon
- **Authentication**: Laravel Sanctum (API) + Session (Web)
- **Email**: Laravel Mail with queue system
- **Deployment**: TBD

## Architecture

The application uses a clean architecture approach with:
- **Service Layer**: Business logic abstraction
- **Repository Pattern**: Data access abstraction  
- **Interface-based NFL Data**: Pluggable API integrations
- **DTO Objects**: Structured data transfer
- **Event-driven**: Email notifications and scoring updates

## Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL 13+

### Setup
```bash
# Clone the repository
git clone <repository-url>
cd nfl-picks-app

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nfl_picks
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run database migrations
php artisan migrate

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

### Development
```bash
# Run frontend with hot reloading
npm run dev

# Run queue worker for email processing
php artisan queue:work

# Run scheduled tasks (for game updates)
php artisan schedule:work
```

## API Integration

The app integrates with ESPN's NFL API to fetch:
- Current week's game schedule
- Live game scores and updates
- Team information

The integration uses Laravel Saloon with an interface-based architecture, making it easy to switch between different NFL data providers or combine multiple sources.

## Database Schema

### Core Tables
- `users` - User accounts and profiles
- `groups` - Pick groups with settings
- `group_members` - Many-to-many relationship for group membership
- `games` - NFL games with teams, dates, and scores
- `picks` - User predictions for specific games

## Contributing

### Development Workflow
1. Create feature branch from `main`
2. Follow the development checklist in `/docs/checklist.md`
3. Write tests for new functionality
4. Submit pull request with clear description

### Code Standards
- Follow Laravel coding standards
- Use PHP-CS-Fixer for code formatting
- Write PHPDoc comments for public methods
- Include tests for new features

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Generate coverage report
php artisan test --coverage
```

## Deployment

### Environment Setup
- Set `APP_ENV=production`
- Configure production database
- Set up queue worker service
- Configure cron for scheduled tasks
- Set up SSL certificate

### Commands
```bash
# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Build production assets
npm run build
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Development Status

🚧 **Currently in Development** - MVP Phase 1

See the [development checklist](/docs/checklist.md) for current progress and upcoming features.

---

**Current Version**: 0.1.0 (MVP Development)  
**Laravel Version**: 12.x  
**PHP Version**: 8.3+he Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
