<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : ''; // Get the date input if provided
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // Page number for pagination
$rowsPerPage = 10; // Number of rows per page
$offset = ($page - 1) * $rowsPerPage; // Calculate the offset

// Prepare the SQL query with placeholders to prevent SQL injection
$sql = "
    SELECT SQL_CALC_FOUND_ROWS * FROM dispose 
    WHERE b_id = ? 
    AND (order_number LIKE ? OR product LIKE ?)
";

// Prepare parameters and types
$params = [$branchid, "%$searchTerm%", "%$searchTerm%"];
$types = "iss";

// Add a date filter to the query if a date is provided
if (!empty($date)) {
    $sql .= " AND DATE(date) = ?";
    $params[] = $date;
    $types .= "s";
}

// Order by date ascending
$sql .= " ORDER BY DATE(date) ASC LIMIT ?, ?";

// Add pagination limits to the query
$params[] = $offset;
$params[] = $rowsPerPage;
$types .= "ii";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL preparation failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get total rows for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Initialize total amount
$totalValue = 0;

// Start output buffering
ob_start();

if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): 
        // Calculate total for this row and add to total value
        $subtotal = $row["price"] * $row["quantity"];
        $totalValue += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($row["order_number"]) ?></td>
            <td><?= htmlspecialchars($row["product"]) ?></td>
            <td><?= htmlspecialchars($row["quantity"]) ?></td>
            <td><?= htmlspecialchars($row["price"]) ?></td>
            <td><?= htmlspecialchars($row["reason"]) ?></td>
            <td><?= htmlspecialchars($row["date"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center">No records found</td>
    </tr>
<?php endif;

$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Send the generated HTML and pagination data
echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'totalValue' => number_format($totalValue, 2)
]);
?>
