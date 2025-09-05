# FreelanceHub - Freelance Marketplace Website

This project is a complete, feature-rich freelance marketplace website built with PHP and MySQL. It provides a platform for clients to post projects and for freelancers to bid on them.

## Features

- **User Roles:** Separate registration and dashboards for Clients, Freelancers, and Administrators.
- **Project Management:** Clients can post, manage, and award projects.
- **Bidding System:** Freelancers can browse projects and place bids with detailed proposals.
- **User Profiles:** Public user profiles with bios, skills, and review ratings.
- **Real-time Communication:** Built-in messaging system for users to communicate.
- **Notifications:** Real-time notifications for key events like new bids or messages.
- **Review System:** A 5-star rating and comment system for completed projects.
- **Financial Tracking:** Separate pages for clients to track payments and freelancers to track earnings.
- **Admin Panel:** A comprehensive admin dashboard to manage users, projects, categories, and payments.
- **Modern UI:** Clean and responsive user interface built with Bootstrap.

## Project Structure

```
freelance-marketplace/
├── admin_dashboard.php
├── api/
│   └── notifications.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── bid_project.php
├── chat.php
├── config/
│   └── database.php
├── dashboard.php
├── earnings.php
├── index.php
├── login.php
├── logout.php
├── manage_categories.php
├── manage_payments.php
├── manage_projects.php
├── manage_users.php
├── messages.php
├── my_bids.php
├── my_projects.php
├── notifications.php
├── partials/
│   ├── footer.php
│   └── header.php
├── payments.php
├── post_project.php
├── profile.php
├── project_detail.php
├── project_list.php
├── README.md
├── register.php
├── review.php
└── reviews.php
```

## Setup Instructions

1.  **Web Server:** You need a web server with PHP support (like Apache or Nginx) and a MySQL database server.
2.  **Database:**
    -   Create a new MySQL database named `freelance_marketplace`.
    -   Import the `database.sql` file (not included, but would contain the schema) into this database.
    -   Update the database credentials (`DB_USER`, `DB_PASS`) in `config/database.php` if they are different from the defaults ('root' and '').
3.  **Files:** Place all the project files in the root directory of your web server (e.g., `htdocs` or `www`).
4.  **Admin User:** You will need to manually create an admin user in the `users` table by setting the `role` column to 'admin'.
5.  **Run:** Open the project in your web browser.

### Required Database Schema (Example)

You will need to create tables for `users`, `projects`, `categories`, `bids`, `messages`, `notifications`, `reviews`, and `payments`. The PHP files in this project are coded based on a schema containing these tables.
