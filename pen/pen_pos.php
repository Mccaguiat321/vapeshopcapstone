<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../all_login.php");
    exit();
}
require 'penview.php';
require '../database/db_conn.php';
$userid = $_SESSION['user_id'];
$role = $_SESSION['role'];
$user_name = $_SESSION['user_name'];
$branchid = $_SESSION['branch'];
$branchname = $_SESSION['branchname'];
$sql = "SELECT sod  FROM users WHERE id = ?"; // Use your condition to get the correct user
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid); // Replace $userId with the actual user ID
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$sodValue = $row['sod'] ?? ''; // Default to empty string if no value found


$sql = "SELECT sod, nsod, status FROM users WHERE id = $userid AND b_id  = $branchid";
$result = $conn->query($sql);

$sod_value = 0; // Default to 0 for sod
$nsod_value = 0; // Default to 0 for nsod
$status = 0; // Default to 0 for status

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sod_value = (int)$row['sod']; // Cast to integer for safety
    $nsod_value = (int)$row['nsod']; // Cast to integer for safety
    $status = (int)$row['status']; // Cast to integer for safety
}
// function updateQuantityAndTotal($conn, $itemId, $newQty) {
//     // Sanitize inputs to prevent SQL injection
//     $itemId = mysqli_real_escape_string($conn, $itemId);
//     $newQty = mysqli_real_escape_string($conn, $newQty);
    
//     // Get the price from the database for the item
//     $priceSql = "SELECT price FROM customer_cart WHERE id = '$itemId'";
//     $priceResult = mysqli_query($conn, $priceSql);
//     if ($priceResult && mysqli_num_rows($priceResult) > 0) {
//         $priceRow = mysqli_fetch_assoc($priceResult);
//         $price = $priceRow['price'];
//     } else {
//         // Return false if price retrieval fails
//         return false;
//     }

//     // Calculate the new total
//     $newTotal = $newQty * $price;

//     // Update the quantity and total in the database
//     $updateSql = "UPDATE customer_cart SET qty = '$newQty', total = '$newTotal' WHERE id = '$itemId'";
//     if(mysqli_query($conn, $updateSql)) {
//         return true;
//     } else {
//         return false;
//     }

// }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the request is for updating the cart, checking the presence of the required fields
    if (isset($_POST['itemid']) && isset($_POST['qty'])) {
        // Extract the item ID and quantity from the POST request
        $itemId = (int)$_POST['itemid'];
        $newQty = (int)$_POST['qty'];

        // Proceed only if the item ID and new quantity are valid (i.e., positive numbers)
        if ($itemId > 0 && $newQty > 0) {
            // Fetch the available quantity from the `flavor` table
            $stmtFlavor = $conn->prepare("SELECT quantity FROM flavor WHERE id = (SELECT itemid FROM cart WHERE id = ?)");
            $stmtFlavor->bind_param('i', $itemId);
            $stmtFlavor->execute();
            $resultFlavor = $stmtFlavor->get_result();
            $availableQty = 0;

            if ($resultFlavor->num_rows > 0) {
                $rowFlavor = $resultFlavor->fetch_assoc();
                $availableQty = $rowFlavor['quantity'];
            }

            // Check if the requested quantity exceeds available stock
            if ($newQty > $availableQty) {
                echo json_encode(['error' => 'Not enough stock available.']);
                exit;
            }

       
            $stmtPrice = $conn->prepare("SELECT price FROM cart WHERE id = ?");
            $stmtPrice->bind_param('i', $itemId);
            $stmtPrice->execute();
            $resultPrice = $stmtPrice->get_result();

            if ($resultPrice->num_rows > 0) {
                $rowPrice = $resultPrice->fetch_assoc();
                $price = $rowPrice['price'];
                $newTotal = $newQty * $price;

                // Update the cart with the new quantity and total
                $stmtUpdate = $conn->prepare("UPDATE cart SET qty = ?, total = ? WHERE id = ?");
                $stmtUpdate->bind_param('idi', $newQty, $newTotal, $itemId);
                $stmtUpdate->execute();

                if ($stmtUpdate->affected_rows > 0) {
                    // Respond with the new quantity, total, and available quantity
                    echo json_encode(['qty' => $newQty, 'total' => $newTotal, 'availableQty' => $availableQty]);
                } else {
                    echo json_encode(['error' => 'Failed to update the cart.']);
                }
            } else {
                echo json_encode(['error' => 'Item not found in the cart.']);
            }

            // Close the prepared statements
            $stmtPrice->close();
            $stmtUpdate->close();
            $stmtFlavor->close();
        } else {
            echo json_encode(['error' => 'Invalid item ID or quantity.']);
        }
        exit;
    }
}
    
