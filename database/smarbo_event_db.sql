-- Database: smarbo_event_db
DROP DATABASE IF EXISTS smarbo_event_db;
CREATE DATABASE smarbo_event_db;
USE smarbo_event_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'event_owner') NOT NULL DEFAULT 'event_owner',
    full_name VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(200) NOT NULL,
    event_type ENUM('harusi', 'sendoff', 'kitchen_party', 'nyingine') NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(200) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    event_owner_id INT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Event Posts table for updates/photos/videos
CREATE TABLE event_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    post_type ENUM('update', 'photo', 'video', 'announcement') DEFAULT 'update',
    media_url VARCHAR(255),
    media_type ENUM('image', 'video') DEFAULT 'image',
    posted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Event Post Media (for multiple media files per post)
CREATE TABLE event_post_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    media_url VARCHAR(255) NOT NULL,
    media_type ENUM('image', 'video') DEFAULT 'image',
    thumbnail_url VARCHAR(255),
    caption VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES event_posts(id) ON DELETE CASCADE
);

-- Contributions table
CREATE TABLE contributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    contributor_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    promised_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    balance DECIMAL(10,2) GENERATED ALWAYS AS (promised_amount - paid_amount) STORED,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Invitations table
CREATE TABLE invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    status ENUM('pending', 'approved', 'disapproved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    attended_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Event Likes table
CREATE TABLE event_post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES event_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Event Comments table
CREATE TABLE event_post_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES event_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES event_post_comments(id) ON DELETE CASCADE
);

-- Insert default admin user (password: password)
INSERT INTO users (username, email, password, role, full_name, phone, avatar) 
VALUES 
('admin', 'admin@smarbo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Smarbo Admin', '+255123456789', 'https://ui-avatars.com/api/?name=Smarbo+Admin&background=E6521F&color=fff'),
('owner', 'owner@smarbo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'event_owner', 'Event Owner', '+255987654321', 'https://ui-avatars.com/api/?name=Event+Owner&background=E6521F&color=fff');

-- Create some events
INSERT INTO events (event_name, event_type, event_date, location, description, cover_image, event_owner_id, created_by) 
VALUES 
('John & Mary Wedding', 'harusi', '2024-12-25', 'Dar es Salaam Serena Hotel', 'Beautiful wedding ceremony and reception for John and Mary', 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800', 2, 1),
('University Send Off Party', 'sendoff', '2024-11-30', 'UDOM University', 'Graduation send off party for Computer Science class of 2024', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w-800', 2, 1),
('Bridal Shower Party', 'kitchen_party', '2024-10-15', 'Mbezi Garden', 'Kitchen party and bridal shower for Sarah', 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800', 2, 1);

-- Add some event posts
INSERT INTO event_posts (event_id, title, content, post_type, posted_by) 
VALUES 
(1, 'Wedding Venue Confirmed!', 'Great news everyone! We have confirmed the venue at Dar es Salaam Serena Hotel. The booking is secured for December 25th, 2024.', 'announcement', 1),
(1, 'Bridal Shower Photos', 'Check out these amazing photos from Sarah''s bridal shower last weekend!', 'photo', 1),
(1, 'Dance Practice Session', 'Join us for dance practice sessions every Saturday at 4 PM. Everyone is welcome!', 'update', 1);

-- Add post media
INSERT INTO event_post_media (post_id, media_url, media_type, caption) 
VALUES 
(2, 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800', 'image', 'Beautiful cake at the bridal shower'),
(2, 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800', 'image', 'The happy couple'),
(2, 'https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?w=800', 'image', 'Friends and family gathering');

-- Add some contributions
INSERT INTO contributions (event_id, contributor_name, phone_number, promised_amount, paid_amount) 
VALUES 
(1, 'Alice Johnson', '+255712345678', 500000, 500000),
(1, 'Bob Smith', '+255765432189', 300000, 150000),
(1, 'Charlie Brown', '+255788765432', 200000, 200000),
(2, 'David Wilson', '+255711223344', 250000, 250000);

-- Add some invitations
INSERT INTO invitations (event_id, guest_name, phone_number, status) 
VALUES 
(1, 'David Wilson', '+255711223344', 'approved'),
(1, 'Emma Davis', '+255755667788', 'pending'),
(1, 'Frank Miller', '+255799887766', 'disapproved'),
(2, 'Grace Lee', '+255744556677', 'approved');

-- Add attendance
INSERT INTO attendance (event_id, guest_name, status) 
VALUES 
(1, 'David Wilson', 'approved'),
(1, 'Emma Davis', 'pending'),
(1, 'Frank Miller', 'pending');