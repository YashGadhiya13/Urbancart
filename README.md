# UrbanCart

UrbanCart is a dynamic e-commerce web application developed using HTML, CSS, JavaScript, PHP, and MySQL. The project provides an online shopping interface for customers while also providing server-side functionality for managing website data and administrative operations.

## Project Overview

UrbanCart demonstrates the development of a full-stack web application by combining frontend technologies, PHP backend processing, and a MySQL database.

The website includes customer-facing pages for browsing products, viewing product information, managing a shopping cart, completing checkout, and accessing general website information.

## Technologies Used

### Frontend
- HTML5
- CSS3
- JavaScript
- Responsive Web Design

### Backend
- PHP
- Server-side form processing
- Authentication and session-related functionality

### Database
- MySQL
- PHP and MySQL integration

## Main Features

- Home page
- Product/shop page
- Individual product pages
- Shopping cart
- Checkout
- User authentication
- Member functionality
- Admin dashboard
- Product management
- User management
- Product gallery
- Contact page
- About page
- Privacy page
- Responsive interface

## Project Structure

```text
urbancart/
│
├── admin/
│   ├── products/
│   ├── users/
│   └── dashboard.php
│
├── assets/
│   ├── css/
│   │   ├── dashboard.css
│   │   ├── responsive.css
│   │   └── style.css
│   │
│   ├── images/
│   │
│   └── js/
│       ├── cart.js
│       ├── dashboard.js
│       ├── form-validation.js
│       ├── gallery.js
│       └── script.js
│
├── auth/
├── database/
├── includes/
├── member/
│
├── .gitignore
├── .htaccess
├── about.php
├── cart.php
├── checkout.php
├── contact.php
├── gallery.php
├── index.php
├── privacy.php
├── product.php
├── robots.txt
├── shop.php
├── sitemap.php
└── README.md
```

## Frontend

The frontend is responsible for the visual appearance and user interaction of UrbanCart.

CSS files are stored inside:

```text
assets/css/
```

JavaScript files are stored inside:

```text
assets/js/
```

Images and other visual resources are stored inside:

```text
assets/images/
```

The main customer-facing pages include `index.php`, `shop.php`, `product.php`, `cart.php`, `checkout.php`, `gallery.php`, `about.php`, and `contact.php`.

## Backend

PHP is used for server-side functionality.

Important backend directories include:

```text
admin/
auth/
includes/
member/
```

The admin section contains functionality related to products, users, and the administrative dashboard.

## Database

MySQL is used as the database management system for UrbanCart.

Database-related files and configuration are organised inside:

```text
database/
```

The database layer is used by the PHP backend to store and retrieve application information.

## Running the Project Locally

### Requirements

Install:

- XAMPP
- Visual Studio Code
- Web browser such as Google Chrome

### Step 1 - Copy the Project

Place the `urbancart` project folder inside the XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\urbancart
```

### Step 2 - Start XAMPP

Open the XAMPP Control Panel and start:

```text
Apache
MySQL
```

### Step 3 - Configure the Database

Open phpMyAdmin from your local XAMPP environment.

Create the required MySQL database and import the project's SQL/database structure if required.

Make sure the PHP database configuration matches your local MySQL settings.

Typical local XAMPP settings are:

```text
Host: localhost
Username: root
Password: [blank by default]
```

### Step 4 - Run UrbanCart

Open the project through your local Apache server:

```text
http://localhost/urbancart/
```

The main `index.php` page should load automatically.

### Admin Area

The admin dashboard can be accessed locally at:

```text
http://localhost/urbancart/admin/dashboard.php
```

## Application Architecture

UrbanCart follows a basic three-layer web application structure:

```text
User
  ↓
Frontend
HTML + CSS + JavaScript
  ↓
Backend
PHP
  ↓
Database
MySQL
```

The frontend handles presentation and interaction, PHP performs server-side processing, and MySQL stores persistent application data.

## Security

The project should follow standard web-development security practices, including:

- Server-side input validation
- Secure password handling
- Protection against SQL injection
- Authentication and session management
- Appropriate access control for administrative functionality

## Future Improvements

Possible future improvements include:

- Online payment gateway integration
- Advanced product search and filtering
- Product reviews and ratings
- Order tracking
- Wishlist functionality
- Email notifications
- Improved admin analytics
- Enhanced security and validation

## Academic Project

This project was developed as part of:

**ICT726 - Web Development**

The purpose of UrbanCart is to demonstrate practical understanding of frontend development, PHP server-side programming, database integration, and full-stack web application development.

## Author

**Yashkumar Nileshbhai Gadhiya**

**Student ID:** 20034880