if (isset($_POST['formType']) && $_POST['formType'] === 'categoryFilter') {
    // Store the selected category in the session
    $_SESSION['selectedCategory'] = isset($_POST['categoryss']) ? $_POST['categoryss'] : 'clear';
    // Redirect to the first page
    header("Location: ?page=1");
    exit; // Stop further execution
} elseif (isset($_GET['category'])) {
    // Capture category from URL (for consistency)
    $_SESSION['selectedCategory'] = $_GET['category'];
}



    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap4.5.2.css">
    <title>Cloud Keepers
    </title>
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&family=Nunito:ital,wght@0,688;1,688&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&family=Sixtyfour+Convergence&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sixtyfour+Convergence&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    /* width */

    body{
        background-color: white;
        overflow: hidden; /* Hide scrollbars globally */
    }
    ::-webkit-scrollbar {
        width: 5px;
    }
    ::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px #fff;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: #fff;
        border-radius: 10px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #fff;
    }
    .division{
        display: flex;
    }
    .left{
        flex-basis: 79.5%;
        margin-top: 1%;
    }
    .right{
        flex-basis: 20.5%;
        
    }
    #seachmala{
        width: 65.7%;
        margin-right: 4.5%;
    }
    td,th{
      
        text-transform: uppercase;
        font-size: 12px;
        border:  none !important;
          
    }

    th{
        padding: 5px 10px !important;
        font-size: 12px;
        color: white;
        border: none !important;
    }
    .right{
        width: 190px !important;
    }
    .colorofcart{
    color: #fff !important;
    }
    .colorofsubtotal{
        color:black;
    }
    .colorofsubtotals {
   
    background: black;
    border: none;
    box-shadow: none;
    color:black;
}
.colorofsubtotals {
   
    background-color: transparent; 
    border: none; 
    outline: none; 
    
    transform: translateX(-20px);
}
.line{
    width: 89%;
    background-color:#fffffe;
    margin-right: 20px;
    border-top: 2px  dotted #161D6F;
    transform: translateY(-12px);
    
  
    
}
.div{
    width: 76%;
    height: 90px;
    
    border-top: solid 2px #161D6F;
    border-radius: 10px;
    transform: translateY(-68px);
    margin-left: 31px;
    position: absolute;
    display: flex;
  
}
.button, .btn{
    background-color: black;
    color: #fff;
}
.button, .btn:hover{
    background-color: #FFF5E4; 
    color: black;
}
#buttoncashier{
  
    background-color: #F5F7F8;
    color: black;

}
#buttoncashier:hover{
    background-color: #FFF5E4;
    color: black;

}
#buttoncashiers{
  
  background-color: #F5F7F8;
  color: black;

}
#buttoncashiers:hover{
  background-color: #FFF5E4;
  color: black;

}
#buttoncashierss{
  
  background-color: #F5F7F8;
  color: black;

}
#buttoncashierss:hover{
  background-color: #FFF5E4;
  color: black;

}
.wor1, .wor2,.wor3{
    margin-top: 5px;
    height: 73px;
    width: 400px;
  display: flex;
    margin-right: 4px;
    border: 1px solid #161D6F;
    color:black;
    border-radius: 10px;
box-shadow: rgba(0, 0, 0, 0.15) 0px 15px 25px, rgba(0, 0, 0, 0.05) 0px 5px 10px;
}
.logout-button {
   
    color: black;              /* Text color */
    border: none;    
    height: 40px;          /* No border */
    padding: 2px 20px !important;       /* Padding */
    font-size: 16px;          /* Font size */
    cursor: pointer;           /* Pointer cursor on hover */
    border-radius: 5px;       /* Rounded corners */
    transition: background-color 0.3s; /* Transition effect */
}

.logout-button:hover {
    background-color: #FFF5E4; /* Darker shade on hover */
}
.profile{
    width: 60px;
    height: 60px;
    margin: 5px;
    
    
}
.profile > img{
    width: 50px;
    height: 50px;
    margin: 5px;
    
}
.store > img{
    width: 60px;
    height: 64px;
    margin: 5px;
    
}

.usernamestyle{
    font-size: 30px;
    line-height: 40px;
    text-transform: uppercase;
    margin: 17px;
    letter-spacing: 2px;
    font-family: "Poppins", sans-serif;
  font-weight: bold;
  font-style: normal;
 
}

.usernamestyles{
    
    font-size: 20px;

    text-transform: uppercase;
    margin: 8px 0 0 0  !important;
    letter-spacing: 2px;
    font-family: "Poppins", sans-serif;
  font-weight: bold;
  font-style: normal;
 
}
.timeinph{
    display: flex;
    flex-direction: column;
    margin: 0;
    gap: 0;

}
.priceandflavortext{
    margin-top: 4px;
    display: flex;
    justify-content: space-around;
    color: #16161a;
    font-size: 15px;
    font-family: "Noto Serif", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-variation-settings:
    "wdth" 100;
}
.price{
    color:black;
}
.container::-webkit-scrollbar {
    display: none; 
}
#tablenaten::-webkit-scrollbar{
    display: none; 
    overflow: hidden; 
    overflow-y: auto;
}
#aid{
    color: #16161a !important;
    text-decoration: none;
}
#buttonofplaceorder{
    font-family: "Noto Serif", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  color: #D4F6FF;
  font-style: normal;
  background-color: #161D6F;
  font-variation-settings:
    "wdth" 100;
    }
    #buttonofplaceorder:hover{
   
  color: #133E87;
 
  background-color: #A2D2DF;

    }
    .D4F6FF{
        font-family: "Noto Serif", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  color: #D4F6FF;
  font-style: normal;
  background-color: #608BC1;
  font-variation-settings:
  "wdth" 100;
    }
    .D4F6FF:hover{
       
  color: #D4F6FF;
 
  background-color: #A2D2DF;

    }
    .sodimg{
        width: 20px;
        height: 20px;
    }
    .z-reading{
        width: 20px;
        height: 20px;
    }
    #cashiermoneydisplay{
        color: black !important;
        width: 100%;
        font-family: "Noto Serif", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-variation-settings:
    "wdth" 100;
    }
    .modal-header {
            background-color: black;
            color: w;
            border-bottom: 2px solid #495057;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            justify-content: center;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .form-control {
            border-radius: 0.25rem;
            box-shadow: none;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .modal-title {
            font-weight: bold;
        }
    </style>
     <style>
        /* Additional styling if needed */
        .dropdown-toggle::after {
            margin-left: 0.5em; /* Space between text and arrow */
        }
        .dropdown > button{
            background-color: #F4F6FF !important; 
            color: black; 
        }
        .dropdown > button:hover{
            background-color: black !important; 
            color: #FFF5E4; 
        }
        #categoryss{
            border: 1px solid black;
            border-radius: 10px;
        }
        #searchInput{
            border: 1px solid black;
            border-radius: 10px;
            background-color: #F4F6FF;
            color: black;
            max-width: 70%;
            width: 200px;
        }
        #searchInput::placeholder{
          
            color: whblackblackite;
        }
        .mc{
            color: black !important; 
        }
        .tr{
            border-bottom:   1px solid black;
            padding: 0 !important;
        }
    </style>
