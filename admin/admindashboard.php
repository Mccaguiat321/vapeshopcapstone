<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['role']) || !isset($_SESSION['branch'])) {
    header("location: ../all_login.php");
    exit; // It's a good practice to exit after sending a header redirect
}
include "../database/db_conn.php";
require 'function.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap4.5.2.css">
    <title>Cloud Keepers</title>
      <!-- <link rel="stylesheet" href="../assets/css/all.css"> -->
      <link rel="stylesheet" href="../assets/css/all.min.css">
      <!-- <link rel="stylesheet" href="../assets/css/fontawesome.css">
      <link rel="stylesheet" href="../assets/css/fontawesome.min.css"> -->

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
 <style>

     /* body{
        overflow-y: hidden;
        background-image: linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url(../images/e2.jpg) !important;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        width: 100%;
        height: 100vh;
     }
 .addbutton:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    padding: 8px 10px !important;
  } .addbutton{
    background-color: #0B192C  !important;
    color: white !important;
    padding: 8px 10px !important;
    font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
  } */
  .modal-content{
    border-radius: 10px;
    padding: 10px;
 }
  .modal-body, .modal-content {
    border-radius: 10px;
    background-color: #F4F6FF;
  }

  #addItemModal > .modal-body {
    padding: 20px; 
    display: flex;
    flex-direction: column; 
    align-items: stretch; 
  }

  #addItemModal > .form-group {
    margin-bottom: 20px; 
  }

  #addItemModal > .form-control {
    border: none; 
    border-bottom: 1px solid black;
    background-color: transparent;
    border-radius: 0;
    box-shadow: none; 
    outline: none; 
    padding: 5px 0;
  }

   .form-control:focus {
    border-bottom: 1px solid #007bff; 
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    
  }

  #addItemModal >  .modal-footer {
    border-top: none; 
    justify-content: flex-end;
    padding: 10px;
  }

  #addItemModal > .btn {
    transition: background-color 0.3s, transform 0.2s;
  }

  #addItemModal > .btn-primary {
    background-color: #007bff;
  }

  #addItemModal > .btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
  }

  #addItemModal > .btn-secondary {
    margin-left: 10px; 
  }


@import url('https://fonts.googleapis.com/css?family=Quicksand:400,700');


*,
*::before,
*::after {
  box-sizing: border-box;
}
html{
    background-color: #ffffff;
}
body {
  color: #272727;
  font-family: 'Quicksand', serif;
  font-style: normal;
  font-weight: 400;
  letter-spacing: 0;



  
  padding: 1rem;
  background: linear-gradient(rgba(0,0,0,0.2),rgba(0,0,0,0.2)), url(../images/flat.jpg);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  width: 100%;
  height: 100vh;

}

.main{
  max-width: 1200px;
  margin: 0 auto;
  background-color: transparent;
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
}

h1 {
    font-size: 24px;
    font-weight: 400;
    text-align: center;
}

.card img {
  max-height: 100%;
  height: 200px;
  max-width: 100%;
  width:500px;

}


.btn {
  color: #ffffff;
  padding: 10px 0;
  font-size: 14px;
  text-transform: uppercase;
  border-radius: 10px;
  font-weight: 400;
  display: block;
  width: 100%;
  cursor: pointer;
  border: 1px solid ;
  background-color: rgba(255, 255, 255, 0.12);
}

.btn:hover {
 
  background-color: #fff;
  color: black;
}

.cards {
  display: flex;
  flex-wrap: wrap;
  list-style: none;
  margin: 0;
  padding: 0;
}

.cards_item {
  display: flex;
  padding: 1rem;
}

@media (min-width: 40rem) {
  .cards_item {
    width: 50%;
  }
}

@media (min-width: 56rem) {
  .cards_item {
    width: 33.3333%;
  }
}

