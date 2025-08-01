# NFL Picks App Development Checklist

## Phase 1: Core MVP (6-8 weeks)

### Week 1-2: Foundation & Authentication

#### 1. Authentication Setup
- [✓] Create authentication middleware
  ```bash
  php artisan make:middleware Authenticate
  ```
- [✓] Set up basic auth routes in `routes/web.php`
- [✓] Create auth controllers
  ```bash
  php artisan make:controller Auth/LoginController
  php artisan make:controller Auth/RegisterController
  php artisan make:controller Auth/LogoutController
  ```
- [✓] Create user registration form view
- [✓] Create user login form view  
- [✓] Set up password reset functionality
- [✓] Configure session authentication for web
- [✓] Configure Sanctum token authentication for API

#### 2. Livewire Installation & Setup
- [ ] Install Livewire
  ```bash
  composer require livewire/livewire
  php artisan livewire:publish --config
  ```
- [ ] Set up main layout with Livewire scripts
- [ ] Install Alpine.js via CDN or npm
- [ ] Configure Tailwind CSS for styling
  ```bash
  npm install -D tailwindcss postcss autoprefixer
  npx tailwindcss init -p
  ```

#### 3. Database Schema Design
- [ ] Create users table migration (extend default if needed)
  ```bash
  php artisan make:migration add_fields_to_users_table
  ```
- [ ] Create groups table migration
  ```bash
  php artisan make:migration create_groups_table
  ```
- [ ] Create games table migration
  ```bash
  php artisan make:migration create_games_table
  ```
- [ ] Create picks table migration
  ```bash
  php artisan make:migration create_picks_table
  ```
- [ ] Create group_members table migration
  ```bash
  php artisan make:migration create_group_members_table
  ```
- [ ] Run migrations
  ```bash
  php artisan migrate
  ```

#### 4. Core Models
- [ ] Create Group model
  ```bash
  php artisan make:model Group
  ```
- [ ] Create Game model
  ```bash
  php artisan make:model Game
  ```
- [ ] Create Pick model
  ```bash
  php artisan make:model Pick
  ```
- [ ] Create GroupMember model
  ```bash
  php artisan make:model GroupMember
  ```
- [ ] Set up model relationships
- [ ] Add model fillable fields and validation rules

### Week 3-4: NFL Integration & Game Management

#### 5. NFL Data Service Architecture
- [ ] Create NFL data service interface
  ```bash
  php artisan make:interface Contracts/NFLDataServiceInterface
  ```
- [ ] Create Data Transfer Objects
  ```bash
  php artisan make:class DataObjects/GameData
  php artisan make:class DataObjects/TeamData
  ```
- [ ] Create service provider
  ```bash
  php artisan make:provider NFLDataServiceProvider
  ```
- [ ] Register service provider in `bootstrap/providers.php`

#### 6. ESPN API Integration with Saloon
- [ ] Create ESPN Saloon connector
  ```bash
  php artisan make:class Services/ESPN/ESPNConnector
  ```
- [ ] Create ESPN request classes
  ```bash
  php artisan make:class Services/ESPN/Requests/GetCurrentWeekGames
  php artisan make:class Services/ESPN/Requests/GetGameDetails
  php artisan make:class Services/ESPN/Requests/GetScoreboard
  ```
- [ ] Create ESPN NFL data service implementation
  ```bash
  php artisan make:class Services/ESPN/ESPNNFLDataService
  ```
- [ ] Test ESPN API integration
- [ ] Create console command to fetch current week games
  ```bash
  php artisan make:command FetchCurrentWeekGames
  ```
- [ ] Set up scheduled task for game updates

#### 7. Game Management System
- [ ] Create game seeder for testing
- [ ] Create admin interface for game management (optional)
- [ ] Set up automatic game status updates
- [ ] Create console command to update game scores
  ```bash
  php artisan make:command UpdateGameScores
  ```