<style>
  #cashierModal .modal-dialog {
    max-width: 300px; /* Set max width */
    width: 100%; /* Responsive width */
  }
  #checkoutModal .modal-dialog {
    max-width:400px; /* Set max width */
    width: 100%; /* Responsive width */

  }
  #checkoutModal .modal-body {
    max-height: 100%;
    height: 500px;
   
  }
 
  #cashierModal .modal-content {
    border-radius: 20px; /* Apply border radius */
    background-color: #F4F6FF; /* Set background color */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Add shadow */
  }

  #cashierModal .modal-body {
    padding: 20px; /* Add padding */
    display: flex;
    flex-direction: column; /* Align content vertically */
    align-items: stretch; /* Make items stretch to full width */
  }

  #cashierModal .form-group {
    margin-bottom: 20px; /* Add margin for spacing */
  }

  #cashierModal .form-control {
    border: none; /* Remove default border */
    border-bottom: 1px solid black; /* Add bottom border */
    background-color: transparent;
    border-radius: 0; /* Remove border radius */
    box-shadow: none; /* Remove default shadow */
    outline: none; /* Remove outline on focus */
    padding: 5px 0; /* Add padding for better spacing */
  }

  #cashierModal .form-control:focus {
    border-bottom: 1px solid #007bff; /* Change bottom border color on focus */
  }

  #cashierModal .modal-footer {
    border-top: none; /* Remove footer border */
    justify-content: flex-end; /* Align buttons to the right */
    padding: 10px; /* Add padding */
  }

  #cashierModal .btn {
    transition: background-color 0.3s, transform 0.2s; /* Smooth transition */
  }

  #cashierModal .btn-primary {
    background-color: #007bff; /* Bootstrap primary color */
  }

  #cashierModal .btn-primary:hover {
    background-color: #0056b3; /* Darker on hover */
    transform: translateY(-2px); /* Lift effect */
  }

  #cashierModal .btn-secondary {
    margin-left: 10px; /* Spacing between buttons */
  }
  label{
    font-family: "Raleway", sans-serif;
  font-optical-sizing: auto;
  font-weight: 500;
  font-style: normal;
  }
</style>

<style>
  .modal-dialog {
    max-width: 400px; /* Set max width */
    width: 100%; /* Responsive width */
  }

  .modal-content {
    border-radius: 20px; /* Apply border radius */
    background-color: #F4F6FF; /* Set background color */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Add shadow */
  }

  .modal-body {
    padding: 20px; /* Add padding */
    display: flex;
    flex-direction: column; /* Align content vertically */
    align-items: stretch; /* Make items stretch to full width */
  }

  .form-group {
    margin-bottom: 20px; /* Add margin for spacing */
  }

  .form-control {
    border: none; /* Remove default border */
    border-bottom: 1px solid black; /* Add bottom border */
    background-color: transparent;

    border-radius: 0; /* Remove border radius */
    box-shadow: none; /* Remove default shadow */
    outline: none; /* Remove outline on focus */
    padding: 5px 0; /* Add padding for better spacing */
  }

  .form-control:focus {
    border-bottom: 1px solid #007bff; /* Change bottom border color on focus */
  }

  .modal-footer {
    border-top: none; /* Remove footer border */
    justify-content: flex-end; /* Align buttons to the right */
    padding: 10px; /* Add padding */
  }

  .btn {
    transition: background-color 0.3s, transform 0.2s; /* Smooth transition */
  }

  .btn-primary {
    background-color: #007bff; /* Bootstrap primary color */
  }

  .btn-primary:hover {
    background-color: #0056b3; /* Darker on hover */
    transform: translateY(-2px); /* Lift effect */
  }

  .btn-secondary {
    margin-left: 10px; /* Spacing between buttons */
  }
</style>
<style>
  .modal-dialog {
    max-width: 400px; /* Set max width */
    width: 100%; /* Responsive width */
  }

  .modal-content {
    border-radius: 20px; /* Apply border radius */
    background-color: #F4F6FF; /* Set background color */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Add shadow */
  }

  .modal-body {
    padding: 20px; /* Add padding */
    display: flex;
    flex-direction: column; /* Align content vertically */
    align-items: stretch; /* Make items stretch to full width */
  }

  .form-group {
    margin-bottom: 20px; /* Add margin for spacing */
  }

  .form-control {
    border: none; /* Remove default border */
    border-bottom: 1px solid black; /* Add bottom border */
    background-color: transparent;
    border-radius: 0; /* Remove border radius */
    box-shadow: none; /* Remove default shadow */
    outline: none; /* Remove outline on focus */
    padding: 5px 0; /* Add padding for better spacing */
  }

  .form-control:focus {
    border-bottom: 1px solid #007bff; /* Change bottom border color on focus */
  }

  .modal-footer {
    border-top: none; /* Remove footer border */
    justify-content: flex-end; /* Align buttons to the right */
    padding: 10px; /* Add padding */
  }

  .btn {
    transition: background-color 0.3s, transform 0.2s; /* Smooth transition */
  }

  .btn-primary {
    background-color: #007bff; /* Bootstrap primary color */
  }

  .btn-primary:hover {
    background-color: #0056b3; /* Darker on hover */
    transform: translateY(-2px); /* Lift effect */
  }

  .btn-secondary {
    margin-left: 10px; /* Spacing between buttons */
  }

  .food-trip{
    box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;
}
/* Background Image with Blur Effect */
/* Styling for Top-Right Blurred Logo */
.logovape{
    display: flex;
    justify-content: space-between;
    font-family: "Poppins", sans-serif;
  font-weight: bold;
  font-style: normal;
}
.plus ,.minus{
    background-color: #608BC1;
    color: #CBDCEB;
}
.plus:hover{
    background-color: #CBDCEB;
    color: #608BC1;
}
.minus:hover{
    background-color: red;
    color: black;
}
/* Scoped container for cashier modal input */
/* Scoped container for cashier modal input */
.cashier-input-group {
  position: relative;
  margin-bottom: 5px;
}

.cashier-input-group input {
  font-size: 18px;
  padding: 10px 10px 10px 5px;
  display: block;
  width: 248px;
  border: none;
  border-bottom: 1px solid #757575;
  background-color: transparent;
}

.cashier-input-group input:focus {
  outline: none;
}

/* Label styles */
.cashier-input-group label {
  color: #999;
  font-size: 18px;
  font-weight: normal;
  position: absolute;
  pointer-events: none;
  left: 5px;
  top: 10px;
  transition: 0.2s ease all;
}

/* Active state for label */
.cashier-input-group input:focus ~ label,
.cashier-input-group input:valid ~ label {
  top: -20px;
  font-size: 14px;
  color: #5264AE;
}

/* Bottom bar styles */
.cashier-input-group .bar {
  position: relative;
  display: block;
  width: 248px;
}

.cashier-input-group .bar:before,
.cashier-input-group .bar:after {
  content: '';
  height: 2px;
  width: 0;
  bottom: 1px;
  position: absolute;
  background: #5264AE;
  transition: 0.2s ease all;
}

.cashier-input-group .bar:before {
  left: 50%;
}