.card {
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 20px 40px -14px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.card_content {
  padding: 1rem;
  background: #181C14;
}

.card_title {
  color: #ffffff;
  font-size: 1.1rem;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: capitalize;
  margin: 0px;
}

.card_text {
  color: #ffffff;
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1.25rem;    
  font-weight: 400;
}
.made_by{
  font-weight: 400;
  font-size: 13px;
  margin-top: 35px;
  text-align: center;
}
.addbutton:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    padding: 8px 10px !important;
  } .addbutton{
    background-color: #0B192C  !important;
    color: white !important;
    padding: 8px 10px !important;
    font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
  width: 50px;
  }

  .addbuttosn:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    padding: 8px 20px !important;
  } .addbuttons{
    background-color: #0B192C  !important;
    color: white !important;
    padding: 8px 10px !important;
    font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
  width: 100px;
  }
  .addbuttona:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    width: 100px;
  } .addbuttona{
    background-color: #0B192C  !important;
    color: white !important;
    padding: 8px 10px !important;
    font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
  width: 100px;
  }
  #search {
    width: 100%; /* Make it adapt to its container */
    max-width: 600px; /* Set a maximum width for larger screens */
    height: 45px;
    margin: 0 auto; /* Center it by default */
    margin-left: 40px; /* Add the desired left margin */
    border: 2px solid #0B192C;
    border-radius: 20px;
    padding: 0 15px; /* Add padding for better appearance */
    box-sizing: border-box; /* Ensure padding doesn't affect width */
}

  .main {
    width: 100%;
    max-height: 680px; /* Limit the maximum height to 400px */
    overflow-y: auto;  /* Enable vertical scrolling */
 /* Optional: Add a border for visibility */

 border-radius: 20px;
 box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;
    padding: 10px; /* Optional: Add padding for a nicer layout */
  }

  /* Optionally style the scrollbar */
  .main::-webkit-scrollbar {
   display: none;
  }

  .main::-webkit-scrollbar-track {
    height: 10px;
  }

  .main::-webkit-scrollbar-thumb {
    height: 10px;
  }

  .main::-webkit-scrollbar-thumb:hover {
    background: #555; 
  }
  @media (max-width: 768px) {
    #search {
        max-width: 400px; /* Reduce the maximum width for medium screens */
        height: 40px; /* Slightly smaller height for smaller screens */
    }
}

@media (max-width: 576px) {
    #search {
        max-width: 100%; /* Take full width on small screens */
        height: 35px; /* Smaller height for very small screens */
    }
}
</style>
</head>

<body>
<!-- <div class="d-flex justify-content-center align-items-center" style="height: 100vh;" >
<div class="glass-container">
   
    

        <div class="table-responsive" style="overflow: auto; max-height: 100%; height: 300px; box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px; border-radius: 15px; padding: 0;">
            <table class="table text-center" id="userTable" style="border-radius: 15px; border-collapse: collapse; width: 100%; margin: 0;">
                <thead class="thead-light sticky-top" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <tr>
                        <th scope="col">Branch</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                  
                </tbody>
            </table>
        </div>
    </div>
</div> -->


<div class="d-flex flex-nowrap align-items-center justify-content-between mb-3 px-lg-5 px-md-4 px-2" style="overflow-x: auto; width: 100%;">
    <!-- Search Input -->
    <input type="text" id="search" class="form-control me-3 " placeholder="Search..." style="flex: 1; min-width: 200px;">

    <!-- Download Button -->
    <button id="downloadBtn" class="btn addbutton me-2" onclick="downloadTable()">
        <i class="fa-solid fa-download"></i>
   

    </button>

    <!-- Print Button -->
    <button id="printBtn" class="btn addbutton me-2" onclick="printTable()">
        <i class="fa-solid fa-print"></i>
    </button>

    <!-- Add Branch Button -->
    <button type="button" class="btn addbutton me-2" id="addBranchButton" title="Add Branch" data-toggle="modal" data-target="#addbranch">
        <img src="../images/branch.svg" id="branchIcon" alt="Add Branch">
    </button>

    <!-- Dropdown Menu -->
    <div class="dropdown">
        <button class="btn dropdown-toggle addbutton" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-user"></i>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a></li>
        </ul>
    </div>
</div>

        <div class="main">
  <div id="tableBody"></div> <!-- This will be populated by the PHP -->
</div>



    <div class="modal fade" id="addbranch" tabindex="-1" role="dialog" aria-labelledby="addBranchModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
          
                <div class="modal-body">
                <button type="button" class="close mb-3" data-dismiss="modal" aria-label="Close" style="float: right; transform:translateY(-12px)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <!-- Form inside the modal -->
                    <form method="post" enctype="multipart/form-data" >
                        <div class="form-group">
                            <label for="input1">Enter Branch Name</label>
                            <input type="text" class="form-control" name="branch" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
        <label for="image" class="form-label">Upload New Image (optional)</label>
        <input type="file" id="image" class="form-control" name="image" accept="image/*">
        <small class="form-text text-muted">Choose an image file to upload. Max size: 5MB.</small>
    </div>



                        <button type="submit" name="addbranch" class="btn addbuttona float-right">Create</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php
