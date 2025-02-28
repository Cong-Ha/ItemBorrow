<?php include("create.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/GET_borrowInfo.js"></script>
    <script src="js/UPDATE_borrowEdit.js"></script>
    <script src="js/DELETE_borrow.js"></script>
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-light bg-dark border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand text-light" href="index.php">
                <i class="bi bi-house fs-3"></i>
            </a>
        </div>
    </nav>
    <div class="container mt-3">
        <h2>Borrowed</h2>
        <a href="addLoan.php" class="btn btn-success float-end">Add loan</a>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Borrow Id</th>
                    <th>User Name</th>
                    <th>Item Name</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Usage Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php
                //database connection
                include("config/dbConfig.php");

                //select data
                $query = 
                    "SELECT 
                    borrow_id, 
                    u.full_name,
                    i.item_id, 
                    i.item_name, 
                    borrow_date, 
                    due_date, 
                    usage_location, 
                    status 
                    FROM borrowings b
                    JOIN users u ON b.user_id = u.user_id
                    JOIN items i ON b.item_id = i.item_id";

                //prepare query statement
                $stmt = $conn->prepare($query);

                //execute query
                $stmt->execute();
            ?>
            <tbody>
                <?php
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        extract($row);

                        echo"<tr>";
                            echo"<td>{$borrow_id}</td>";
                            echo"<td>{$full_name}</td>";
                            echo"<td id-item='{$item_id}'>{$item_name}</td>";
                            echo"<td>{$borrow_date}</td>";
                            echo"<td>{$due_date}</td>";
                            echo"<td>{$usage_location}</td>";
                            echo"<td>{$status}</td>";
                            echo"<td>";
                                echo"<button type='button' class='btn btn-primary btn-sm' onClick='borrowInfo($borrow_id)' data-bs-toggle='modal' data-bs-target='#infoModal'>Info</button>";    
                                echo"<button type='button' class='btn btn-warning btn-sm' onClick='borrowEdit($borrow_id)' data-bs-toggle='modal' data-bs-target='#updateModal'>Edit</button>";
                                echo"<a href='#' onclick='delete_borrow({$borrow_id}, event);' class='btn btn-danger btn-sm'>Del</a>";
                            echo"</td>";
                        echo"</tr>";

                    }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Info Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Borrow Info</h5>
                    <button type="button" class="btn-close bg-dark-subtle" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="borrowDetails">
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <!-- Modal Header -->
                <div class="modal-header justify-content-center">
                    <h4 class="modal-title">Update Info</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body" id="borrowEdit"></div>
            </div>
        </div>
    </div>
</body>
</html>

