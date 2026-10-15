-- Prime Realty / Thompson Realty Group Database Schema
-- Run: mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS prime_realty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE prime_realty;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS property_images;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS cities;
DROP TABLE IF EXISTS states;
DROP TABLE IF EXISTS property_types;
DROP TABLE IF EXISTS admins;
SET FOREIGN_KEY_CHECKS = 1;

-- Admins
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Property Types
CREATE TABLE property_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- States
CREATE TABLE states (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Cities
CREATE TABLE cities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    state_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
    UNIQUE KEY unique_city_state (name, state_id)
) ENGINE=InnoDB;

-- Properties
CREATE TABLE properties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(15,2) NOT NULL DEFAULT 0,
    property_type_id INT UNSIGNED NOT NULL,
    status ENUM('available','sold','pending') NOT NULL DEFAULT 'available',
    description TEXT,
    short_description VARCHAR(500),
    bedrooms INT UNSIGNED DEFAULT 0,
    bathrooms INT UNSIGNED DEFAULT 0,
    kitchen INT UNSIGNED DEFAULT 0,
    garage INT UNSIGNED DEFAULT 0,
    parking INT UNSIGNED DEFAULT 0,
    land_size VARCHAR(50),
    year_built YEAR,
    address VARCHAR(255),
    state_id INT UNSIGNED NOT NULL,
    city_id INT UNSIGNED NOT NULL,
    zip_code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    amenities TEXT,
    nearby_places TEXT,
    featured_image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    views INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_type_id) REFERENCES property_types(id),
    FOREIGN KEY (state_id) REFERENCES states(id),
    FOREIGN KEY (city_id) REFERENCES cities(id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Property Images
CREATE TABLE property_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    is_cover TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contact Messages
CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read)
) ENGINE=InnoDB;

-- Testimonials
CREATE TABLE testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_title VARCHAR(100),
    content TEXT NOT NULL,
    rating TINYINT UNSIGNED DEFAULT 5,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Site Settings (key-value)
CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default Admin (password: Admin@123)
INSERT INTO admins (username, email, password, full_name) VALUES
('admin', 'admin@thompsonrealtygroup.com', '$2y$10$Xl1wtYfO34X6peSfOTdn9ect1Npug27.f54ZtaYAYro.DTuPliJhC', 'Kayla Thompson');

-- Property Types
INSERT INTO property_types (name, slug) VALUES
('House', 'house'),
('Apartment', 'apartment'),
('Villa', 'villa'),
('Condo', 'condo'),
('Townhouse', 'townhouse'),
('Land', 'land');

-- States
INSERT INTO states (name, code) VALUES
('Vermont', 'VT'),
('California', 'CA'),
('Texas', 'TX'),
('Florida', 'FL'),
('New York', 'NY');

-- Cities
INSERT INTO cities (state_id, name) VALUES
(1, 'Burlington'), (1, 'Montpelier'), (1, 'Stowe'),
(2, 'Los Angeles'), (2, 'San Francisco'),
(3, 'Austin'), (3, 'Houston'),
(4, 'Miami'), (4, 'Orlando'),
(5, 'New York City'), (5, 'Albany');

-- Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Thompson Realty Group'),
('site_tagline', 'Find Your Dream Property'),
('logo', 'assets/images/branding/logo.svg'),
('hero_banner', 'assets/images/hero/hero-banner.png'),
('contact_phone', '+1 (802) 289-2563'),
('whatsapp_number', '18022892563'),
('contact_email', 'Kaylathompson@gmail.com'),
('office_address', '123 Main Street, Burlington, VT 05401'),
('working_hours', 'Mon - Fri: 9:00 AM - 6:00 PM | Sat: 10:00 AM - 4:00 PM'),
('facebook', 'https://facebook.com/thompsonrealtygroup'),
('instagram', 'https://instagram.com/thompsonrealtygroup'),
('linkedin', 'https://linkedin.com/company/thompsonrealtygroup'),
('tiktok', 'https://tiktok.com/@thompsonrealtygroup'),
('footer_text', 'Thompson Realty Group — Your trusted partner in finding the perfect property. Licensed Real Estate Professional.'),
('meta_title', 'Thompson Realty Group | Premium Real Estate Listings'),
('meta_description', 'Discover exceptional properties with Thompson Realty Group. Browse luxury homes, apartments, and villas. Contact Kayla Thompson today.'),
('agent_name', 'Kayla Thompson'),
('agent_title', 'Real Estate Agent'),
('agent_bio', 'With over 15 years of experience in the Vermont real estate market, Kayla Thompson has helped hundreds of families find their dream homes. Her dedication, market expertise, and personalized approach make her one of the most trusted agents in the region.'),
('agent_photo', 'assets/images/branding/agent-card.png'),
('agent_experience', '15+ Years'),
('agent_achievements', 'Top Producer 2024, 500+ Homes Sold, Certified Luxury Home Marketing Specialist');