$sql = "SELECT * FROM branch";
$result = mysqli_query($conn, $sql);

if ($result) {
    foreach ($result as $row):
        // Fetch the image filename from the database
        $imageFilename = !empty($row['image']) ? htmlspecialchars($row['image']) : null;
        // Construct the image path
        $imagePath = $imageFilename && file_exists("../images/" . $imageFilename) 
            ? "../images/" . $imageFilename 
            : "../images/27002.jpg"; // Default image

        ?>
        <!-- Modal for each row -->
        <div class="modal fade" id="editUserModal<?= htmlspecialchars($row["id"]) ?>" tabindex="-1"
             aria-labelledby="editUserModalLabel<?= htmlspecialchars($row["id"]) ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <ion-icon name="close-circle-outline" data-bs-dismiss="modal" aria-label="Close" class="float-right" 
                                  style="font-size: 20px; cursor: pointer; color: #ff5b5b;"></ion-icon>
                                  <form action="" method="post" enctype="multipart/form-data" class="p-4">
    <!-- Hidden ID field -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($row["id"]) ?>">

    <!-- Row: Display Current Image -->
    <div class="mb-3 text-center">
        <img src="<?= htmlspecialchars($imagePath) ?>" alt="Branch Image" style="max-width: 400px; height: 300px;" class="mb-3">
    </div>

    <!-- Row: Upload New Image -->
    <div class="mb-3">
        <label for="image" class="form-label">Upload New Image (optional)</label>
        <input type="file" id="image" class="form-control" name="image" accept="image/*">
        <small class="form-text text-muted">Choose an image file to upload. Max size: 5MB.</small>
    </div>

    <!-- Row 1: Description -->
    <div class="mb-3">
        <label for="description" class="form-label mb-3">Edit Details</label>
        <input type="text" id="description" class="form-control" 
               name="branch" value="<?= htmlspecialchars($row['branch']) ?>" required>
    </div>

    <!-- Form Buttons -->
    <button type="submit" class="btn addbuttons float-right " name="update" s>
        Update
    </button>
</form>

                    </div>
                </div>
            </div>
        </div>

        <?php
    endforeach;
}
?>
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmLogout">Logout</button>
            </div>
        </div>
    </div>
</div>


 <?php 
 require '../include/footer.php';
 ?>
<script src="../js/ajax.js"></script>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
    document.getElementById('confirmLogout').onclick = function() {
        // Redirect to logout page on confirmation
        window.location.href = "../logout.php";
    };
</script>
    <script>
        <?php
        if (isset($_SESSION['adminsuccess'])) { ?>
            alertify.set('notifier', 'position', 'top-right');
        alertify.success('<?php echo $_SESSION['adminsuccess']; ?>');
            <?php
            unset($_SESSION['adminsuccess']);
        } ?>
    </script>
        <script>
        <?php
        if (isset($_SESSION['success'])) { ?>
            alertify.set('notifier', 'position', 'top-right');
        alertify.success('<?php echo $_SESSION['success']; ?>');
            <?php
            unset($_SESSION['success']);
        } ?>
    </script>
    <script>
        <?php
        if (isset($_SESSION['error'])) { ?>
            alertify.set('notifier', 'position', 'bottom-right');
            alertify.error('<?php echo $_SESSION['error']; ?>');
            <?php
            unset($_SESSION['error']);
        } ?>
    </script>


<script>
$(document).ready(function() {
    function loadData(query = '') {
        $.ajax({
            url: 'livebranch.php', // Your PHP file
            type: 'POST',
            data: { query: query },
            success: function(data) {
                $('#tableBody').html(data); // Update table body with response
            }
        });
    }

    // Initial load
    loadData();

    // Live search
    $('#search').on('input', function() {
        var query = $(this).val();
        loadData(query); // Call loadData with the search query
    });
});
</script>

<script>
    // Using jQuery
    $('#addBranchButton').hover(
        function() {
            $('#branchIcon').attr('src', '../images/branch_hover.svg'); // Change to hover image
        },
        function() {
            $('#branchIcon').attr('src', '../images/branch.svg'); // Change back to original image
        }
    );
</script>


</body>

</html>