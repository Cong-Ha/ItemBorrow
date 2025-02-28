<div class="container mt-3">
    <table class="table table-dark table-hover">
        <tbody>
            <?php
            include("config/dbConfig.php");

            if (isset($_GET['id'])) {
                $borrowId = $_GET['id'];

                $query = "SELECT 
                    borrow_id, 
                    u.full_name, 
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
                    foreach ($result as $key => $value) {
                        echo "<tr>
                                <th class='text-light'>" . ucwords(str_replace("_", " ", $key)) . "</th>
                                <td>" . htmlspecialchars($value) . "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' class='text-danger'>Borrow info not found!</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2' class='text-danger'>Invalid request!</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>





