<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$rowsPerPage = 12; // Number of rows per page
$offset = ($page - 1) * $rowsPerPage; // Calculate the offset

// Prepare the SQL statement with pagination and filtering
$sql = "SELECT SQL_CALC_FOUND_ROWS users.id, user_name, email, password, role, branch.branch 
        FROM users 
        INNER JOIN branch ON branch.id = users.b_id 
        WHERE branch.id = ? AND (user_name LIKE ? OR email LIKE ?)
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL preparation failed: " . $conn->error);
}

$searchTerm = "%$searchTerm%";
$stmt->bind_param("issii", $branchid, $searchTerm, $searchTerm, $offset, $rowsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Get the total number of rows for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc();
$totalRows = $totalResult['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Start output buffering
ob_start();
?>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["user_name"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
            <td><?= htmlspecialchars($row["role"]) ?></td>
            <td>
                <button type="button" class="btn btn-primary"
                    style="background: transparent; border: 1px solid black; margin-right: 10px; border-radius: 10px"
                    onmouseover="this.style.backgroundColor='red'; this.style.color='white'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='red'">
                    <a href="delete_inventory.php?id=<?= htmlspecialchars($row['id']) ?>"
                        class="text-white text-decoration-none"
                        onclick="return confirm('Are you sure you want to delete this item?');">
                        <i class="fa-solid fa-trash" style="color: black"></i>
                    </a>
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="4" class="text-center">No records found</td>
    </tr>
<?php endif; ?>

<?php
// Get the content from the output buffer and clean it
$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Send the generated HTML along with pagination data as JSON
echo json_encode([
    'content' => $content,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);
?>
