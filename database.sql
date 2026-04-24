-- StayScape - Airbnb Clone Database Schema

CREATE DATABASE IF NOT EXISTS stayscape;
USE stayscape;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    bio TEXT,
    is_host TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL
);

-- Properties table
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    host_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    location VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    max_guests INT DEFAULT 1,
    bedrooms INT DEFAULT 1,
    bathrooms INT DEFAULT 1,
    beds INT DEFAULT 1,
    property_type VARCHAR(50) DEFAULT 'Entire place',
    amenities TEXT,
    image VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0.0,
    review_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (host_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Property images table
CREATE TABLE property_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    guest_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    service_fee DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (guest_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

-- Wishlists table
CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, property_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    property_id INT,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
);

-- Insert default categories
INSERT INTO categories (name, icon) VALUES
('Beach', 'fa-umbrella-beach'),
('Mountains', 'fa-mountain'),
('Cities', 'fa-city'),
('Countryside', 'fa-tree'),
('Lakes', 'fa-water'),
('Tropical', 'fa-sun'),
('Castles', 'fa-chess-rook'),
('Camping', 'fa-campground'),
('Arctic', 'fa-snowflake'),
('Desert', 'fa-sun-plant-wilt'),
('Islands', 'fa-island-tropical'),
('Luxury', 'fa-gem');

-- Insert sample users (password is 'password123' hashed with password_hash)
INSERT INTO users (full_name, email, password, phone, bio, is_host) VALUES
('Rahul Sharma', 'rahul@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', 'Superhost from Mumbai. Love hosting travelers from around the world!', 1),
('Priya Patel', 'priya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543211', 'Adventure lover and host in Goa.', 1),
('Amit Kumar', 'amit@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543212', 'I love exploring new places!', 0),
('Sarah Johnson', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', 'Traveling the world one stay at a time.', 0),
('Demo User', 'demo@stayscape.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'Demo account for testing.', 0);

-- Insert sample properties
INSERT INTO properties (host_id, category_id, title, description, price_per_night, location, city, country, max_guests, bedrooms, bathrooms, beds, property_type, amenities, rating, review_count) VALUES
(1, 1, 'Beachfront Villa with Ocean Views', 'Wake up to stunning ocean views in this beautiful beachfront villa. Featuring modern amenities, a private pool, and direct beach access. Perfect for families and groups looking for a luxurious coastal getaway.', 8500.00, 'Juhu Beach Road', 'Mumbai', 'India', 6, 3, 2, 4, 'Entire villa', 'WiFi,Pool,Kitchen,AC,Parking,Beach Access,TV,Washer', 4.8, 124),
(1, 3, 'Modern Apartment in South Mumbai', 'Stylish 2BHK apartment in the heart of South Mumbai. Walking distance to Gateway of India and Marine Drive. Fully furnished with all modern amenities.', 4500.00, 'Colaba', 'Mumbai', 'India', 4, 2, 1, 2, 'Entire apartment', 'WiFi,Kitchen,AC,TV,Washer,Elevator', 4.6, 89),
(2, 1, 'Cozy Beach Hut in Goa', 'Experience the Goan vibe in this charming beach hut. Steps away from the sand, with a hammock on the porch and the sound of waves as your alarm clock.', 2500.00, 'Anjuna Beach', 'Goa', 'India', 2, 1, 1, 1, 'Entire place', 'WiFi,Beach Access,Fan,Restaurant Nearby', 4.5, 203),
(2, 1, 'Luxury Villa with Private Pool - Goa', 'Indulge in luxury at this stunning villa featuring a private infinity pool, landscaped gardens, and contemporary design. Ideal for celebrations and group getaways.', 15000.00, 'Vagator', 'Goa', 'India', 10, 5, 4, 6, 'Entire villa', 'WiFi,Pool,Kitchen,AC,Parking,Garden,BBQ,Staff', 4.9, 67),
(1, 2, 'Mountain Retreat in Manali', 'Escape to the mountains in this cozy wooden cottage surrounded by pine forests. Enjoy breathtaking views of the Himalayas, bonfire nights, and fresh mountain air.', 3500.00, 'Old Manali Road', 'Manali', 'India', 4, 2, 1, 2, 'Entire cottage', 'WiFi,Fireplace,Kitchen,Parking,Mountain View,Heating', 4.7, 156),
(1, 4, 'Heritage Haveli in Jaipur', 'Stay in a beautifully restored 200-year-old haveli with traditional Rajasthani architecture, courtyard, and rooftop dining with fort views.', 6000.00, 'Amer Road', 'Jaipur', 'India', 6, 3, 2, 4, 'Entire place', 'WiFi,AC,Courtyard,Rooftop,Breakfast,Parking', 4.8, 92),
(2, 2, 'Treehouse Experience in Munnar', 'A unique treehouse stay surrounded by tea plantations. Perfect for couples seeking an offbeat romantic getaway with nature at its best.', 4000.00, 'Tea Valley Road', 'Munnar', 'India', 2, 1, 1, 1, 'Treehouse', 'WiFi,Nature View,Breakfast,Balcony', 4.9, 178),
(1, 5, 'Lakeside Cottage in Udaipur', 'Charming cottage overlooking Lake Pichola. Wake up to stunning lake and palace views. Traditional decor meets modern comfort.', 5500.00, 'Lake Palace Road', 'Udaipur', 'India', 4, 2, 1, 2, 'Entire cottage', 'WiFi,AC,Lake View,Rooftop,Kitchen,Parking', 4.7, 134),
(2, 3, 'Designer Loft in Bangalore', 'Ultra-modern loft apartment in Koramangala with exposed brick walls, designer furniture, and a fully equipped kitchen. Close to restaurants and nightlife.', 3800.00, 'Koramangala', 'Bangalore', 'India', 3, 1, 1, 1, 'Entire loft', 'WiFi,Kitchen,AC,TV,Gym,Washer,Workspace', 4.5, 67),
(1, 6, 'Tropical Paradise in Andaman', 'Private beachfront bungalow on Havelock Island. Crystal clear waters, white sand, and lush tropical surroundings for the ultimate island escape.', 7000.00, 'Radhanagar Beach', 'Havelock Island', 'India', 4, 2, 1, 2, 'Entire bungalow', 'WiFi,Beach Access,AC,Restaurant,Snorkeling,Kayak', 4.8, 45),
(2, 12, 'Luxury Houseboat in Kerala', 'Cruise the Kerala backwaters on this premium houseboat with AC bedrooms, a personal chef, and panoramic views of palm-fringed canals.', 12000.00, 'Alleppey Backwaters', 'Alleppey', 'India', 6, 3, 2, 3, 'Houseboat', 'AC,Chef,Meals Included,Deck,Cruise', 4.9, 211),
(1, 2, 'Snowview Lodge in Shimla', 'Charming colonial-era lodge with panoramic snow-capped mountain views. Cozy fireplace, wooden interiors, and old-world charm.', 4200.00, 'Mall Road', 'Shimla', 'India', 5, 2, 2, 3, 'Entire lodge', 'WiFi,Fireplace,Kitchen,Parking,Mountain View,Heating,TV', 4.6, 88);

-- Insert sample reviews
INSERT INTO reviews (property_id, user_id, rating, comment) VALUES
(1, 3, 5, 'Absolutely stunning villa! The ocean views were breathtaking and the host was incredibly welcoming. Will definitely come back!'),
(1, 4, 5, 'Perfect beachfront location. The villa was spotless and had everything we needed. Highly recommend!'),
(1, 3, 4, 'Great location and amenities. The pool was amazing. Only wish the WiFi was a bit stronger.'),
(3, 3, 5, 'The most authentic Goan experience! Falling asleep to the sound of waves was magical.'),
(3, 4, 4, 'Lovely beach hut, very cozy. The location is unbeatable. Priya was a great host!'),
(5, 4, 5, 'The mountain views were incredible! Such a peaceful retreat. The cottage was warm and cozy.'),
(5, 3, 5, 'Best mountain getaway ever! The fireplace, the views, everything was perfect.'),
(7, 3, 5, 'A treehouse in the tea plantations - truly unique! One of the best experiences of my life.'),
(7, 4, 5, 'Romantic, magical, and unforgettable. The views from the treehouse are stunning.'),
(11, 3, 5, 'The houseboat experience was out of this world! The food was amazing and the views were serene.'),
(11, 4, 5, 'Best trip ever! The chef prepared the most delicious Kerala cuisine. Highly recommended!'),
(4, 3, 5, 'Luxury at its finest! The infinity pool overlooking the valley was jaw-dropping.');