.cashier-input-group .bar:after {
  right: 50%;
}

/* Active state for bottom bar */
.cashier-input-group input:focus ~ .bar:before,
.cashier-input-group input:focus ~ .bar:after {
  width: 50%;
}

/* Highlighter effect */
.cashier-input-group .highlight {
  position: absolute;
  height: 60%;
  width: 100px;
  top: 25%;
  left: 0;
  pointer-events: none;
  opacity: 0.5;
}

/* Active state for highlighter */
.cashier-input-group input:focus ~ .highlight {
  animation: inputHighlighter 0.3s ease;
}

/* Highlighter animation */
@keyframes inputHighlighter {
  from { background: #5264AE; }
  to { width: 0; background: transparent; }
}

</style>


</head>

<body >
    <div class="division">
    <div class="left">
    <div class='mt-2 pb-2 d-flex' id="cate">
    <div class="form-group mb-0 mr-2">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                <input type="hidden" name="formType" value="categoryFilter">
                <select class="form-control" name="categoryss" id="categoryss" style="width: 200px; margin-left:36px; background:#161D6F; color: #fff;font-weight:600;letter-spacing:1px;text-align:center" onchange="this.form.submit()">
                    <option value="clear" <?php if (!isset($_SESSION['selectedCategory']) || $_SESSION['selectedCategory'] === "clear") echo "selected"; ?>>
                        All Category
                    </option>
                    <?php
                    // Fetch categories
                    $categorySql = "SELECT id, category FROM category WHERE c_b_id = $branchid";
                    $categoryResult = mysqli_query($conn, $categorySql);
                    if ($categoryResult) {
                        while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                            $categoryId = htmlspecialchars($categoryRow["id"]);
                            $categoryName = htmlspecialchars($categoryRow["category"]);
                    ?>
                            <option value="<?= $categoryId ?>" <?php if (isset($_SESSION['selectedCategory']) && $_SESSION['selectedCategory'] === $categoryId) echo "selected"; ?>>
                                <?= $categoryName ?>
                            </option>
                    <?php
                        }
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }
                    ?>
                </select>
            </form>
        </div>
        
           <!-- <div class="input-group" id="searchmala" style="position: relative;">
    <input type="text" class="form-control" name="search" id="searchInput" placeholder="Search..." 
           onkeyup="searchProducts()" style="text-align: center; padding-right: 40px;">
    <span class="input-group-text" style="position: absolute; top: 50%; right: 31%; transform: translateY(-50%); background-color: transparent; border: none;">
        <i class="fa-solid fa-magnifying-glass" style="color: black;"></i>
    </span>
</div> -->


        <div class="dropdown" style="margin-left:70%;background-color:#161D6F;border-radius:10px">
            <button class="btn dropdown-toggle" style="background-color: #161D6F !important;border-radius:10px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user" style="color:white"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Start displaying products based on selected category -->
    <div class="container food-trip" style="height: 600px; overflow-y: auto;" id="scrollable-container">
    <?php
    // Set items per page
    $itemsPerPage = 18;

    // Get the current page from URL, default to 1
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Retrieve the selected category from the session
    $categoryId = isset($_SESSION['selectedCategory']) ? $_SESSION['selectedCategory'] : "clear";

    // Prepare the base SQL statement
    $sql = "SELECT flavor.brand, flavor.flavor, flavor.price, flavor.id, flavor.image, category.category, flavor.quantity, flavor.description, flavor.cost
            FROM flavor 
            INNER JOIN category ON flavor.rs_id = category.id 
            WHERE flavor.f_b_id = ? AND flavor.status = 1 AND flavor.quantity > 0 AND flavor.date > NOW() ";

    // Add category filter if not "clear"
    if ($categoryId !== "clear") {
        $sql .= " AND flavor.rs_id = ?";
    }

    // Prepare SQL for counting total items
    $countSql = "SELECT COUNT(*) as total FROM flavor WHERE f_b_id = ? AND status = 1 AND quantity > 0 AND date > NOW()";
    if ($categoryId !== "clear") {
        $countSql .= " AND rs_id = ?";
    }

    // Get the total count of items for pagination
    $countStmt = mysqli_prepare($conn, $countSql);
    if ($categoryId !== "clear") {
        mysqli_stmt_bind_param($countStmt, "ii", $branchid, $categoryId);
    } else {
        mysqli_stmt_bind_param($countStmt, "i", $branchid);
    }
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $totalItems = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Append limit and offset to the main query
    $sql .= " LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Check if we need to bind the category parameter
    if ($categoryId !== "clear") {
        mysqli_stmt_bind_param($stmt, "iiii", $branchid, $categoryId, $itemsPerPage, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $branchid, $itemsPerPage, $offset);
    }

    // Execute the statement
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Default image path
    $defaultImage = "../images/27002.jpg";

    // Display products
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $flavor = htmlspecialchars($row['flavor']);
            $flavorCapitalized = ucfirst($flavor); // Capitalize the first letter of the flavor
            $flavorShort = substr($flavorCapitalized, 0, 10); // Get the first 10 letters
            $showSeeMore = strlen($flavor) > 10; // Check if flavor exceeds 10 letters

            // Check if image exists, otherwise use the default image
            $imagePath = !empty($row['image']) && file_exists("../images/" . $row['image']) ? "../images/" . htmlspecialchars($row['image']) : $defaultImage;
    ?>
            <div>
                <a href="#" onclick="document.getElementById('form<?= htmlspecialchars($row['id']); ?>').submit(); return false;">
                    <div class="grid_1_of_4 images_1_of_4 fadeIn" style="margin-left: 20px;">
                        <img src="<?= $imagePath; ?>" alt="Product Image" />
                        <div class="priceandflavortext">
                            <p class="mc">
                                <?= $flavorShort ?>
                                <?php if ($showSeeMore): ?>
                                    <span data-bs-toggle="modal" data-bs-target="#flavorModal<?= htmlspecialchars($row["id"]) ?>" style="color: #16161a; font-size: 14px; cursor: pointer;">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </span>
                                <?php endif; ?>
                            </p>
                            <p class="price">₱<?= htmlspecialchars($row['price']); ?></p>
                        </div>
                    </div>
                </a>

                <!-- Hidden form for automatic submission -->
                <form id="form<?= htmlspecialchars($row['id']); ?>" method="post" action="penpos.php" class="d-none">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <input type="hidden" name="flavor" value="<?php echo htmlspecialchars($row['flavor']); ?>">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($row['category']); ?>">
                    <input type="hidden" name="cost" value="<?php echo htmlspecialchars($row['cost']); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($row['price']); ?>">
                    <input type="number" min="1" value="1" name="qty" required>
                    <input type="hidden" name="addcartnaten" value="1"> <!-- Add this to trigger the PHP logic -->
                    <input type="hidden" name="currentPage" value="<?= $currentPage; ?>">
                </form>

                <!-- Modal for full flavor text -->
                <div class="modal fade" id="flavorModal<?= htmlspecialchars($row["id"]) ?>" tabindex="-1" aria-labelledby="flavorModalLabel<?= htmlspecialchars($row["id"]) ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <label>Name Of Item :</label>
                                <?= $flavorCapitalized; // Show the full flavor with capitalized first letter ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
        echo "<p>No products found.</p>";
    }
    ?>
