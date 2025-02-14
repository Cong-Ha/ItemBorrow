#Users
INSERT INTO Users (user_id, full_name, email, phone, role)
VALUES
(1, 'John Doe', 'john.doe@example.com', '1234567890', 'Student'),
(2, 'Jane Smith', 'jane.smith@example.com', '9876543210', 'Librarian'),
(3, 'Alice Brown', 'alice.brown@example.com', '1112223333', 'Teacher'),
(4, 'Robert Johnson', 'robert.johnson@example.com', '4445556666', 'Student'),
(5, 'Emma Williams', 'emma.williams@example.com', '7778889999', 'Admin')
ON DUPLICATE KEY UPDATE
full_name = VALUES(full_name),
email = VALUES(email),
phone = VALUES(phone),
role = VALUES(role);

#Items
INSERT INTO Items (item_id, item_name, category, availability_status)
VALUES
(1, 'Physics Textbook', 'Education', 'Available'),
(2, 'Chemistry Lab Kit', 'Lab', 'Borrowed'),
(3, 'Office Chair', 'Furniture', 'Available'),
(4, 'Anatomy Textbook', 'Education', 'Borrowed'),
(5, 'Digital Projector', 'Electronics', 'Available')
ON DUPLICATE KEY UPDATE
item_name = VALUES(item_name),
category = VALUES(category),
availability_status = VALUES(availability_status);

#Borrowings
INSERT INTO Borrowings (borrow_id, user_id, item_id, borrow_date, due_date, usage_location, status)
VALUES
(1, 1, 2, '2025-02-01 10:00:00', '2025-02-08 10:00:00', 'Lab', 'Borrowed'),
(2, 3, 4, '2025-01-25 09:30:00', '2025-02-01 09:30:00', 'Classroom', 'Overdue'),
(3, 4, 1, '2025-01-30 14:00:00', '2025-02-06 14:00:00', 'Home', 'Returned'),
(4, 2, 3, '2025-01-28 11:00:00', '2025-02-04 11:00:00', 'Library', 'Borrowed'),
(5, 5, 5, '2025-02-02 15:00:00', '2025-02-09 15:00:00', 'Office', 'Borrowed')
ON DUPLICATE KEY UPDATE
user_id = VALUES(user_id),
item_id = VALUES(item_id),
borrow_date = VALUES(borrow_date),
due_date = VALUES(due_date),
usage_location = VALUES(usage_location),
status = VALUES(status);