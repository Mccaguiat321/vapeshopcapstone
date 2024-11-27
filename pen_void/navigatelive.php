<?php
session_start();
include '../database/db_conn.php';

if (isset($_GET['id']) && isset($_GET['branch'])) {
    $dashboardid = intval($_GET['id']);
    $_SESSION['branch_id'] = $dashboardid;
}

$branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : ''; // Get the date input
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$rowsPerPage = 10; // Number of rows per page
$offset = ($page - 1) * $rowsPerPage;

// SQL with parameterized queries to prevent SQL injection
$sql = "SELECT SQL_CALC_FOUND_ROWS 
        orders.id AS ORDER_ID,
        orders.ORDER_NO,
        orders.ORDER_DATE,
        orders.UID,
        orders.TOTAL_AMT,
        orders.payment,
        orders.b_id,
        flavor.flavor, 
        order_details.id AS order_details_ID,
        order_details.PRICE, 
        order_details.QTY, 
        order_details.OID,
        order_details.TOTAL 
    FROM 
        orders 
    INNER JOIN 
        order_details ON orders.id = order_details.OID 
    INNER JOIN 
        flavor ON order_details.PID = flavor.id 
    WHERE 
        orders.b_id = ? 
        AND order_details.QTY > 0";

$params = [$branch_id];
$types = "i";

if (!empty($searchTerm)) {
    $sql .= " AND orders.ORDER_NO LIKE ?";
    $searchTerm = "%" . $searchTerm . "%";
    $params[] = $searchTerm;
    $types .= "s"; 
}

if (!empty($date)) {
    $sql .= " AND DATE(orders.ORDER_DATE) = ?";
    $params[] = $date;
    $types .= "s"; 
}

$sql .= " ORDER BY orders.id ASC LIMIT ?, ?";
$params[] = $offset;
$params[] = $rowsPerPage;
$types .= "ii";

// Prepare the statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error in query preparation: " . $conn->error);
}

// Bind parameters and execute
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total rows for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

ob_start();
$totalSystemTally = 0;

if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        
        <td><?= htmlspecialchars($row["ORDER_NO"]) ?></td>
        <td><?= htmlspecialchars($row["flavor"]) ?></td>
        <td><?= htmlspecialchars($row["PRICE"]) ?></td>
        <td><?= htmlspecialchars($row["QTY"]) ?></td>
        <td><?= htmlspecialchars($row["TOTAL"]) ?></td>
        <td><?= htmlspecialchars($row["ORDER_DATE"]) ?></td>
        <td>
            <button type="button" class="btn topbutton" data-toggle="modal" data-target="#exampleModal<?= $row["order_details_ID"] ?>"
            title="Return"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </button>
            <button type="button" class="btn topbutton" data-toggle="modal" data-target="#dispose<?= $row["order_details_ID"] ?>"
            title="Dispose"
            >
                <i class="fa-solid fa-text-slash"></i>
            </button>
        </td>
    </tr>
    <?php $totalSystemTally += $row["TOTAL"]; ?>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center">No records found</td>
    </tr>
<?php endif;

$content = ob_get_clean();

$stmt->close();
$conn->close();

echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'totalSystemTally' => $totalSystemTally
]);
?>
