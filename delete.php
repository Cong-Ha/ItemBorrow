<?php
    try
    {
        // Include database connection
        include "config/dbConfig.php";

        // Ensure an ID is provided
        $id = isset($_GET['id']) ? $_GET['id'] : die("Id not found.");

        // Start transaction to ensure integrity
        $conn->beginTransaction();

        // Delete from borrowings table first (assuming id is the borrowing_id)
        $query = "DELETE FROM borrowings WHERE borrow_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $successBorrowing = $stmt->execute();

        if ($successBorrowing) {
            $conn->commit();
            header("Location: index.php");
            exit();
        } else {
            $conn->rollBack();
            echo "Error: Unable to delete borrowing record.";
        }
    }
    catch(PDOException $e) 
    {
        $conn->rollBack();
        echo "Error : " . $e->getMessage();
    }
?>