</div>

<div class="pagination float-right" style="margin-right: 35px;">
    <ul class="pagination">
        <!-- Previous Button -->
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage - 1; ?>&category=<?= $categoryId; ?>"><i class="fa-solid fa-arrow-left"></i></a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-arrow-left"></i></span></li>
        <?php endif; ?>

        <!-- Show First Page -->
        <li class="page-item <?= $currentPage === 1 ? 'active' : ''; ?>">
            <a class="page-link" href="?page=1&category=<?= $categoryId; ?>">1</a>
        </li>

        <?php if ($totalPages > 10 && $currentPage > 6): ?>
            <!-- Show ellipsis if more pages exist before current page -->
            <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>

        <!-- Show pages around the current page -->
        <?php 
        $startPage = max(2, $currentPage - 2);  // Show at least two pages before the current page
        $endPage = min($totalPages - 1, $currentPage + 2); // Show at most two pages after the current page

        for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?= $i === $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>&category=<?= $categoryId; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Show ellipsis if more pages exist after the current page -->
        <?php if ($totalPages > 10 && $currentPage < $totalPages - 3): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>

        <!-- Show Last Page -->
        <?php if ($totalPages > 1): ?>
            <li class="page-item <?= $currentPage === $totalPages ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $totalPages; ?>&category=<?= $categoryId; ?>"><?= $totalPages; ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage + 1; ?>&category=<?= $categoryId; ?>"><i class="fa-solid fa-arrow-right"></i></a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-arrow-right"></i></span></li>
        <?php endif; ?>
    </ul>
</div>



</div>

<div class="right" style="transform:translateX(-10px)">
<div class="right-attribute" style="transform: translateY(9px);">
<div class="colorofcart"style=" height: 410px; box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; border-radius: 10px; background :white;">


        <?php 
$branchid = $_SESSION['branch'];
$staff_id = $_SESSION['user_id'];

$sql = "SELECT 
    cart.id,
    flavor.flavor,
    cart.qty,
    cart.total,
    cart.price
    FROM cart
    INNER JOIN flavor ON cart.itemid = flavor.id
    INNER JOIN category ON flavor.rs_id = category.id
    INNER JOIN users ON cart.userid = users.id 
    WHERE cart.userid = $staff_id 
    AND cart.b_id = $branchid 
    AND cart.tiny = 1";

$result = mysqli_query(mysql: $conn, query: $sql);
?>

<?php if ($result): ?>
    <div id="" class="" style="max-height: 100%; width: 100%; overflow: auto;border-radius: 15px; background :white; ">
        <table class="table text-center" style="border-radius: 15px; border-collapse: collapse; width: 100%;padding: 0 !important;  background :white;" >
        <thead class="sticky-top" style="border-top-left-radius: 15px !important; border-top-right-radius: 15px !important; background: #161D6F ;padding : 0 !important;" >
        <tr>
                    <th scope="col" style=" color: #fff !important;">Name</th>
                    <th scope="col" style=" color: #fff !important;">Price</th>
                    <th scope="col" style=" color: #fff !important;">Action</th>
                </tr>
            </thead>
            <tbody  style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="tr">
                        <td>
                            <?php
                            $flavor = htmlspecialchars($row["flavor"]);
                            $flavorShort = substr($flavor, 0, 5); // Get the first 5 letters
                            $showSeeMore = strlen($flavor) > 5; // Check if flavor exceeds 5 letters
                            ?>
                            <?= $flavorShort ?>
                            <?php if ($showSeeMore): ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#flavorModal<?= htmlspecialchars(string: $row["id"]) ?>" style="color: blue;font-size: 12px"><i class="fa-solid fa-ellipsis"></i></a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row["price"]) ?>
                        </td>
                        <td style="align-items: center;">
                            <div class="tabledata" style="display: flex; flex-direction: row; align-items: center;">
                                <div class="td1" style="flex-basis: 90%; display: flex; align-items: center;">
                                    <button class="btn btn-outline-secondary minus" data-itemid="<?= $row['id'] ?>" 
                                        style="width: 20px; height: 20px; padding: 0; font-size: 12px;font-weight: 400;">
                                      <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <input type="number" class="qty text-center" value="<?= htmlspecialchars($row["qty"]) ?>" 
                                        data-itemid="<?= $row['id'] ?>" min="1" 
                                        style="width: 35px; height: 22px; font-size: 12px; font-weight: bold; pointer-events: none; 
                                        text-align: center; line-height: 25px; padding: 0; border-radius: 5px" readonly />
                                    <button class="btn btn-outline-secondary plus" data-itemid="<?= $row['id'] ?>" 
                                        style="width: 20px; height: 20px; padding: 0; font-size: 12px;font-weight: 400; ">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                                <div class="td1" style="flex-basis: 10%; display: flex; justify-content: center; align-items: center;margin-left: 7px">
                                    <a href="#" class="delete-item" data-itemid="<?= $row['id'] ?>" style="color: red; font-size: 12px; transform:translateY(-10px);" title="Delete Item">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
   
</div>


    <!-- Modal for showing full flavor -->
    
    
