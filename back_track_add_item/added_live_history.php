<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

if (isset($_GET['id']) && isset($_GET['branch'])) {
    $dashboardid = intval($_GET['id']);
    $_SESSION['branch_id'] = $dashboardid;
}

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$date = isset($_POST['date']) ? $_POST['date'] : ''; // Get the date input, if provided
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // Get the current page
$rowsPerPage = 10; // Set the number of rows per page
$offset = ($page - 1) * $rowsPerPage; // Calculate the offset for SQL LIMIT

// Start constructing the SQL query
$sql = "SELECT SQL_CALC_FOUND_ROWS add_stock_history.id, flavor.flavor, flavor.price, add_stock_history.quantity, add_stock_history.date 
        FROM add_stock_history 
        INNER JOIN flavor ON flavor.id = add_stock_history.product_name 
        WHERE add_stock_history.b_id = ?";

// Prepare parameters for branch ID
$params = [$branchid];
$types = "i"; // Branch ID is an integer

// Add a date filter to the query if a date is provided
if (!empty($date)) {
    $sql .= " AND DATE(add_stock_history.date) = ?";
    array_push($params, $date);
    $types .= "s"; // Add string type for the date
}

$sql .= " ORDER BY add_stock_history.id ASC LIMIT ?, ?";
array_push($params, $offset, $rowsPerPage);
$types .= "ii"; // Add integer types for LIMIT and OFFSET

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Check if preparation is successful
if (!$stmt) {
    die("SQL preparation failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param($types, ...$params);

// Execute the statement
$stmt->execute();
$result = $stmt->get_result(); // Get the result set

// Get total rows for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

$totalValue = 0; // Initialize total value

ob_start();

// Check if there are any records
if ($result && $result->num_rows > 0) {
    // Output the table rows
    while ($row = $result->fetch_assoc()):
        $subtotal = $row["quantity"] * $row["price"]; // Calculate subtotal for each row
        $totalValue += $subtotal; // Add to the total value
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row["flavor"]); ?></td>
            <td><?php echo htmlspecialchars($row["quantity"]); ?></td>
            <td><?php echo htmlspecialchars($row["price"]); ?></td>
            <td><?php echo htmlspecialchars($row["date"]); ?></td>
        </tr>
    <?php endwhile;
} else {
    // If no records are found
    echo "<tr><td colspan='4' class='text-center'>No records found</td></tr>";
}

$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Output the content and pagination data as JSON
echo json_encode([
    'content' => $content,
    'totalSystemTally' => number_format($totalValue, 2),
    'totalPages' => $totalPages,
    'currentPage' => $page,
]);
?>
