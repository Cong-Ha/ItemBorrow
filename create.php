<?php
$msg="";
$errors = [];

// make data calls
include("config/dbConfig.php");
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

//database sequence
if($_SERVER["REQUEST_METHOD"]=="POST") {
    //output any errors to javascript to control modal submit
    if(!empty($errors)) {
        echo "<script>var errors = " . json_encode($errors) . ";</script>";
    }

    //assign form data
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $role = sanitizeInput($_POST['role'] ?? '');
    $item_id = $_POST['item_id'];
    $borrow_date = date('Y-m-d H:i:s'); //current timestamp
    $due_date = date('Y-m-d H:i:s', strtotime('+7 days')); //due in 7 days
    $usage_location = sanitizeInput($_POST['usage_location'] ?? '');

    $allowedRoles = ["Student", "Teacher", "Librarian", "Admin"];
    $allowedLocations = ["Classroom", "Home", "Lab", "Office"];
    $itemIds = array_column($items,"item_id");
    // echo "<script>console.log('Items: " . json_encode($itemIds) . "');</script>";

    if(!validateInput($full_name, $patterns['FullName']))
    {
        $errors['full_name'] = "Invalid Full Name (only letters including uppercase & spaces, max 100 characters).";
                
    }
    if(!validateInput($email, $patterns['Email']))
    {
        $errors['email'] = "Invalid Email (use standard format: email@gmail.com).";
                
    }
    if(!validateInput($phone, $patterns['Phone']))
    {
        $errors['phone'] = "Invalid Full Name (only only digits, spaces, parantheses, plus signs or hyphens, max 20 characters).";
                
    }
    if(!validateInput($item_id, $patterns['ItemId'])) {
        $errors['item_id'] = 'Invalid Item Id must be a numerical value!';
    }
    if(!validateInput($borrow_date, $patterns['BorrowDate'])) {
        $errors['borrow_date'] = 'Invalid date must be in YYYY-MM-DD HH:MM:SS format!';
    }
    if(empty($role)) {
        $errors['role'] = "Role is required.";
    } elseif (!in_array($role, $allowedRoles)){
        $errors['role'] = "Invalid role selected";
    }
    if (empty($usage_location)) {
        $errors['usage_location'] = "Usage location is required.";
    } elseif (!in_array($usage_location, $allowedLocations)) {
        $errors['usage_location'] = "Invalid usage location selected.";
    }

    if(empty($errors)) {
        try {
            //start transaction
            $conn->beginTransaction();        
            $query = "INSERT INTO users SET full_name = ?, email = ?, phone = ?, role = ?";
            $stmt = $conn->prepare($query);
        
            //bind parameters
            $stmt->bindParam(1, $full_name);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $phone);
            $stmt->bindParam(4, $role);
    
            $successUser = $stmt->execute();
    
            //insert into borrowing table
            if($successUser) {
                $user_id = $conn->lastInsertId();
                // echo "<script>console.log('User ID: " . json_encode($user_id) . "');</script>";
                $query = "INSERT INTO borrowings SET user_id = ?, item_id = ?, borrow_date = ?, due_date = ?, usage_location = ?";
                $stmt = $conn->prepare($query);
    
                $stmt->bindParam(1, $user_id);
                $stmt->bindParam(2, $item_id);
                $stmt->bindParam(3, $borrow_date);
                $stmt->bindParam(4, $due_date);
                $stmt->bindParam(5, $usage_location);
    
                $successBorrow = $stmt->execute();
                
                //update item table
                if($successBorrow){
                    $query = "UPDATE items SET availability_status = 'Borrowed' WHERE item_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(1, $item_id);
                    $successUpdate = $stmt->execute();
    
                    if($successUpdate) {
                        $conn->commit();
    
                        //re-fetch items for select drop down
                        $query = "SELECT item_id, item_name, availability_status FROM items";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                        $msg= "<div class='alert alert-success'><strong>All transactions successful, record was saved!</strong></div>";
                    } else {
                        $conn->rollback();
                        $msg= "<div class='alert alert-danger'><strong>Failed to update item status!</strong></div>";
                    }
                } else {
                    $conn->rollback();
                    $msg= "<div class='alert alert-danger'><strong>Failed to record borrowing transaction!</strong></div>";
                }
            } else {
                $conn->rollBack();
                $msg= "<div class='alert alert-danger'><strong>Failed to add user!</strong></div>";
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $msg = "<div class='alert alert-danger'><strong>Error: " . $e->getMessage() . "</strong></div>";
        }
    }
}
?>