<?php endif; ?>

        
    </div>
    


        <div class="card colorofcart"
            style="height: 365px;border-radius: 10px;margin-top: 10px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;background :white;">
            <div class="card-body" style="margin: 0 10px">
                <div class="head">
                    <div class="row d-flex align-items-center justify-content-between" id="subtotalContainer"  style="transform: translateX(5px);">               
    <h6 class="colorofsubtotal" style="margin-left: 10px;">SUBTOTAL</h6>
    <h5 hidden id="subtotalDisplay" name="subtotal"></h5>
    <input type="number" class="form-control colorofsubtotal" id="subtotalInput" name="subtotalInput"
        style="width: 170px; background-color: transparent; border: none; outline: none; text-align: right;transform:translateY(-5px); font-weight: 500" readonly>
<hr class="line">
</div>
                </div>
                <div class="foot" >
                <p style="color:black; font-weight: 500;">Cashier Money</p>
                <input type="text" 
       value="<?php echo htmlspecialchars($sodValue); ?>" 
       id="cashiermoneydisplay"
       style="border-radius: 0; padding: 10px; background-color: transparent; color: #003366; border: none; border-bottom: 2px solid black; font-weight: 500;" 
       readonly>

                <p style="color:black;  font-weight: 500"style="margin: 10px 0 10px 0 !important;">Manage</p>
                <div class="buttonsave d-flex flex-row" style="max-width: 100%; justify-content: space-between;">
                <button class="btn " style="flex: 1; margin-right: 10px; padding: 8px 0;background-color:#161D6F" title="Insert Cashier Money"
        data-toggle="modal" data-target="#cashierModal"
        <?php if ($sod_value > 0 || $status == 1) echo 'disabled'; ?>> <!-- Disable if sod is greater than 0 -->
    <img src="../images/image.png" class="sodimg">
</button>

<!-- Button for Printing Z-reading -->
<button class="btn " style="flex: 1; padding: 8px 0;background-color:#161D6F" 
        title="Printing Z-reading"
        data-toggle="modal" data-target="#zReadingModal"
        <?php if ($nsod_value == $sod_value || $status == 1) echo 'disabled'; ?>> <!-- Disable if nsod equals sod -->
    <img src="../images/bills_3153501.png" class="z-reading">
</button>
</div>
<p  style="margin: 14px 0 10px 0 !important;color:black ; font-weight: 500">Payment</p>
<?php
// Assuming database connection ($conn) is already established
$tiny = 1;
$sqlss = "SELECT COUNT(*) AS item_count FROM cart WHERE userid = ? AND tiny = ? AND b_id = ?";
$stmtss = $conn->prepare($sqlss);
$stmtss->bind_param("iii", $userid, $tiny, $branchid);
$stmtss->execute();
$result = $stmtss->get_result();
$rows = $result->fetch_assoc();
$item_counts = $rows['item_count'];

// Determine if the button should be disabled
$button_disabled = ($item_counts == 0) ? 'disabled' : '';  // Disable if no items in cart

// Close statement and connection if needed
$stmtss->close();
?>

<!-- Now, render the button with the evaluated condition -->
<button type="button" class="btn" 
        style="width: 100%; text-align: center; letter-spacing: 2px; border-radius: 40px" 
        data-bs-toggle="modal" 
        data-bs-target="#checkoutModal"
        id="buttonofplaceorder"
        <?php echo $button_disabled; ?>> <!-- Output 'disabled' if no items -->
    Place Order
</button>
</div>
        


        </div>


        </div>
    </div>
                                    </div>                      
    </div>
    <div class="div">
        <div class="wor1">
        <div class="profile"><script src="https://cdn.lordicon.com/lordicon.js"></script>
<lord-icon
    src="https://cdn.lordicon.com/xcxzayqr.json"
    trigger="hover"
    style="width:65px;height:60px">
</lord-icon>
</div>
            <p class="usernamestyle"><?php  echo $user_name ?></p>
        </div>
        <div class="wor2">
        <div class="store"><img src="../images/store.png" alt=""></div>
            <p class="usernamestyle"><?php  echo $branchname ?></p>
        </div>
        <div class="wor3">

        <div class="store"><img src="../images/schedule.png" alt=""></div>
     <div class="timeinph">
     <p class="usernamestyles"><?php echo date('Y-m-d'); ?></p>
       

     <p id="time"></p>

     </div>

        </div>
      
      
    </div>
    <div class="modal fade" id="cashierModal" tabindex="-1" role="dialog" aria-labelledby="cashierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:600px; height: 400px;">
    <div class="modal-content" style="height: 40%;">
      <div class="modal-body">
        <div class="row">
          <!-- Left side: Image -->
          <div class="col-md-6 d-flex align-items-center justify-content-center">
            <img src="../images/cashier_bg.png" width="220" height="250" alt="Cashier Background">
          </div>
          
          <!-- Right side: Form -->
          <div class="col-md-6">
            
            <img src="../images/cash-machine.png"   width="70" height="70" style="transform:translateX(80px);margin-top:30px">
            <form method="post">
            <div class="cashier-input-group mt-4">
  <input type="number" id="cashierAmount" name="startofday" placeholder=" " 
    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1); updateValue(this.value);" 
    required>
  <label for="cashierAmount">Insert Cashier Money:</label>
  <span class="highlight"></span>
  <span class="bar"></span>
</div>

<div class="modal-footer d-flex justify-content-between flex-row" style="transform:translateX(-10px);padding:0">
  <button type="button" class="btn btn-secondary btn-custom" id="buttoncashier" data-dismiss="modal" style="padding: 10px 40px;">Close</button>
  <button type="submit" class="btn btn-primary btn-custom" name="sod" style="padding: 10px 34px;">Submit</button>
</div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<!-- Z-Reading Modal -->
<div class="modal fade" id="zReadingModal" tabindex="-1" role="dialog" aria-labelledby="zReadingModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document" style="max-width:600px; height: 400px;">
    <div class="modal-content" style="height: 40%;">
            <div class="modal-body">
            <div class="row">

            <div class="col-md-6 d-flex align-items-center justify-content-center">
            <img src="../images/4300524.jpg" width="220" height="250" alt="Cashier Background">
          </div>

          <div class="col-md-6">
          <img src="../images/cash-machine.png"   width="70" height="70" style="transform:translateX(80px);margin-top:30px">

                <form method="post">
                <div class="cashier-input-group mt-4">
  <input type="number" id="zReadingInfo" name="endofday" placeholder=" " 
    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1); updateValue(this.value);" 
    required>
  <label for="cashierAmount">Enter Total Money In Cashier</label>
  <span class="highlight"></span>
  <span class="bar"></span>
