<?php
$errors = [];

include("config/dbConfig.php");
header('Content-Type: application/json');
$borrow_id = isset($_POST['borrow_id']) ? $_POST['borrow_id'] : die("Id not found.");

$query = "SELECT item_id, item_name, availability_status FROM items";
$stmt = $conn->prepare($query);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC); //drop down items

function sanitizeInput($data) {
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = trim($data);
    return $data;
}

function validateInput($data, $patterns) {
    return preg_match($patterns, $data);
}

$patterns = [
    "FullName" => "/^[A-Za-z\s]+$/",
    "Email" => "/^[\w.-]+@[a-zA-Z\d.-]+\.[a-zA-Z]{2,}$/",
    "Phone" => "/^[\d\s\(\)\+\-]{1,20}$/",
    "BorrowDate" => "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", //YYYY-MM-DD HH:MM:SS format
    "ItemId" => "/^\d+$/", //numeric value only
];

if($_SERVER["REQUEST_METHOD"]=="POST") {
    //assign form data
    $full_name = sanitizeInput($_POST['fullNameUP']);
    $email = sanitizeInput($_POST['emailUP']);
    $phone = sanitizeInput($_POST['phoneUP']);
    $role = sanitizeInput($_POST['roleUP'] ?? '');
    $item_id = $_POST['item_idUP'];
    $borrow_date = date('Y-m-d H:i:s'); //current timestamp
    $due_date = date('Y-m-d H:i:s', strtotime('+7 days')); //due in 7 days
    $usage_location = sanitizeInput($_POST['usage_locationUP'] ?? '');
    $status = sanitizeInput($_POST['statusUP'] ?? '');

    $allowedRoles = ["Student", "Teacher", "Librarian", "Admin"];
    $allowedLocations = ["Classroom", "Home", "Lab", "Office"];
    $itemIds = array_column($items,"item_id");
    $allowedStatus = ["Borrowed", "Returned", "Overdue"];
    // echo "<script>console.log('Items: " . json_encode($itemIds) . "');</script>";

    if(!validateInput($full_name, $patterns['FullName']))
    {
        $errors['fullNameUP'] = "Invalid Full Name (only letters including uppercase & spaces, max 100 characters).";
                
    }
    if(!validateInput($email, $patterns['Email']))
    {
        $errors['emailUP'] = "Invalid Email (use standard format: email@gmail.com).";
                
    }
    if(!validateInput($phone, $patterns['Phone']))
    {
        $errors['phoneUP'] = "Invalid Phone number (only only digits, spaces, parantheses, plus signs or hyphens, max 20 characters).";
                
    }
    if(!validateInput($item_id, $patterns['ItemId'])) {
        $errors['item_idUP'] = 'Invalid Item Id must be a numerical value!';
    }
    if(!validateInput($borrow_date, $patterns['BorrowDate'])) {
        $errors['borrow_date'] = 'Invalid date must be in YYYY-MM-DD HH:MM:SS format!';
    }
    if(empty($role) || !in_array($role, $allowedRoles)) {
        $errors['roleUP'] = "Invalid role selected";
    }
    if (empty($usage_location) || !in_array($usage_location, $allowedLocations)) {
        $errors['usage_locationUP'] = "Invalid usage location selected.";
    }
    if (empty($status) || !in_array($status, $allowedStatus)) {
        $errors['statusUP'] = "Invalid status selected.";
    }
    if (!empty($errors)) {
        echo json_encode(["status" => "error", "errors" => $errors]);
        exit;
    }
}

if(empty($errors)) {
    try {
        $conn->beginTransaction();

        // Update user details
        $query = "UPDATE users 
        JOIN borrowings ON users.user_id = borrowings.user_id
        SET users.full_name = ?, 
            users.email = ?, 
            users.phone = ?, 
            users.role = ?
        WHERE borrowings.borrow_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$full_name, $email, $phone, $role, $borrow_id]);

        // Update borrowings table
        $query = "UPDATE borrowings 
        SET item_id = ?, 
            borrow_date = ?, 
            usage_location = ?, 
            status = ?
        WHERE borrow_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$item_id, $borrow_date, $usage_location, $status, $borrow_id]);

        //Update item availability status
        //make item available if returned
        if($status === "Returned") { $status = "Available"; }

        $query = "UPDATE items 
        SET availability_status = ? 
        WHERE item_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$status, $item_id]);

        if($conn->commit()){
            echo json_encode(["status" => "success", "message" => "Borrow details updated successfully!"]);
        }

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

?>