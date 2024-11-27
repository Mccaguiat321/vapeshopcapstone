<?php
session_start();
include '../database/db_conn.php';

$branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$rowsPerPage = 12;
$offset = ($page - 1) * $rowsPerPage;

// Prepare the main SQL statement with filtering
$sql = "SELECT SQL_CALC_FOUND_ROWS daily_tally.*, users.user_name 
        FROM daily_tally 
        INNER JOIN users ON users.id = daily_tally.user_id 
        WHERE daily_tally.b_id = ?";

$params = [$branch_id];
$types = "i";

// Apply filters to the main query
if (!empty($searchTerm)) {
    $sql .= " AND (
        users.user_name LIKE ? 
        OR daily_tally.stafftally LIKE ? 
        OR daily_tally.systemtallly LIKE ? 
        OR daily_tally.result_tally LIKE ? 
        OR daily_tally.result LIKE ? 
        OR DATE_FORMAT(daily_tally.date, '%Y-%m-%d') LIKE ?
    )";
    $searchTerm = "%$searchTerm%";
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $types .= str_repeat('s', 6);
}

if (!empty($date)) {
    $sql .= " AND DATE(daily_tally.date) = ?";
    array_push($params, $date);
    $types .= "s";
}

// Pagination limits for the main query
$sql .= " LIMIT ?, ?";
array_push($params, $offset, $rowsPerPage);
$types .= "ii";

// Prepare and execute the main query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get total rows for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Calculate the total system tally across all pages with a separate query
$totalTallySql = "SELECT SUM(daily_tally.systemtallly) AS totalSystemTally
                  FROM daily_tally
                  INNER JOIN users ON users.id = daily_tally.user_id 
                  WHERE daily_tally.b_id = ?";

$tallyParams = [$branch_id];
$tallyTypes = "i";

// Add the same filters to the tally query
if (!empty($searchTerm)) {
    $totalTallySql .= " AND (
        users.user_name LIKE ? 
        OR daily_tally.stafftally LIKE ? 
        OR daily_tally.systemtallly LIKE ? 
        OR daily_tally.result_tally LIKE ? 
        OR daily_tally.result LIKE ? 
        OR DATE_FORMAT(daily_tally.date, '%Y-%m-%d') LIKE ?
    )";
    array_push($tallyParams, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $tallyTypes .= str_repeat('s', 6);
}

if (!empty($date)) {
    $totalTallySql .= " AND DATE(daily_tally.date) = ?";
    array_push($tallyParams, $date);
    $tallyTypes .= "s";
}

// Execute the tally query
$tallyStmt = $conn->prepare($totalTallySql);
$tallyStmt->bind_param($tallyTypes, ...$tallyParams);
$tallyStmt->execute();
$tallyResult = $tallyStmt->get_result();
$totalSystemTally = $tallyResult->fetch_assoc()['totalSystemTally'];

// Output data
ob_start();

if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="mc"><?= htmlspecialchars($row["user_name"]) ?></td>
            <td><?= htmlspecialchars($row["stafftally"]) ?></td>
            <td><?= htmlspecialchars($row["systemtallly"]) ?></td>
            <td><?= htmlspecialchars($row["result_tally"]) ?></td>
            <td class="mc"><?= htmlspecialchars($row["result"]) ?></td>
            <td class="mc"><?= htmlspecialchars($row["date"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center">No records found</td>
    </tr>
<?php endif;

$content = ob_get_clean();

$stmt->close();
$tallyStmt->close();
$conn->close();

echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'totalSystemTally' => $totalSystemTally
    
]);
?>
