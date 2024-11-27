<?php
session_start();
include '../database/db_conn.php'; // Include your database connection file

// Get the search term (if provided)
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';

// Prepare the SQL statement to select from the branch table with a search filter
$sql = "SELECT * FROM branch WHERE branch LIKE ?";
$searchTerm = "%$searchTerm%";  // Use wildcards for partial match search

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $searchTerm);  // Bind the search term to the query
$stmt->execute();
$result = $stmt->get_result();  // Execute the query and get the result set

// Default image path
$defaultImage = "../images/27002.jpg";

// Start output buffering
ob_start();

if ($result->num_rows > 0): ?>
    <ul class="cards">
    <?php while ($row = $result->fetch_assoc()): ?>
        <li class="cards_item">
            <div class="card">
                <?php
                // Fetch the image filename from the database
                $imageFilename = !empty($row['image']) ? htmlspecialchars($row['image']) : null;
                // Construct the image path
                $imagePath = $imageFilename && file_exists("../images/" . $imageFilename) 
                    ? "../images/" . $imageFilename 
                    : $defaultImage;
                ?>
                <div class="card_image">
                    <img src="<?= $imagePath ?>" alt="Branch Image" >
                </div>
                <div class="card_content">
                    <h2 class="card_title"><?= htmlspecialchars($row["branch"]) ?></h2>
                    <p class="card_text">Branch details for <?= htmlspecialchars($row["branch"]) ?>.</p>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-primary"
                            style="margin-right: 5px;"
                            data-bs-toggle="modal"
                            data-bs-target="#editUserModal<?= htmlspecialchars($row["id"]) ?>">
                            <i class="fa-solid fa-pen-to-square" style="font-weight: bolder;"></i> Edit
                        </button>
                        <a href="pendashboard.php?id=<?= htmlspecialchars($row["id"]) ?>&branch=<?= htmlspecialchars($row["branch"]) ?>"
                           class="btn btn-primary"
                           style="margin-left: 5px;">
                           <i class="fa-solid fa-screwdriver-wrench"></i> Manage
                        </a>
                    </div>
                </div>
            </div>
        </li>
    <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p class="text-center">No records found</p>
<?php endif; ?>

<?php
// Get the content from the output buffer and clean it
$content = ob_get_clean();

// Close the statement and connection
$stmt->close();
$conn->close();

// Send the generated HTML
echo $content;
?>
