<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

$branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$rowsPerPage = 10; // Set the number of rows per page
$offset = ($page - 1) * $rowsPerPage; // Calculate the offset for pagination

// Base SQL query
$sql = "SELECT SQL_CALC_FOUND_ROWS flavor.id, category.category, flavor.date, flavor.brand, flavor.flavor, 
               flavor.description, flavor.quantity, flavor.cost, flavor.price, flavor.image, 
               flavor.low, flavor.manufactured_date
        FROM category 
        INNER JOIN flavor ON category.id = flavor.rs_id 
        INNER JOIN branch ON flavor.f_b_id = branch.id 
        WHERE flavor.date <= NOW() AND flavor.f_b_id = ? 
        AND (category.category LIKE ? OR flavor.flavor LIKE ? OR flavor.description LIKE ?)";

// Check if a date filter is applied
if (!empty($date)) {
    $sql .= " AND DATE(flavor.date) = ?";
}

// Add ORDER BY and pagination clauses
$sql .= " ORDER BY flavor.quantity ASC LIMIT ?, ?";

$searchTerm = "%$searchTerm%";
$params = [$branch_id, $searchTerm, $searchTerm, $searchTerm];
$types = "isss"; // Integer for branch_id, strings for search terms

// If a date is provided, add it to the parameters
if (!empty($date)) {
    $params[] = $date; 
    $types .= "s"; // Add string type for the date
}

// Add pagination parameters
$params[] = $offset;
$params[] = $rowsPerPage;
$types .= "ii";

// Prepare, bind, and execute the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result(); 

// Get the total number of rows (for pagination calculation)
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage); // Calculate total pages

// Start output buffering
ob_start();

$totalAmount = 0; // Initialize total amount

if ($result->num_rows > 0): ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="mc"><?= htmlspecialchars($row["category"]) ?></td>
            <td><?= htmlspecialchars($row["brand"]) ?></td>
            <td><?= htmlspecialchars($row["flavor"]) ?></td>
            <td><?= htmlspecialchars($row["description"]) ?></td>
            <td class="mc">
                <?= htmlspecialchars($row["quantity"]) ?>
            </td>
            <td class="mc"><?= htmlspecialchars($row["cost"]) ?></td>
            <td class="mc"><?= htmlspecialchars($row["price"]) ?></td>
            <td class="mc"><?= htmlspecialchars($row["manufactured_date"]) ?></td>  
            <td>
                <?= htmlspecialchars($row["date"]) ?>
            </td>
        </tr>
        
        <?php 
        $itemTotal = $row["quantity"] * $row["price"]; // Calculate quantity * price for each item
        $totalAmount += $itemTotal; // Add the item total to the overall total
        ?>
    <?php endwhile; ?>

<?php else: ?>
    <tr>
        <td colspan="10" class="text-center">No records found</td>
    </tr>
<?php endif;

// Capture the output buffer content
$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Send the response as JSON
echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'totalSystemTally' => number_format($totalAmount, 2),
]);
?>
