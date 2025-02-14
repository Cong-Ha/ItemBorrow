CREATE DATABASE IF NOT EXISTS cweb1131;
USE cweb1131;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('Student', 'Teacher', 'Librarian', 'Admin') NOT NULL DEFAULT 'Student'
);

CREATE TABLE IF NOT EXISTS items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    availability_status ENUM('Available', 'Borrowed') NOT NULL DEFAULT 'Available'
);

CREATE TABLE IF NOT EXISTS borrowings (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_id INT,
    borrow_date DATETIME,
    due_date DATETIME,
    usage_location ENUM('Classroom', 'Home', 'Library', 'Lab', 'Office') NOT NULL DEFAULT 'Library',
    `status` ENUM('Borrowed', 'Overdue', 'Returned') NOT NULL DEFAULT 'Borrowed',
    CONSTRAINT fk_borrowings_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_borrowings_items FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);