-- Testimonials
INSERT INTO testimonials (client_name, client_title, content, rating) VALUES
('Sarah & Michael Johnson', 'Home Buyers', 'Kayla made our home buying experience seamless. Her knowledge of the local market and attention to detail helped us find the perfect family home in Burlington.', 5),
('David Chen', 'Property Investor', 'Professional, responsive, and incredibly knowledgeable. Thompson Realty Group exceeded our expectations on every investment property we purchased.', 5),
('Emily Rodriguez', 'First-Time Buyer', 'As a first-time buyer, I was nervous about the process. Kayla guided me every step of the way with patience and expertise. Highly recommended!', 5),
('Robert Williams', 'Seller', 'Our home sold above asking price in just two weeks. The marketing strategy and staging advice were outstanding.', 5);

-- Sample Properties (using images from assets/images/properties/)
INSERT INTO properties (title, slug, price, property_type_id, status, description, short_description, bedrooms, bathrooms, kitchen, garage, parking, land_size, year_built, address, state_id, city_id, zip_code, amenities, nearby_places, featured_image, is_featured) VALUES
('Modern Lakeside Villa', 'modern-lakeside-villa', 875000.00, 3, 'available', 'Stunning contemporary villa with panoramic lake views. Features open-concept living, floor-to-ceiling windows, and premium finishes throughout. The gourmet kitchen boasts quartz countertops and high-end appliances. Master suite includes a spa-like bathroom and private balcony overlooking the water.', 'Panoramic lake views with contemporary luxury finishes.', 4, 3, 1, 2, 4, '0.75 acres', 2022, '45 Lakeview Drive', 1, 1, '05401', 'Swimming Pool,Smart Home,Fireplace,Central AC,Hardwood Floors,Walk-in Closets', 'Schools 0.5mi,Shopping 1.2mi,Parks 0.3mi', 'assets/images/properties/1.jpeg', 1),
('Downtown Luxury Apartment', 'downtown-luxury-apartment', 425000.00, 2, 'available', 'Sophisticated downtown apartment in the heart of Burlington. Enjoy city skyline views from your private terrace. Building amenities include fitness center, rooftop lounge, and 24/7 concierge service.', 'City living at its finest with premium building amenities.', 2, 2, 1, 1, 2, '1200 sq ft', 2020, '88 Church Street', 1, 1, '05401', 'Gym,Concierge,Rooftop Terrace,In-unit Laundry,Stainless Appliances', 'Restaurants 0.1mi,Transit 0.2mi,Shopping 0.3mi', 'assets/images/properties/2.jpeg', 1),
('Colonial Family Home', 'colonial-family-home', 625000.00, 1, 'available', 'Classic New England colonial on a tree-lined street. Four spacious bedrooms, updated kitchen, and a beautifully landscaped backyard perfect for entertaining. Original hardwood floors and crown molding throughout.', 'Charming colonial with modern updates and large yard.', 4, 2, 1, 2, 3, '0.5 acres', 1998, '212 Maple Avenue', 1, 2, '05602', 'Fireplace,Deck,Garden,Updated Kitchen,Hardwood Floors', 'Schools 0.4mi,Parks 0.2mi,Library 0.6mi', 'assets/images/properties/3.jpeg', 1),
('Mountain Retreat Cabin', 'mountain-retreat-cabin', 395000.00, 1, 'available', 'Cozy mountain retreat nestled in the Green Mountains. Perfect weekend getaway or year-round residence. Features vaulted ceilings, stone fireplace, and wraparound deck with mountain views.', 'Serene mountain escape with breathtaking views.', 3, 2, 1, 1, 2, '2 acres', 2015, '78 Mountain Road', 1, 3, '05672', 'Fireplace,Deck,Mountain Views,Wood Stove,Hiking Trails', 'Ski Resort 5mi,Hiking 0.1mi,Lake 2mi', 'assets/images/properties/4.jpeg', 0),
('Waterfront Contemporary', 'waterfront-contemporary', 1250000.00, 3, 'available', 'Architectural masterpiece on private waterfront. Glass walls blur the line between indoor and outdoor living. Infinity pool, private dock, and smart home technology throughout.', 'Architectural waterfront masterpiece with infinity pool.', 5, 4, 1, 3, 6, '1.2 acres', 2023, '1 Shoreline Boulevard', 1, 1, '05401', 'Infinity Pool,Private Dock,Smart Home,Wine Cellar,Home Theater', 'Marina 0.5mi,Beach 0.2mi,Fine Dining 1mi', 'assets/images/properties/5.jpeg', 1),
('Urban Loft Space', 'urban-loft-space', 310000.00, 2, 'available', 'Industrial-chic loft with exposed brick, soaring ceilings, and oversized windows. Open floor plan ideal for modern living. Located in a vibrant arts district.', 'Industrial loft in vibrant arts district.', 1, 1, 1, 0, 1, '950 sq ft', 2018, '34 Artisan Lane', 1, 1, '05401', 'Exposed Brick,High Ceilings,Polished Concrete,Open Floor Plan', 'Galleries 0.1mi,Cafes 0.1mi,Transit 0.3mi', 'assets/images/properties/6.jpeg', 0),
('Suburban Estate', 'suburban-estate', 985000.00, 1, 'sold', 'Magnificent estate on manicured grounds. Grand foyer, formal dining, chef kitchen, and resort-style backyard with pool and outdoor kitchen.', 'Grand estate with resort-style amenities.', 6, 5, 1, 3, 8, '1.5 acres', 2019, '500 Estate Lane', 1, 1, '05401', 'Pool,Outdoor Kitchen,Home Office,Wine Room,Three-car Garage', 'Country Club 1mi,Schools 0.8mi,Shopping 2mi', 'assets/images/properties/7.jpeg', 1),
('Cozy Starter Home', 'cozy-starter-home', 285000.00, 1, 'available', 'Perfect starter home in a friendly neighborhood. Recently renovated with new flooring, paint, and appliances. Fenced backyard and detached garage.', 'Affordable renovated starter home.', 3, 1, 1, 1, 2, '0.25 acres', 1985, '67 Oak Street', 1, 2, '05602', 'Fenced Yard,New Appliances,Detached Garage,Updated Bath', 'Schools 0.3mi,Park 0.5mi,Grocery 0.4mi', 'assets/images/properties/8.jpeg', 0),
('Penthouse Suite', 'penthouse-suite', 1850000.00, 2, 'available', 'Exclusive penthouse with 360-degree views. Private elevator, rooftop terrace, and bespoke interior design. The epitome of luxury urban living.', 'Exclusive penthouse with panoramic city views.', 3, 3, 1, 2, 4, '2800 sq ft', 2024, '1 Skyline Tower', 5, 10, '10001', 'Private Elevator,Rooftop Terrace,Concierge,Smart Home,Marble Finishes', 'Theater 0.2mi,Fine Dining 0.1mi,Central Park 0.5mi', 'assets/images/properties/9.jpeg', 1),
('Countryside Ranch', 'countryside-ranch', 520000.00, 1, 'available', 'Sprawling single-level ranch on peaceful countryside. Open layout, sunroom, and attached workshop. Ideal for those seeking tranquility without sacrificing convenience.', 'Peaceful countryside ranch with workshop.', 4, 3, 1, 2, 4, '3 acres', 2005, '890 Country Road', 1, 3, '05672', 'Sunroom,Workshop,Attached Garage,Fireplace,Pasture', 'Farmers Market 3mi,Town Center 5mi,Lake 1mi', 'assets/images/properties/10.jpeg', 0);

