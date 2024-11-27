<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : ''; // Get the date input if provided
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$rowsPerPage = 10;
$offset = ($page - 1) * $rowsPerPage;

// Prepare the SQL query with placeholders to prevent SQL injection
$sql = "
    SELECT SQL_CALC_FOUND_ROWS orders.ORDER_NO, orders.id AS order_id, orders.ORDER_DATE, orders.TOTAL_AMT, orders.payment, branch.branch 
    FROM orders 
    INNER JOIN branch ON branch.id = orders.b_id 
    WHERE branch.id = ? 
    AND (orders.ORDER_NO LIKE ? OR orders.ORDER_DATE LIKE ? OR orders.TOTAL_AMT LIKE ? OR orders.payment LIKE ?)
";

// Prepare parameters and types
$params = [$branchid, "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
$types = "issss";

// Add a date filter to the query if a date is provided
if (!empty($date)) {
    $sql .= " AND DATE(orders.ORDER_DATE) = ?";
    $params[] = $date;
    $types .= "s";
}

// Append LIMIT for pagination
$sql .= " LIMIT ?, ?";
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

// Get the total rows for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Calculate the total amount across all pages
$totalAmountSql = "
    SELECT SUM(orders.TOTAL_AMT) AS totalAmount
    FROM orders 
    INNER JOIN branch ON branch.id = orders.b_id 
    WHERE branch.id = ? 
    AND (orders.ORDER_NO LIKE ? OR orders.ORDER_DATE LIKE ? OR orders.TOTAL_AMT LIKE ? OR orders.payment LIKE ?)
";

$totalAmountParams = [$branchid, "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
$totalAmountTypes = "issss";

// Add the same date filter to the total amount query
if (!empty($date)) {
    $totalAmountSql .= " AND DATE(orders.ORDER_DATE) = ?";
    $totalAmountParams[] = $date;
    $totalAmountTypes .= "s";
}

$totalAmountStmt = $conn->prepare($totalAmountSql);
$totalAmountStmt->bind_param($totalAmountTypes, ...$totalAmountParams);
$totalAmountStmt->execute();
$totalAmountResult = $totalAmountStmt->get_result();
$totalAmount = $totalAmountResult->fetch_assoc()['totalAmount'];

// Start output buffering
ob_start();

if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["ORDER_NO"]) ?></td>
            <td><?= htmlspecialchars($row["ORDER_DATE"]) ?></td>
            <td><?= htmlspecialchars($row["TOTAL_AMT"]) ?></td>
            <td><?= htmlspecialchars($row["payment"]) ?></td>
            <td>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dispose<?= htmlspecialchars($row["order_id"]) ?>">
                <i class="fa-solid fa-list"></i>
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">No records found.</td>
    </tr>
<?php endif;

$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$totalAmountStmt->close();
$conn->close();

// Send the generated HTML (tbody only)
echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'totalSystemTally' => number_format($totalAmount, 2)
]);
?>
