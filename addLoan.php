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
                echo "<script>console.log('User ID: " . json_encode($user_id) . "');</script>";
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

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Add Loan</title>
</head>
    <body class="bg-dark text-light">
        <div class="container mt-5 mb-5 d-flex justify-content-center">
            <div class="card w-50 bg-dark border-light">
                <div class="card-body">
                    <h4 class="text-center text-light">Add Loan</h4>
                    <!-- database access message -->
                    <?php echo $msg; ?>
                    <form action="#" method="POST">
                        <div class="form-group">
                            <!-- Full Name -->
                            <div class="form-group mt-2">
                                <label for="full_name" class="form-label text-light">Full Name:</label>
                                <input type="text" class="form-control text-light bg-secondary border-light" name="full_name" id="full_name" maxlength="100">
                                <span class="text-danger"><?php echo $errors['full_name'] ?? ''; ?></span>
                            </div>

                            <!-- Email -->
                            <div class="form-group mt-2">
                                <label for="email" class="form-label text-light">Email:</label>
                                <input type="text" class="form-control text-light bg-secondary border-light" name="email" id="email" maxlength="100">
                                <span class="text-danger"><?php echo $errors['email'] ?? ''; ?></span>
                            </div>

                            <!-- Phone -->
                            <div class="form-group mt-2">
                                <label for="phone" class="form-label text-light">Phone Number:</label>
                                <input type="text" class="form-control text-light bg-secondary border-light" name="phone" id="phone" maxlength="20">
                                <span class="text-danger"><?php echo $errors['phone'] ?? ''; ?></span>
                            </div>

                            <!-- role -->
                            <div class="form-group mt-2">
                                <label for="role" class="form-label text-light">Role:</label>
                                <select class="form-control text-light bg-secondary border-light" name="role" id="role">
                                    <option value="" disabled selected>Select Role</option>
                                    <option value="Student">Student</option>
                                    <option value="Teacher">Teacher</option>
                                    <option value="Librarian">Librarian</option>
                                    <option value="Admin">Admin</option>
                                </select>
                                <span class="text-danger"><?php echo $errors['role'] ?? ''; ?></span>
                            </div>

                            <!-- Usage Location -->
                            <div class="form-group mt-2">
                                <label for="usage_location" class="form-label text-light">Usage Location:</label>
                                <select class="form-control text-light bg-secondary border-light" name="usage_location" id="usage_location">
                                    <option value="" disabled selected>Select Role</option>
                                    <option value="Classroom">Classroom</option>
                                    <option value="Home">Home</option>
                                    <option value="Lab">Lab</option>
                                    <option value="Office">Office</option>
                                </select>
                                <span class="text-danger"><?php echo $errors['usage_location'] ?? ''; ?></span>
                            </div>

                            <!-- Loan Item Dropdown -->
                            <div class="form-group mt-2">
                                <label for="item_id" class="form-label text-light">Select Item:</label>
                                <select class="form-control bg-secondary text-light border-light" name="item_id" id="item_id">
                                    <option value="">Select an Item</option>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= htmlspecialchars($item['item_id']) ?>"
                                            <?= ($item['availability_status'] !== 'Available') ? 'disabled' : '' ?>>
                                            <?= htmlspecialchars($item['item_name']) ?> 
                                            (<?= htmlspecialchars($item['availability_status']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-danger"><?php echo $errors['item_id'] ?? ''; ?></span>
                            </div>

                            <div class="form-group mt-2 d-flex justify-content-center">
                                <button class="btn btn-primary">Add</button>
                                <a href=index.php class="btn btn-danger ms-3">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

<style>
        /* Change color of disabled options */
        select option:disabled {
        color: #a0a0a0 !important; /* Light gray for visibility */
    }
</style>