<?php
$errors = [];
include("config/dbConfig.php");
$query = "SELECT item_id, item_name, availability_status FROM items";
$stmt = $conn->prepare($query);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC); //drop down items


if (isset($_GET['id'])) {
    $borrowId = $_GET['id'];

    $query = "SELECT 
                borrow_id, 
                u.full_name,
                u.email,
                u.phone,
                u.role,
                i.item_id, 
                i.item_name, 
                borrow_date, 
                due_date, 
                usage_location, 
                status 
              FROM borrowings b
              JOIN users u ON b.user_id = u.user_id
              JOIN items i ON b.item_id = i.item_id
            WHERE borrow_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $borrowId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        ?>
        <form id="updateBorrowForm">
            <div class="form-group mt-2">
                <label for="fullNameUP" class="form-label text-light">Full Name:</label>
                <input type="text" id="fullNameUP" name="fullNameUP" class="form-control bg-secondary text-light border-light" value="<?= htmlspecialchars($result['full_name']) ?>">
                <span class="text-danger"></span>
            </div>
            <div class="form-group mt-2">
                <label for="emailUP" class="form-label text-light">Email:</label>
                <input type="text" id="emailUP" name="emailUP" class="form-control bg-secondary text-light border-light" value="<?= htmlspecialchars($result['email']) ?>">
                <span class="text-danger"></span>
            </div>
            <div class="form-group mt-2">
                <label for="phoneUP" class="form-label text-light">Phone Number:</label>
                <input type="text" id="phoneUP" name="phoneUP" class="form-control bg-secondary text-light border-light" value="<?= htmlspecialchars($result['phone']) ?>">
                <span class="text-danger"></span>
            </div>
            <div class="form-group mt-2">
                <label for="roleUP" class="form-label text-light">Role:</label>
                <select class="form-control text-light bg-secondary border-light" name="roleUP" id="roleUP">
                    <option value="" disabled>Select Role</option>
                    <option value="Student" <?= $result['role'] == 'Student' ? 'selected' : ''?>>Student</option>
                    <option value="Teacher" <?= $result['role'] == 'Teacher' ? 'selected' : ''?>>Teacher</option>
                    <option value="Librarian" <?= $result['role'] == 'Librarian' ? 'selected' : ''?>>Librarian</option>
                    <option value="Admin" <?= $result['role'] == 'Admin' ? 'selected' : ''?>>Admin</option>
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group mt-2">
                <label for="usage_locationUP" class="form-label text-light">Usage Location:</label>
                <select class="form-control text-light bg-secondary border-light" name="usage_locationUP" id="usage_locationUP">
                    <option value="" disabled>Select Role</option>
                    <option value="Classroom" <?= $result['usage_location'] == 'Classroom' ? 'selected' : ''?>>Classroom</option>
                    <option value="Home" <?= $result['usage_location'] == 'Home' ? 'selected' : ''?>>Home</option>
                    <option value="Lab" <?= $result['usage_location'] == 'Lab' ? 'selected' : ''?>>Lab</option>
                    <option value="Office" <?= $result['usage_location'] == 'Office' ? 'selected' : ''?>>Office</option>
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group mt-2">
                <label for="item_idUP" class="form-label text-light">Select Item:</label>
                <select class="form-control bg-secondary text-light border-light" name="item_idUP" id="item_idUP">
                    <option value="">Select an Item</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= htmlspecialchars($item['item_id']) ?>"
                        <?= $item['item_id'] == $result['item_id'] ? 'selected' : '' ?>
                        <?= ($item['item_id'] !== $result['item_id']) ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($item['item_name']) ?> 
                            (<?= htmlspecialchars($item['availability_status']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group mt-2">
                <label for="statusUP" class="form-label text-light">Status:</label>
                <select id="statusUP" name="statusUP" class="form-control bg-secondary text-light border-light">
                    <option value="Borrowed" <?= $result['status'] == 'Borrowed' ? 'selected' : '' ?>>Borrowed</option>
                    <option value="Returned" <?= $result['status'] == 'Returned' ? 'selected' : '' ?>>Returned</option>
                    <option value="Overdue" <?= $result['status'] == 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                </select>
                <span class="text-danger"></span>
            </div>
            <!-- Modal footer -->
            <div class="form-group mt-2 mb-2 d-flex justify-content-center">
                <button type="submit" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            </div>
            <input type="hidden" id="borrowId" name="borrowId" value="<?= $borrowId ?>">
        </form>
        <?php
    } else {
        echo "<p class='text-danger'>Borrow info not found!</p>";
    }
} else {
    echo "<p class='text-danger'>Invalid request!</p>";
}
?>

<style>
        /* Change color of disabled options */
        select option:disabled {
        color: #a0a0a0 !important; /* Light gray for visibility */
    }
</style>