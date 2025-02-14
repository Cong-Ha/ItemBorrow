<?php
    $host = "localhost";
    $dbName = "cweb1131";
    $userName = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbName", $userName, $password);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) { 
        die("Connection failed: " . $e->getMessage());
    }

?>