### Week 5-6: Groups & Picks Core

#### 8. Group Management
- [ ] Create Group Livewire component
  ```bash
  php artisan make:livewire Groups/CreateGroup
  php artisan make:livewire Groups/ManageGroup
  php artisan make:livewire Groups/JoinGroup
  ```
- [ ] Implement group creation functionality
- [ ] Implement group invitation system (invite codes)
- [ ] Set up group member limit validation (max 20)
- [ ] Create group dashboard view
- [ ] Implement leave group functionality

#### 9. Pick Submission System
- [ ] Create Pick Livewire component
  ```bash
  php artisan make:livewire Picks/SubmitPicks
  php artisan make:livewire Picks/ViewPicks
  ```
- [ ] Create pick form with game selection
- [ ] Implement pick validation (one per game, before deadline)
- [ ] Add pick deadline enforcement (game start time)
- [ ] Create pick history view
- [ ] Implement pick editing (before deadline)

#### 10. User Interface Components
- [ ] Create main dashboard layout
- [ ] Create current week games display
- [ ] Create group leaderboard component
  ```bash
  php artisan make:livewire Leaderboard/GroupLeaderboard
  ```
- [ ] Create user profile component
  ```bash
  php artisan make:livewire User/UserProfile
  ```

### Week 7-8: Scoring & Polish

#### 11. Scoring System
- [ ] Create scoring calculation service
  ```bash
  php artisan make:class Services/ScoringService
  ```
- [ ] Implement weekly scoring logic
- [ ] Create console command for score calculation
  ```bash
  php artisan make:command CalculateWeeklyScores
  ```
- [ ] Set up automated scoring after games complete
- [ ] Create leaderboard calculation logic

#### 12. Email Notifications
- [ ] Set up mail configuration
- [ ] Create notification classes
  ```bash
  php artisan make:notification PicksDeadlineReminder
  php artisan make:notification WeeklyResults
  php artisan make:notification GroupInvitation
  ```
- [ ] Set up queue system for emails
  ```bash
  php artisan queue:table
  php artisan migrate
  ```
- [ ] Schedule deadline reminder emails
- [ ] Create weekly results email

#### 13. API Endpoints for Mobile
- [ ] Create API controllers
  ```bash
  php artisan make:controller Api/AuthController
  php artisan make:controller Api/GameController
  php artisan make:controller Api/GroupController
  php artisan make:controller Api/PickController
  ```
- [ ] Set up API routes in `routes/api.php`
- [ ] Create API resources for JSON responses
  ```bash
  php artisan make:resource GameResource
  php artisan make:resource GroupResource
  php artisan make:resource PickResource
  ```
- [ ] Test API endpoints
- [ ] Create API documentation

#### 14. Testing & Bug Fixes
- [ ] Create feature tests for core functionality
  ```bash
  php artisan make:test GroupManagementTest
  php artisan make:test PickSubmissionTest
  php artisan make:test ScoringTest
  ```
- [ ] Create unit tests for services
- [ ] Set up test database
- [ ] Run comprehensive testing
- [ ] Fix bugs and edge cases
- [ ] Performance optimization

#### 15. Final Polish
- [ ] Responsive design testing
- [ ] Cross-browser compatibility
- [ ] Error handling and user feedback
- [ ] Loading states and animations
- [ ] Security review and CSRF protection
- [ ] Environment configuration for production
- [ ] Deployment preparation

## Phase 2: Enhancement Features (Future)
- [ ] Advanced group management
- [ ] Historical data/season tracking  
- [ ] Mobile app development
- [ ] Enhanced UI/animations
- [ ] Push notifications
- [ ] Social features (comments, trash talk)
- [ ] Multiple scoring systems
- [ ] Playoff brackets

---

## Notes
- Check off items as you complete them
- Adjust timeline based on your development pace
- Some tasks can be done in parallel
- Consider creating git branches for major features
