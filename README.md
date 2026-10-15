# Prime Realty - Real Estate Listing Website

A complete, production-ready real estate listing website built with **Vanilla PHP**, **MySQL**, **HTML5**, **CSS3**, and **JavaScript**.

**Brand:** Thompson Realty Group | **Agent:** Kayla Thompson

---

## Features

### Public Website
- Stunning hero section with image slider (dusk/modern theme)
- Property listings with search, filters, sorting & pagination
- Detailed property pages with image gallery & lightbox
- About page with realtor profile
- Contact form with database storage
- WhatsApp floating button
- Responsive mobile-first design
- SEO-friendly markup
- Lazy-loaded images & animated statistics

### Admin Panel
- Secure login with session timeout
- Dashboard with property & message statistics
- Full property CRUD with image upload & thumbnails
- Message management (read/unread/delete/search)
- Site settings management
- CSRF protection on all forms

---

## Requirements

- PHP 8.0+ (with GD extension for thumbnails)
- MySQL 5.7+ / MariaDB
- Apache with mod_rewrite (XAMPP recommended)
- Web browser

---

## Installation (XAMPP)

### 1. Copy Project Files
Place the project in your XAMPP htdocs folder:
```
C:\xampp\htdocs\REALTOR\
```

### 2. Create Database
1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import the database schema:
   - Go to **Import** tab
   - Select `database/schema.sql`
   - Click **Go**

   **OR** run via command line:
   ```bash
   mysql -u root < database/schema.sql
   ```

### 3. Configure Database (if needed)
Edit `includes/config/database.php` if your MySQL credentials differ:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'prime_realty');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Configure App URL (if needed)
Edit `includes/config/config.php`:
```php
define('APP_URL', 'http://localhost/REALTOR');
```

### 5. Set Permissions
Ensure the uploads folder is writable:
```
assets/uploads/
```

### 6. Access the Website
- **Public Site:** http://localhost/REALTOR/
- **Admin Panel:** http://localhost/REALTOR/admin/login/

---

## Default Admin Credentials

| Field    | Value        |
|----------|--------------|
| Username | `admin`      |
| Password | `Admin@123`  |

> Change the password after first login by updating the database directly.

---

## Project Structure

```
REALTOR/
├── assets/
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   ├── images/       # Static images (hero, branding, properties)
│   └── uploads/      # User-uploaded images
├── includes/
│   ├── config/       # Database & app configuration
│   ├── functions.php # Core helper functions
│   ├── header.php    # Public site header
│   └── footer.php    # Public site footer
├── admin/
│   ├── dashboard/    # Admin dashboard
│   ├── login/        # Login & logout
│   ├── properties/   # Property management
│   ├── messages/     # Contact messages
│   └── settings/     # Site settings
├── api/              # AJAX endpoints
├── database/         # SQL schema & sample data
├── IMAGES/           # Original property photos
├── index.php         # Home page
├── listings.php      # Property listings
├── property.php      # Property details
├── about.php         # About page
├── contact.php       # Contact page
├── 404.php           # Error page
└── .htaccess         # Apache configuration
```

---

## Database Tables

- `admins` - Admin users
- `properties` - Property listings
- `property_images` - Property gallery images
- `property_types` - House, Apartment, Villa, etc.
- `states` / `cities` - Location data
- `messages` - Contact form submissions
- `testimonials` - Client testimonials
- `settings` - Site configuration (key-value)

---

## Security Features

- PDO prepared statements (SQL injection prevention)
- Output escaping with `htmlspecialchars()` (XSS prevention)
- CSRF tokens on all forms
- Password hashing with `password_hash()`
- Secure session configuration
- File upload validation (type, size, MIME)
- Session timeout (1 hour)
- Directory traversal prevention

---

## Color Palette

| Color     | Hex       | Usage              |
|-----------|-----------|--------------------|
| Primary   | `#0F4C81` | Buttons, headings  |
| Teal      | `#004B49` | Brand accent       |
| Secondary | `#1ABC9C` | Highlights         |
| Accent    | `#F39C12` | Badges, stars      |

---

## Sample Data

The database includes:
- 10 sample properties using images from the `IMAGES/` folder
- 4 client testimonials
- 6 property types
- 5 states with cities
- Default site settings for Thompson Realty Group

---

## Troubleshooting

**Database connection error:**
- Ensure MySQL is running in XAMPP
- Verify credentials in `includes/config/database.php`
- Import `database/schema.sql`

**Images not showing:**
- Check that `assets/images/properties/` contains the JPEG files
- Verify `APP_URL` in config matches your setup

**Upload errors:**
- Ensure `assets/uploads/` is writable
- Check PHP `upload_max_filesize` and `post_max_size` (min 10MB)
- Verify GD extension is enabled in PHP

**Admin login fails:**
- Default password is `Admin@123` (case-sensitive)
- Re-import schema if admin user is missing

---

## License

This project is for educational and commercial use.

---

Built with ❤️ for Thompson Realty Group
