-- Database: travel_db

CREATE DATABASE IF NOT EXISTS travel_db;

USE travel_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    second_title VARCHAR(255) DEFAULT NULL,
    second_description TEXT DEFAULT NULL,
    features BLOB NOT NULL,
    banner_image VARCHAR(255) DEFAULT NULL,
    background_image VARCHAR(255) DEFAULT NULL,
    duration INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    destination_slug VARCHAR(255) DEFAULT NULL,
    destination_title VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    travel_date DATE NOT NULL,
    persons INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
    message TEXT,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    reason VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (destination_slug) REFERENCES destinations(slug) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS destination_highlights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    video_title VARCHAR(255) NOT NULL,
    video_description TEXT,
    video_url VARCHAR(255) NOT NULL,
    video_type ENUM('youtube', 'mp4') DEFAULT 'youtube',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS guest_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS custom_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    guest_id INT DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    custom_destination VARCHAR(255) NOT NULL,
    travel_date DATE,
    people INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guest_id) REFERENCES guest_users(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);


ALTER TABLE bookings 
  ADD COLUMN guest_id INT DEFAULT NULL,
  ADD COLUMN agent_message TEXT DEFAULT NULL,
  ADD COLUMN source ENUM('site', 'chatbot', 'agent') DEFAULT 'site',
  ADD COLUMN channel ENUM('book_now', 'talk_to_agent') DEFAULT 'book_now',
  ADD CONSTRAINT fk_guest FOREIGN KEY (guest_id) REFERENCES guest_users(id) ON DELETE SET NULL;