-- Gallery images for properties
INSERT INTO property_images (property_id, image_path, is_cover, sort_order) VALUES
(1, 'assets/images/properties/1.jpeg', 1, 0),
(1, 'assets/images/properties/11.jpeg', 0, 1),
(1, 'assets/images/properties/12.jpeg', 0, 2),
(2, 'assets/images/properties/2.jpeg', 1, 0),
(2, 'assets/images/properties/13.jpeg', 0, 1),
(3, 'assets/images/properties/3.jpeg', 1, 0),
(3, 'assets/images/properties/14.jpeg', 0, 1),
(4, 'assets/images/properties/4.jpeg', 1, 0),
(4, 'assets/images/properties/15.jpeg', 0, 1),
(5, 'assets/images/properties/5.jpeg', 1, 0),
(5, 'assets/images/properties/16`.jpeg', 0, 1),
(5, 'assets/images/properties/17.jpeg', 0, 2),
(6, 'assets/images/properties/6.jpeg', 1, 0),
(7, 'assets/images/properties/7.jpeg', 1, 0),
(7, 'assets/images/properties/8.jpeg', 0, 1),
(8, 'assets/images/properties/8.jpeg', 1, 0),
(9, 'assets/images/properties/9.jpeg', 1, 0),
(9, 'assets/images/properties/10.jpeg', 0, 1),
(10, 'assets/images/properties/10.jpeg', 1, 0);