</div>
                    <div class="modal-footer d-flex justify-content-between flex-row" style="transform:translateX(-10px);padding:0">

                   
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"style="padding: 10px 34px;">Close</button>
                        <button type="submit" class="btn btn-primary" id="buttoncashiers" name="eod" style="padding: 10px 40px;">Submit</button>
                      
                    </div>
                </form>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>         
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" >                      
      <div class="modal-body">
   <div class="logovape">
   <h3 style="margin-left: 20px;transform:translateY(40px)">Check Out</h3>
   <img src="../images/vapelogo.png" height="100" width="100" >
   </div>                            
        <form method="post" id="checkoutForm" style="margin-top: 40px;">
          <!-- Subtotal Field -->
          <div class="mb-3 d-flex justify-content-between align-items-center">
            <label for="subtotalInput" class="form-label" style="color: #2c3e50;font-size: 1.2rem;transform:translateY(5px)">Subtotal </label>
            <input type="number" id="subtotalInput" name="subtotalInput" 
              class="form-control-plaintext" 
              style="font-weight: bold; color: #2c3e50; text-align: right; font-size: 1.2rem; border: none;" readonly>
          </div>
          <hr style="border: 1px dotted black;transform:translateY(-10px)">
          <div class="mb-3 d-flex justify-content-between align-items-center">
            <label for="cashpaymentinput" class="form-label" style="color: #2c3e50;font-size: 1.2rem">Payment :</label>
            <input type="number" id="cashpaymentinput" name="cashpaymentinput" class="form-control"
    style="border: none; border-bottom: 1px dotted black; background-color: transparent; padding: 10px; outline: none; 
           border-radius: 0; width: 240px; transform: translateY(-10px);"
    placeholder="Enter Here"
    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1); updateValue(this.value);"
    required>


          </div>
          <div class="mb-3  d-flex justify-content-between align-items-center">
            <label style="color: #2c3e50;font-size: 1.2rem;">Change: 
              
            </label>
            <span id="new-value" style="font-weight: bold;font-size: 1.2rem; text-align: right;margin-right:15px">0.00</span>
          </div>
          <hr style="border: 1px dotted black;transform:translateY(-10px)">
          <div class="d-flex justify-content-between align-items-center" style="transform: translateY(50px);">
  <img src="../images/Picsart_24-10-07_16-25-55-381.png" width="100" height="100" style="transform: translateY(-30px);">
  <button type="submit" name="cashpayment" id="buttoncashierss" class="btn " 
    style=" border: 1px solid black; border-radius: 8px; padding: 10px !important;"
    <?php echo $button_disabled; ?>>
    Check Out
  </button>
</div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php 
mysqli_data_seek($result, 0); 
while ($row = mysqli_fetch_assoc($result)): 
    // Check if "flavor" key exists; if not, set a default value
    $flavor = isset($row["flavor"]) ? htmlspecialchars($row["flavor"]) : '';
?>
    <div class="modal fade" id="flavorModal<?= htmlspecialchars($row["id"]) ?>" tabindex="-1" aria-labelledby="flavorModalLabel<?= htmlspecialchars($row["id"]) ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <?= $flavor; // Show the full flavor ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>
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
    <script>
    // Save scroll position before the user navigates away
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function () {
            localStorage.setItem('scrollPos', document.querySelector('.container').scrollTop);
        });
    });

    // Restore scroll position when the page loads
    window.addEventListener('load', function () {
        const scrollPos = localStorage.getItem('scrollPos');
        if (scrollPos) {
            document.querySelector('.container').scrollTop = scrollPos;
        }
    });
</script>
    <script>
    // This code is optional; modern browsers support position: sticky.
    const header = document.querySelector('.table thead');
    const sticky = header.offsetTop;

    window.onscroll = function() {
        if (window.pageYOffset > sticky) {
            header.classList.add('sticky');
        } else {
            header.classList.remove('sticky');
        }
    };
</script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("#new-value").forEach(function(element) {
            element.innerText = "0.00";
        });
    });

    function updateValue(payment) {
        // Retrieve and parse the subtotal as a float value
        var subtotalValue = parseFloat(document.querySelector("#subtotalInput").value);

        // Ensure payment is a valid number
        var paymentValue = parseFloat(payment);

        // Check if subtotal and payment are valid numbers before calculation
        if (isNaN(subtotalValue) || isNaN(paymentValue)) {
            document.querySelectorAll("#new-value").forEach(function(element) {
                element.innerText = "0.00";
            });
            return;
        }

        // Calculate the change
        var newValue = paymentValue - subtotalValue;

        // Update all instances of "new-value"
        document.querySelectorAll("#new-value").forEach(function(element) {
            element.innerText = newValue.toFixed(2); // Display the result with 2 decimal places
        });
    }

    function fetchSubtotal() {
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            if (this.status === 200) {
                // Update both subtotal displays and inputs with the fetched value
                document.querySelectorAll("#subtotalDisplay").forEach(function(display) {
                    display.textContent = this.responseText;
                }, this);

                document.querySelectorAll("#subtotalInput").forEach(function(input) {
                    input.value = this.responseText;
                }, this);
            } else {
                console.error('Error fetching subtotal:', this.statusText);
                document.querySelectorAll("#subtotalDisplay").forEach(function(display) {
                    display.textContent = "Error fetching subtotal";
                });

                document.querySelectorAll("#subtotalInput").forEach(function(input) {
                    input.value = "";
                });
            }
        };

        xhttp.onerror = function() {
            console.error('Request failed');
            document.querySelectorAll("#subtotalDisplay").forEach(function(display) {
                display.textContent = "Request failed";
            });

            document.querySelectorAll("#subtotalInput").forEach(function(input) {
                input.value = "";
            });
        };

        xhttp.open("GET", "livesubtotal.php");
        xhttp.send();
    }

    // Call fetchSubtotal regularly and initially
    setInterval(fetchSubtotal, 100);
    fetchSubtotal();
