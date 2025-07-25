# Travel and Tour Management System

## Overview
This is a web-based Travel and Tour Management System with AI-powered travel recommendations. It allows users to browse and book travel packages, interact with an AI assistant, and manage their bookings. Administrators can manage tours, bookings, users, and view analytics.

## Technologies Used
- Frontend: HTML, CSS, JavaScript
- Backend: PHP, MySQL
- AI: Basic rule-based AI assistant (can be extended)
- Charts: Chart.js for admin analytics

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL or MariaDB
- Web server (e.g., Apache, Nginx) or PHP built-in server

### Database Setup
1. Create the database and tables by r1unning the SQL script:
   ```bash
   mysql -u your_username -p < database_setup.sql
   ```
   Replace `your_username` with your MySQL username.

2. Update database credentials in `php/config.php` if needed.

### Running the Application
1. Place the project files in your web server's root directory or use PHP's built-in server:
   ```bash
   php -S localhost:8000
   ```
   Run this command in the project root directory.

2. Access the application in your browser at `http://localhost:8000`.

### User Registration and Login
- Register a new user via `register.html`.
- Login via `login.html`.
- After login, access your dashboard, book tours, and manage your profile.

### Admin Panel
- Admin login is not implemented by default. You can create an admin user manually in the database and set a session variable `admin_logged_in` to `true` for testing.
- Access admin panel pages under the `admin/` directory.

### AI Assistant
- The AI assistant backend API is at `php/ai_assistant.php`.
- Frontend chat integration is a placeholder and can be extended.

### Email Notifications
- Basic email utility is in `php/send_email.php`.
- Configure your mail server or SMTP settings as needed.

## Future Enhancements
- Full AI assistant integration with machine learning.
- Payment gateway integration.
- Multilingual and multi-currency support.
- Mobile app and AR features.

## Contact
For any questions or support, please contact the project team.