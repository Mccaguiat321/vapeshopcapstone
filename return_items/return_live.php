<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : ''; // Get the date input if provided
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$rowsPerPage = 12;
$offset = ($page - 1) * $rowsPerPage;

// Prepare the SQL query with placeholders to prevent SQL injection
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM returned_items 
        WHERE b_id = ? AND 
        (order_number LIKE ? OR product LIKE ?)";

$params = [$branchid, "%$searchTerm%", "%$searchTerm%"];
$types = "iss";

// Add a date filter to the query if a date is provided
if (!empty($date)) {
    $sql .= " AND DATE(date) = ?";
    $params[] = $date;
    $types .= "s";
}

$sql .= " ORDER BY quantity ASC LIMIT ?, ?";
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

// Initialize total value
$totalValue = 0;

// Start output buffering
ob_start();

if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): 
        // Calculate subtotal for each row
        $subtotal = $row["quantity"] * $row["price"];
        $totalValue += $subtotal; // Add to total
        ?>
        <tr>
            <td><?= htmlspecialchars($row["order_number"]) ?></td>
            <td><?= htmlspecialchars($row["product"]) ?></td>
            <td><?= htmlspecialchars($row["quantity"]) ?></td>
            <td><?= htmlspecialchars($row["price"]) ?></td>
            <td><?= htmlspecialchars($row["date"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">No records found</td>
    </tr>
<?php endif;

$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Output the table content and additional data for pagination
echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'totalSystemTally' => number_format($totalValue, 2)
]);
?>