</script>

    <script>
    $(document).ready(function() {
        $('#openModal').click(function() {
            $('#myModal').addClass('modal-blur').modal('show');
        });
        $('#myModal').on('hidden.bs.modal', function(e) {
            $('#myModal').removeClass('modal-blur');
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        $('.buy-now-btn').click(function() {
            var modalId = $(this).data('target');
            $(modalId).addClass('modal-blur').modal('show');
        });
        $('.modal').on('hidden.bs.modal', function(e) {
            $(this).removeClass('modal-blur'); //
        });
    });
    </script>


    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script>
    <?php
        if (isset($_SESSION['possucc'])) { ?>
    alertify.set('notifier', 'position', 'bottom-left');
    alertify.success('<?php echo $_SESSION['possucc']; ?>');
    <?php
            unset($_SESSION['possucc']);
        } ?>
    </script>
    <script>
    <?php
        if (isset($_SESSION['poserror'])) { ?>
    alertify.set('notifier', 'position', 'bottom-left');
    alertify.error('<?php echo $_SESSION['poserror']; ?>');
    <?php
            unset($_SESSION['poserror']);
        } ?>
    </script>
  <script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['poserrors'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo $_SESSION['poserrors']; ?>');
            
            // Show the modal
            $('#cashierModal').modal('show');

            <?php unset($_SESSION['poserrors']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['poserrorsss'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo $_SESSION['poserrorsss']; ?>');
            
            // Show the modal
            $('#checkoutModal').modal('show');

            <?php unset($_SESSION['poserrorsss']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>


<script>
    function searchProducts() {
        const input = document.getElementById('searchInput').value;
        const currentUrl = new URL(window.location.href);
        
        // Update the URL with the search query
        currentUrl.searchParams.set('search', input);
        
        // Redirect to the updated URL
        window.location.href = currentUrl.toString();
    }
</script>
 <!-- <script>
    function searchProducts() {

        var input = document.getElementById('searchInput').value.toLowerCase();

        var products = document.querySelectorAll('.grid_1_of_4');


        products.forEach(function(product) {

            var productName = product.querySelector('p').textContent.toLowerCase();


            if (productName.includes(input)) {

                var highlightedName = productName.replace(new RegExp(input, 'gi'), function(match) {
                    return '<span class="highlight">' + match + '</span>';
                });

                product.style.display = 'block';
                product.querySelector('p').innerHTML = highlightedName;
            } else {

                product.style.display = 'none';
            }
        });
    }
    </script
   > -->
<script>
function updateCategory() {
    // When the category is changed, submit the form automatically
    document.forms[0].submit();
}
</script>

<script>
    $('#productModal<?php echo $row['id']; ?>').modal({
    backdrop: 'static', // Static backdrop does not allow closing by clicking outside
    keyboard: false // Disable closing by pressing the escape key
});
document.addEventListener("DOMContentLoaded", function() {
    const element = document.getElementById('yourElementId');
    if (element) {
        element.addEventListener('click', function() {
            // Your event handler code
        });
    }
});

</script>

<script>
$(document).ready(function() {
    $('.plus, .minus').on('click', function() {
        var itemId = $(this).data('itemid');
        var input = $('input.qty[data-itemid="'+itemId+'"]');
        var oldValue = parseFloat(input.val());
        var newVal;

        if ($(this).hasClass('plus')) {
            newVal = oldValue + 1; // Increase quantity
        } else {
            newVal = oldValue - 1; // Decrease quantity
            // Ensure it doesn't go below 1
            if (newVal < 1) {
                newVal = 1; // Keep it at 1 if it would go below
            }
        }

        input.val(newVal); // Set the new value

        // Disable the minus button if quantity is 1
        $('.minus[data-itemid="'+itemId+'"]').prop('disabled', newVal === 1);

        // Send updated quantity to the server using AJAX
        $.post('<?php echo $_SERVER["PHP_SELF"]; ?>', { itemid: itemId, qty: newVal }, function(response) {
            console.log('Response for itemId ' + itemId + ': ', response); // Log the response to the console

            // Parse the response
            var data = JSON.parse(response);

            // Update the total and quantity displayed in the table
            if (data.qty && data.total) {
                $('input.qty[data-itemid="'+itemId+'"]').val(data.qty);
                $('td.total[data-itemid="'+itemId+'"]').text(data.total);

                // Disable the plus button if the quantity reaches the available stock
                $('.plus[data-itemid="'+itemId+'"]').prop('disabled', data.qty >= data.availableQty);
            } else if (data.error) {
                alert(data.error); // Alert error if returned
                // Reset to the last valid quantity (optional, depending on UX)
                input.val(oldValue);
            } else {
                console.error('Error updating item:', data.error);
            }
        });
    });

    // Prevent manual input of less than 1
    $('input.qty').on('input', function() {
        var itemId = $(this).data('itemid');
        var inputValue = parseInt($(this).val());
        if (inputValue < 1) {
            $(this).val(1); // Set to 1 if the input value is less than 1
            $('.minus[data-itemid="'+itemId+'"]').prop('disabled', true);
        } else {
            $('.minus[data-itemid="'+itemId+'"]').prop('disabled', false);
        }
    });
});

</script>

<script>
        function updateTime() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'time.php', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('time').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        setInterval(updateTime, 1000); // Update the time every second
        window.onload = updateTime; // Get the time when the page loads
    </script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add event listener for all delete-item buttons
        document.querySelectorAll('.delete-item').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const itemid = this.getAttribute('data-itemid');

                // Automatically send AJAX request to delete item
                fetch('delete_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'itemid=' + itemid
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        // Reload the page or update the UI to reflect the deletion
                        location.reload();
                    } else {
                        alert('Failed to delete item');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>

<script>
    document.getElementById('confirmLogout').onclick = function() {
        // Redirect to logout page on confirmation
        window.location.href = "../logout.php";
    };
</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const zReadingInput = document.getElementById("zReadingInfo");

    zReadingInput.addEventListener("input", function() {
      const value = parseFloat(zReadingInput.value);
      if (value <= 0) {
        zReadingInput.setCustomValidity("Please enter a positive amount greater than zero.");
      } else {
        zReadingInput.setCustomValidity(""); // Reset validity
      }
    });
  });
</script>
<script>
  function updateValue(paymentValue) {
    const subtotalInput = parseFloat(document.getElementById("subtotalInput").value) || 0;
    const paymentAmount = parseFloat(paymentValue) || 0;
    
    const change = paymentAmount - subtotalInput;
    const changeDisplay = document.getElementById("new-value");
    
    if (paymentAmount < subtotalInput) {
      changeDisplay.textContent = "0";
    } else {
      changeDisplay.textContent = change.toFixed(2);
    }
  }
</script>

</body>

</html>
