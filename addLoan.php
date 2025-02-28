<?php include("create.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Add Loan</title>
</head>
    <body class="bg-dark text-light">
        <nav class="navbar navbar-light bg-dark border-bottom">
            <div class="container-fluid">
                <a class="navbar-brand text-light" href="index.php">
                    <i class="bi bi-house fs-3"></i>
                </a>
            </div>
        </nav>
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