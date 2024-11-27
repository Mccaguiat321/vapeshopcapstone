<?php
session_start();
include "../database/db_conn.php";

$branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

$rowsPerPage = 10;
$offset = ($page - 1) * $rowsPerPage;

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT SQL_CALC_FOUND_ROWS flavor.id, flavor.status, category.category, flavor.brand, flavor.flavor, flavor.description, flavor.quantity, flavor.cost, flavor.price, flavor.image, flavor.low, flavor.manufactured_date, flavor.date
        FROM category 
        INNER JOIN flavor ON category.id = flavor.rs_id 
        INNER JOIN branch ON flavor.f_b_id = branch.id 
        WHERE branch.id = ? AND flavor.date > NOW() 
        AND (
            flavor.flavor LIKE ? OR 
            flavor.brand LIKE ? OR 
            category.category LIKE ? OR 
            flavor.description LIKE ? OR 
            flavor.manufactured_date LIKE ? OR 
            flavor.cost LIKE ? OR 
            flavor.price LIKE ? OR 
            flavor.quantity LIKE ?      
            OR 
            flavor.low LIKE ? 
            OR 
            flavor.date LIKE ?
        )";

$params = [$branch_id, "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%" , "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%" , "%$searchTerm%", "%$searchTerm%"];
$types = "issssssssss";

// Check if a date filter is applied
$sql .= " LIMIT ?, ?";
array_push($params, $offset, $rowsPerPage);
$types .= "ii";

// Execute the query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    // If the prepare statement failed, show an error
    echo json_encode(['error' => 'Database query preparation failed.']);
    exit();
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    // If execution failed, show an error
    echo json_encode(['error' => 'Failed to execute query.']);
    exit();
}

// Calculate total pages for pagination
$totalRowsResult = $conn->query("SELECT FOUND_ROWS() as total");
if (!$totalRowsResult) {
    echo json_encode(['error' => 'Failed to calculate total rows.']);
    exit();
}

$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

ob_start();
$totalAmount = 0; // Initialize total amount
?>

<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td class="mc"><?= ucwords(htmlspecialchars($row["category"])) ?></td>
            <td><?= htmlspecialchars($row["brand"]) ?></td>
            <td>
                <?php
                $flavor = ucwords(htmlspecialchars($row["flavor"]));
                $flavorShort = substr($flavor, 0, 10);
                $showSeeMore = strlen($flavor) > 10;
                ?>
                <?= $flavorShort ?>
                <?php if ($showSeeMore): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#flavorModal<?= htmlspecialchars($row['id']) ?>" style="color: blue; font-size: 12px">
                        <i class="fa-solid fa-ellipsis"></i>
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <?php
                $description = ucwords(htmlspecialchars($row["description"]));
                $descriptionShort = substr($description, 0, 10);
                $showSeeMore = strlen($description) > 10;
                ?>
                <?= $descriptionShort ?>
                <?php if ($showSeeMore): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#descriptionModal<?= htmlspecialchars($row["id"]) ?>" style="color: blue; font-size: 12px">
                        <i class="fa-solid fa-ellipsis"></i>
                    </a>
                <?php endif; ?>
            </td>
            <td class="mc">
                <span 
                    <?php if ($row["quantity"] < $row['low']) echo 'style="background: red; color: white; padding:10px 20px; border-radius: 5px;"'; ?>>
                    <?= htmlspecialchars($row["quantity"]) ?>
                </span>
            </td>
            <td class="mc"><?= htmlspecialchars($row["cost"]) ?></td>
            <td class="mc"><?= htmlspecialchars($row["price"]) ?></td>
            <td class="mc"><?= htmlspecialchars($row["manufactured_date"]) ?></td>
            <td>
    <?php
    $expirationDate = new DateTime($row["date"]);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($expirationDate);

    $bgColor = "#fff";  // Default background color
    $textColor = "#000"; // Default text color

    // If expiration date is within the current year
    if ($expirationDate->format('Y') == $currentDate->format('Y')) {
        $bgColor = "#FFCCCC";  // Light red for the current year
        $textColor = "#8B0000";  // Dark red text
    } 
    // If expiration date is 1 year ahead
    elseif ($expirationDate->format('Y') == $currentDate->format('Y') + 1) {
        $bgColor = "#D0E4FF";  // Light blue for 1 year ahead
        $textColor = "#1E3A8A";  // Dark blue text
    } 
    // If expiration date is 2 years ahead
    elseif ($expirationDate->format('Y') == $currentDate->format('Y') + 2) {
        $bgColor = "#FFD8B1";  // Light orange for 2 years ahead
        $textColor = "#FF8C00";  // Dark orange text
    } 
    // If the expiration date is more than 2 years ahead
    else {
        $bgColor = "#D0F5D1";  // Light green for more than 2 years ahead
        $textColor = "#228B22";  // Dark green text
    }
    ?>
    <span style="border-radius: 40px; padding: 8px 8px; background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
        <?= htmlspecialchars($row["date"]) ?>
    </span>
</td>

            <td class="mc">
                <span style="border-radius: 40px; padding: 8px 8px; background-color: <?= $row["status"] == 0 ? '#FFECC8' : '#D4F4EC' ?>; color: <?= $row["status"] == 0 ? '#FF9C73' : '#6439FF' ?>;">
                    <?= htmlspecialchars($row["status"] == 0 ? 'On Hold' : 'Active') ?>
                </span>
            </td>
            <td class="mc">
                <button type="button" class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addstocks<?= htmlspecialchars($row["id"]) ?>" 
                        style="background-color: #f77f00; border: 1px solid black; margin-right: 10px; border-radius: 10px; color: black;"
                        onmouseover="this.style.backgroundColor='#481E14'; this.style.color='white'" 
                        onmouseout="this.style.backgroundColor='#f77f00'; this.style.color='black'">
                   <img src="../images/addbag.svg" alt="">
                </button>
                <button type="button" class="btn btn-primary" 
                        data-bs-toggle="modal"
                        data-bs-target="#editUserModal<?= htmlspecialchars($row["id"]) ?>"
                        style="background: #003049; border: 1px solid black; margin-right: 10px; border-radius: 10px; color: black;"
                        onmouseover="this.style.backgroundColor='#db3a34'; this.style.color='white'"
                        onmouseout="this.style.backgroundColor='#003049'; this.style.color='black'">
                    <i class="fa-solid fa-pen-to-square" style="color: white;"></i>
                </button>
            </td>
        </tr>
        <?php 
        $itemTotal = $row["quantity"] * $row["price"];
        $totalAmount += $itemTotal;
        ?>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="11" class="text-center">No records found on this page</td>
    </tr>
<?php endif; ?>

<?php
$content = ob_get_clean();
$stmt->close();
$conn->close();

$response = [
    "content" => $content, // Table rows content
    "totalSystemTally" => number_format($totalAmount, 0), // Total tally
    "totalPages" => $totalPages // Total pages for pagination
];
echo json_encode($response);
?>
