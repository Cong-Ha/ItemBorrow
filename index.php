

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-dark text-light">
    <div class="container mt-3">
        <h2>Borrowed</h2>
        <a href="addLoan.php" class="btn btn-success float-end">Add loan</a>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Borrow Id</th>
                    <th>User Id</th>
                    <th>Item Id</th>
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
                $query = "SELECT borrow_id, user_id, item_id, borrow_date, due_date, usage_location, status FROM borrowings";

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
                            echo"<td>{$user_id}</td>";
                            echo"<td>{$item_id}</td>";
                            echo"<td>{$borrow_date}</td>";
                            echo"<td>{$due_date}</td>";
                            echo"<td>{$usage_location}</td>";
                            echo"<td>{$status}</td>";
                            echo"<td>";
                                echo'<button type="button" class="btn btn-primary btn-sm">Info</button>';    
                                echo'<button type="button" class="btn btn-warning btn-sm">Edit</button>';
                                echo"<a href='#' onclick='delete_borrow({$borrow_id});' class='btn btn-danger btn-sm'>Del</a>";
                            echo"</td>";
                        echo"</tr>";

                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<script>
    function delete_borrow(id) {
        var answer = confirm(id + " Are you sure?");
        if(answer) {
            window.location="delete.php?id="+id;
        }
    }
</script>

