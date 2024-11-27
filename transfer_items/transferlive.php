<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

if (isset($_GET['id']) && isset($_GET['branch'])) {
    $dashboardid = intval($_GET['id']);
    $_SESSION['branch_id'] = $dashboardid;
}

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : ''; // Get the search term, if provided
$date = isset($_POST['date']) ? $_POST['date'] : ''; // Get the date input, if provided
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // Get the current page
$rowsPerPage = 10; // Number of rows per page
$offset = ($page - 1) * $rowsPerPage; // Calculate offset for LIMIT

// Prepare the SQL query to filter by branch_id, date, and search term
$sql = "SELECT SQL_CALC_FOUND_ROWS transfer_history.id, transfer_history.date, branch.branch, 
        transfer_history.quantity, transfer_history.product_name, transfer_history.send_to, transfer_history.price
        FROM transfer_history 
        INNER JOIN branch ON branch.id = transfer_history.send_to 
        WHERE transfer_history.b_id = ?";
$params = [$branchid];
$types = "i"; // Branch ID is an integer

// Add the date filter to the query if the date is provided
if (!empty($date)) {
    $sql .= " AND DATE(transfer_history.date) = ?";
    array_push($params, $date);
    $types .= "s"; // Date is a string
}

// Add search term filter to the query if the search term is provided
if (!empty($searchTerm)) {
    $sql .= " AND (branch.branch LIKE ? OR transfer_history.product_name LIKE ?)";
    $searchTerm = "%$searchTerm%"; // Add wildcards for LIKE query
    array_push($params, $searchTerm, $searchTerm);
    $types .= "ss"; // Search term is a string
}

$sql .= " ORDER BY transfer_history.id ASC LIMIT ?, ?";
array_push($params, $offset, $rowsPerPage);
$types .= "ii"; // Add integer types for LIMIT and OFFSET

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL preparation failed: " . $conn->error);
}

// Bind the parameters
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get the total number of rows
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

$totalAmount = 0; // Initialize the total amount counter
$totalQuantity = 0; // Initialize the total quantity counter

ob_start();
?>

<!-- Output only the <tr> rows -->
<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php 
            $quantity = $row['quantity'];
            $price = $row['price'];
            $totalPerItem = $quantity * $price; // Calculate total for this item
            $totalAmount += $totalPerItem; // Sum up the total amount
            $totalQuantity += $quantity; // Sum up the total quantity
        ?>
        <tr>
            <td><?= htmlspecialchars($row["branch"]) ?></td>
            <td><?= htmlspecialchars($row["product_name"]) ?></td>
            <td><?= htmlspecialchars($row["quantity"]) ?></td>
            <td><?= htmlspecialchars($row["price"]) ?></td>
            <td><?= htmlspecialchars($row["date"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">No records found</td>
    </tr>
<?php endif; ?>

<?php
$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Return data as JSON
echo json_encode([
    'content' => $content,
    'totalSystemTally' => number_format($totalAmount, 2),
    'totalPages' => $totalPages,
    'currentPage' => $page,
]);
?>
