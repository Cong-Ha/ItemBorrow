<?php
    try
    {
        include "config/dbConfig.php";

        $id = isset($_GET['id']) ? $_GET['id'] : die("Id not found.");
        $itemId = isset($_GET['item_id']) ? $_GET['item_id'] : die("itemId not found.");

        $conn->beginTransaction();
        //update item table to available status
        if(!empty($itemId)){
            $query = "UPDATE items 
            SET availability_status = ? 
            WHERE item_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute(["Available", $itemId]);
        }

        // Delete from borrowings table
        $query = "DELETE FROM borrowings WHERE borrow_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $successBorrowing = $stmt->execute();

        if ($successBorrowing) {
            $conn->commit();
            header("Location: index.php");
        }
        exit();
    }
    catch(PDOException $e) 
    {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
?>