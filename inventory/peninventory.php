<?php
session_start();
include "../database/db_conn.php";
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['role']) || !isset($_SESSION['branch'])) {
    header("location: ../all_login.php");
    exit; // It's a good practice to exit after sending a header redirect
}
if (isset($_GET['id']) && isset($_GET['branch'])) {
    $dashboardid = intval($_GET['id']);
    $_SESSION['branch_id'] = $dashboardid;
}
$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$branch = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : null;


$sql = "SELECT SUM(quantity * price) AS total_price FROM flavor WHERE date > NOW() AND f_b_id = $branchid";
$result = $conn->query($sql);

// Display the total
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalPrice = isset($row['total_price']) && $row['total_price'] !== null ? $row['total_price'] : 0;
    $totalPrice = number_format($totalPrice, 2, '.', ',');
} else {
    $totalPrice = number_format(0, 2, '.', ',');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap4.5.2.css">
    <?php require 'add_beverage.php'; ?>
    <title>Cloud Keepers s</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Paaji+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <style>
        body{
            overflow-y: hidden;
            padding: 20px 20px 0 90px !important;
        }
        td {
            color: #0B192C !important;
            font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 500;
  font-style: normal;
            letter-spacing: 1px;
            font-variation-settings:        
                "wdth" 100;
            font-size: 14px;


        }



        th {
            font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
            letter-spacing: 1px;
            font-variation-settings:
                "wdth" 100;
            font-size: 12px;
            background-color: #22092C !important;
            color: #F4F6FF !important;            
        }
        label{
            font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
            letter-spacing: 1px;
            font-variation-settings:
                "wdth" 100;
            font-size: 16px;
          
            color: #0B192C !important;    
        }

        .topbutton {
            background-color: #0B192C !important;
            color: #F4F6FF !important;
            font-family: "Inconsolata", monospace;
            font-optical-sizing: auto;
            font-weight: 700;
            font-style: normal;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-variation-settings:
                "wdth" 100;
        }

        .topbutton:hover {
            background-color: #0D92F4 !important;
            color: black !important;
        }

        .title-branch {
            font-family: "Inconsolata", monospace;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            letter-spacing: 3px;
            font-variation-settings:
                "wdth" 100;
        }
        thead,th{
            border: none !important;
        }
        .sticky-header th {
    position: sticky;
    top: 0;
    background-color: #003366; /* Same background color as the header */
    color: #fff; /* Same text color as the header */
    z-index: 1; /* Ensures it stays above other content */
}
a{
    text-decoration: none !important;
}

    </style>
  <style>
  .modal-content{
    border-radius: 30px;
    padding: 10px;
 }

  .modal-body, .modal-content {
    border-radius: 40px; /* Apply border radius */
    background-color: #F4F6FF; /* Set background color */
   

  }

  #addItemModal > .modal-body {
    padding: 20px; /* Add padding */
    display: flex;
    flex-direction: column; /* Align content vertically */
    align-items: stretch; /* Make items stretch to full width */
  }

  #addItemModal > .form-group {
    margin-bottom: 20px; /* Add margin for spacing */
  }

  #addItemModal > .form-control {
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
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    
  }

  #addItemModal >  .modal-footer {
    border-top: none; /* Remove footer border */
    justify-content: flex-end; /* Align buttons to the right */
    padding: 10px; /* Add padding */
  }

  #addItemModal > .btn {
    transition: background-color 0.3s, transform 0.2s; /* Smooth transition */
  }

  #addItemModal > .btn-primary {
    background-color: #007bff; /* Bootstrap primary color */
  }

  #addItemModal > .btn-primary:hover {
    background-color: #0056b3; /* Darker on hover */
    transform: translateY(-2px); /* Lift effect */
  }

  #addItemModal > .btn-secondary {
    margin-left: 10px; /* Spacing between buttons */
  }
  input{
    box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;  }

    .naVE > button{
        box-shadow: rgba(0, 0, 0, 0.07) 0px 1px 2px, rgba(0, 0, 0, 0.07) 0px 2px 4px, rgba(0, 0, 0, 0.07) 0px 4px 8px, rgba(0, 0, 0, 0.07) 0px 8px 16px, rgba(0, 0, 0, 0.07) 0px 16px 32px, rgba(0, 0, 0, 0.07) 0px 32px 64px;
    }
    #search{
        box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px;
                 }

           

  .btn:hover ion-icon {
    color: #F6B17A !important; 
  }
  .deletecolor:hover  i {
    color: red !important; 
  }
  .addbutton{
    background-color: #0B192C  !important;
    color: white !important;
    padding: 8px 10px !important;
    font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
  }

  #toggleCheckboxes{
    font-size: 20px;
    padding:2px 10px;
    border-radius: 10px;
    color: white;
    background-color: #0B192C  !important;
    margin-right: 5px;
    
  }
  #toggleCheckboxes:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    
  }
  .addbutton:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    padding: 8px 10px !important;
  }
  .container::-webkit-scrollbar {
    display: none;
}


/* Hide scrollbar for Firefox */
.container {
    scrollbar-width: none; /* For Firefox */
}
#tablenaten::-webkit-scrollbar {
    display: none;
}
.container {
    scrollbar-width: none; /* For Firefox */
}
#tablesss::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for Firefox */
#tablesss {
    scrollbar-width: none; /* For Firefox */
}
.tablenangcategory{
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    }

    .table-responsive {
    max-height: 100%; /* Ensure the table height doesn't exceed the modal body */
    height: 400px; /* Set the fixed height */
    overflow-y: auto; /* Enable vertical scrolling */
}

.table-responsive table {
    width: 100%; /* Ensure the table occupies the full container width */
}

.table thead {
    position: sticky; /* Make the header sticky for better UX */
    top: 0;
    z-index: 1;
}


#searchInpute{
    max-width: 100%;
    width: 900px;
}
#searchInputeaaa{
    border-radius: 50px;
}

#dateInput{
    border-radius: 10px;
    max-width: 100%;
    width: 200px;
    height: 43px;
   }
   #totalDisplay {
    max-width: 100%;
    width: 300px;
    height: 38px;
    border: 1px solid #003366;
    border-radius: 10px;
    text-align: center; /* Horizontal center */
    line-height: 43px; /* Match the height for vertical center */
    margin-right: 10px;
}
#totalDisplays {
    max-width: 100%;
    width: 300px;
    height: 38px;
    border: 1px solid #003366;
    border-radius: 10px;
    text-align: center; /* Horizontal center */
    line-height: 43px; /* Match the height for vertical center */
    margin-right: 10px;
}
</style>

    <link rel="stylesheet" type="text/css" href="testing.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
    <link rel="stylesheet" href="../admin/side_bar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    

<header class="header">
            <div class="header__container">
              

                <p href="#" class="header__logo" style="margin-top: 12px">INVENTORY</p>

    
                <div class="header__toggle">
                    <i class='bx bx-menu' id="header-toggle"></i>
                </div>
            </div>
        </header>
 <!--========== NAV ==========-->
 <div class="nav" id="navbar">
            <nav class="nav__container">
                <div>
                    <a href="#" class="nav__link nav__logo">
                      
                        <span class="nav__logo-name" style="font-size: 15px">Dashboard</span>
                    </a>
    
                    <div class="nav__list">
                        <div class="nav__items">
                            <h3 class="nav__subtitle" style="margin-bottom: 10px;"></h3>
    
                            <a href="../admin/pendashboard.php" class="nav__link active">
                            <i class='bx bxs-dashboard nav__icon' style='color:#ffffff'  ></i>
                            <span class="nav__name">Dashboard</span>             
                            </a>               
                            <a href="#" class="nav__link active">
                            <i class='bx bxs-package nav__icon'></i>
                            <span class="nav__name">Inventory</span>             
                            </a> 
                            <a href="../acc/accountdashboard.php" class="nav__link active">   
                            <i class='bx bx-user nav__icon'></i>
                            <span class="nav__name">Users</span>             
                            </a> 
                            <a href="../pen_void/adminvoid.php" class="nav__link active">   
                            <i class='bx bx-rotate-left nav__icon' style='color:#ffffff'  ></i>        
                                             
                            <span class="nav__name">Void Items</span>             
                            </a> 
                        <div class="nav__items">
                        
    
                            <div class="nav__dropdown">
                                <a href="#" class="nav__link">
                                <i class='bx bx-history nav__icon'></i>
                                    <span class="nav__name">History</span>
                                    <i class='bx bx-chevron-down nav__icon nav__dropdown-icon'></i>
                                </a>
                
                                <div class="nav__dropdown-collapse">
                                    <div class="nav__dropdown-content">
                                        <a href="../back_track_add_item/added_track.php" class="nav__dropdown-item">Added Stock</a>
                                        <a href="../transfer_items/transfer_item.php" class="nav__dropdown-item">transfer Item</a>
                                        <a href="../expired_items/expired_items.php" class="nav__dropdown-item">Expired Items</a>
                                        <a href="../return_items/return_item.php" class="nav__dropdown-item">Return Item</a>
                                        <a href="../pen_transaction/transaction.php" class="nav__dropdown-item">Transaction</a>
                                        <a href="../dispose/dispose.php" class="nav__dropdown-item">Dispose</a>
                                    </div>
                                </div>
    
                            </div>
                       

                             
                        </div>
                        
                            <a href="../daily_tally/daily_tally.php" class="nav__link">
                            <svg width="21px" height="21px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M6 20V4M10 20V4M14 20V4M18 20V4M21 5L3 19" stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                <span class="nav__name " style="margin-left: 18px;">Tally</span>
                            </a>
                            <a href="../compare_sales/compare_sales.php" class="nav__link">
                            <i class="fa-solid fa-code-compare" style="color: white;"></i>            
                                                <span class="nav__name" style="margin-left: 18px;">Compare Sales</span>
                            </a>
                            <a href="../admin/admindashboard.php" class="nav__link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: scaleX(-1)progid:DXImageTransform.Microsoft.BasicImage(rotation=0, mirror=1);"><path d="M19.148 2.971A2.008 2.008 0 0 0 17.434 2H6.566c-.698 0-1.355.372-1.714.971L2.143 7.485A.995.995 0 0 0 2 8a3.97 3.97 0 0 0 1 2.618V19c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-8.382A3.97 3.97 0 0 0 22 8a.995.995 0 0 0-.143-.515l-2.709-4.514zm.836 5.28A2.003 2.003 0 0 1 18 10c-1.103 0-2-.897-2-2 0-.068-.025-.128-.039-.192l.02-.004L15.22 4h2.214l2.55 4.251zM10.819 4h2.361l.813 4.065C13.958 9.137 13.08 10 12 10s-1.958-.863-1.993-1.935L10.819 4zM6.566 4H8.78l-.76 3.804.02.004C8.025 7.872 8 7.932 8 8c0 1.103-.897 2-2 2a2.003 2.003 0 0 1-1.984-1.749L6.566 4zM10 19v-3h4v3h-4zm6 0v-3c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v3H5v-7.142c.321.083.652.142 1 .142a3.99 3.99 0 0 0 3-1.357c.733.832 1.807 1.357 3 1.357s2.267-.525 3-1.357A3.99 3.99 0 0 0 18 12c.348 0 .679-.059 1-.142V19h-3z"></path></svg>
                                <span class="nav__name" style="margin-left: 18px;">Branches</span>
                            </a>
                               <a href="../backup/backup.php" class="nav__link">
                               <svg width="21" height="21" viewBox="0 0 169 198" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <mask id="path-1-inside-1_413_65" fill="white">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M130.318 46.364C130.768 44.4495 131 42.4915 131 40.5C131 18.1325 101.675 0 65.5 0C29.3253 0 0 18.1325 0 40.5C0 42.4915 0.232475 44.4495 0.68156 46.364C0.241518 47.4909 0 48.7173 0 50V151C0 151.065 0.000613776 151.129 0.00183582 151.194C0.000612655 151.296 0 151.398 0 151.5C0 173.868 29.3253 192 65.5 192C101.675 192 131 173.868 131 151.5C131 151.398 130.999 151.296 130.998 151.194C130.999 151.129 131 151.065 131 151V50C131 48.7173 130.758 47.4909 130.318 46.364Z"/>
                                </mask>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M130.318 46.364C130.768 44.4495 131 42.4915 131 40.5C131 18.1325 101.675 0 65.5 0C29.3253 0 0 18.1325 0 40.5C0 42.4915 0.232475 44.4495 0.68156 46.364C0.241518 47.4909 0 48.7173 0 50V151C0 151.065 0.000613776 151.129 0.00183582 151.194C0.000612655 151.296 0 151.398 0 151.5C0 173.868 29.3253 192 65.5 192C101.675 192 131 173.868 131 151.5C131 151.398 130.999 151.296 130.998 151.194C130.999 151.129 131 151.065 131 151V50C131 48.7173 130.758 47.4909 130.318 46.364Z" fill="#22092C"/>
                                <path d="M130.318 46.364L120.583 44.0804L119.873 47.1063L121.003 50.0014L130.318 46.364ZM0.68156 46.364L9.99659 50.0014L11.1271 47.1063L10.4173 44.0804L0.68156 46.364ZM0.00183582 151.194L10.0011 151.313L10.003 151.159L10 151.004L0.00183582 151.194ZM130.998 151.194L121 151.004L120.997 151.159L120.999 151.313L130.998 151.194ZM121 40.5C121 41.7076 120.86 42.8995 120.583 44.0804L140.054 48.6477C140.675 45.9994 141 43.2755 141 40.5H121ZM65.5 10C82.0327 10 96.5228 14.1636 106.556 20.3676C116.745 26.6675 121 34.0607 121 40.5H141C141 24.5718 130.592 11.7149 117.075 3.35676C103.402 -5.09739 85.142 -10 65.5 -10V10ZM10 40.5C10 34.0607 14.2548 26.6675 24.4436 20.3676C34.4772 14.1636 48.9673 10 65.5 10V-10C45.858 -10 27.5982 -5.09739 13.9254 3.35676C0.407891 11.7149 -10 24.5718 -10 40.5H10ZM10.4173 44.0804C10.1403 42.8995 10 41.7076 10 40.5H-10C-10 43.2754 -9.67538 45.9994 -9.0542 48.6477L10.4173 44.0804ZM-8.63347 42.7267C-9.51887 44.9942 -10 47.4528 -10 50H10C10 49.9933 10.0003 49.9895 10.0004 49.9882C10.0006 49.9869 10.0006 49.9872 10.0003 49.9887C9.99962 49.9922 9.99833 49.9969 9.99659 50.0014L-8.63347 42.7267ZM-10 50V151H10V50H-10ZM-10 151C-10 151.128 -9.99879 151.256 -9.99636 151.383L10 151.004C10 151.003 10 151.001 10 151H-10ZM-9.99745 151.074C-9.99915 151.216 -10 151.358 -10 151.5H10C10 151.438 10.0004 151.376 10.0011 151.313L-9.99745 151.074ZM-10 151.5C-10 167.428 0.40789 180.285 13.9254 188.643C27.5982 197.097 45.858 202 65.5 202V182C48.9673 182 34.4772 177.836 24.4436 171.632C14.2548 165.332 10 157.939 10 151.5H-10ZM65.5 202C85.142 202 103.402 197.097 117.075 188.643C130.592 180.285 141 167.428 141 151.5H121C121 157.939 116.745 165.332 106.556 171.632C96.5228 177.836 82.0327 182 65.5 182V202ZM141 151.5C141 151.358 140.999 151.216 140.997 151.074L120.999 151.313C121 151.375 121 151.438 121 151.5H141ZM140.996 151.383C140.999 151.255 141 151.128 141 151H121C121 151.002 121 151.003 121 151.004L140.996 151.383ZM141 151V50H121V151H141ZM141 50C141 47.4528 140.519 44.9941 139.633 42.7266L121.003 50.0014C121.002 49.9969 121 49.9923 121 49.9887C120.999 49.9872 120.999 49.9869 121 49.9882C121 49.9895 121 49.9933 121 50H141Z" fill="white" mask="url(#path-1-inside-1_413_65)"/>
                               <path d="M126 49.5C88.1189 79.2502 46.5 81.5 9 49.5M9 93.9113C55.0328 123.157 82.8443 121.645 126 93.9113M126 135.259C84.4295 161.275 46.8811 164.505 9 135.259" stroke="white" stroke-opacity="0.96" stroke-width="9"/>
                                <ellipse cx="115" cy="150.5" rx="47" ry="47.5" fill="white"/>
                                <path d="M145.624 144.038C150.066 159.427 140.493 176.327 123.308 181.288C106.124 186.248 89.0167 177.05 84.5744 161.66C80.132 146.271 89.7054 129.37 106.89 124.41C124.075 119.45 141.182 128.648 145.624 144.038Z" fill="white" stroke="black" stroke-width="12"/>
                                <path d="M104.227 113.547L123.02 118.995L110.435 133.947L104.227 113.547Z" fill="black" stroke="black" stroke-width="10"/>
                                <rect width="22.4937" height="27.1938" transform="matrix(0.841826 0.539749 -0.587722 0.809063 134.696 111.114)" fill="#FFFDFD"/>
                                </svg>
                                <span class="nav__name" style="margin-left: 18px;">Back-up Data</span>
                            </a>
                      
                    </div>
                </div>

                <a href="" class="nav__link nav__logout"  data-toggle="modal" data-target="#logoutModal">
                    <i class='bx bx-log-out nav__icon' ></i>
                    <span class="nav__name">Log Out</span>
                </a>
            </nav>
        </div>

    
  
     
        <div class="main">

            <div class="naVE" >
                <button type="button" class="btn mb-3 topbutton " data-bs-toggle="modal" data-bs-target="#addItemModal"
                    style="border: 1px solid #003366">Add
                    Item</button>



                <button type="button" class="btn  mb-3 topbutton" data-bs-toggle="modal" data-bs-target="#anotherModal"
                    style="border: 1px solid #003366">
                    Add Category

                </button>


                <button type="button" class="btn mb-3 topbutton" data-toggle="modal" data-target="#deleteTableModal"
                    style="border: 1px solid #003366">
                    Modify Category
                </button>
                <button type="button" class="btn mb-3 topbutton" data-toggle="modal" data-target="#exampleModal"
                    style="border: 1px solid #003366">
                    Modify stock range
                </button>
                <button type="button" class="btn mb-3 topbutton" data-bs-toggle="modal" data-bs-target="#transferModal"
                    style="border: 1px solid #003366">
                    Transfer Item
                </button>
            

          
                <button type="button" class="btn mb-3 topbutton float-right" data-bs-toggle="modal" data-bs-target="#questionModal"
                    style="border: 1px solid #003366">
                    <i class="fa-solid fa-question"></i>   

                </button>
            </div>
            <!-- Add a search bar -->
           

            <div class="modal fade" id="anotherModal" tabindex="-1" aria-labelledby="anotherModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" >
                        <div class="modal-body"  style="padding: 30px;">
                        <ion-icon name="close-circle-outline" data-bs-dismiss="modal" aria-label="Close" style="float:right;font-size: 20px;margin-bottom: 15px;"></ion-icon>
                            <form action="" method="POST" enctype="multipart/form-data">

                                <div class="mb-3">
                                    <label for="beverages" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="beverages" name="beverages" required>
                                    <small>Enter The Name Of The Category</small>
                                </div>
                              

                                <button type="submit" name="sad" class="btn btn-primary addbutton">Create</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">

      <div class="modal-body">
        <i class="fa-solid fa-question" style="font-size: 4rem; color: #0d6efd;"></i>
        <p class="mt-3">
          The colors applied to the data represent the urgency of the expiration dates:
          <ul class="text-start mt-3">
            <li>
              <span style="display: inline-block; height: 10px; width: 20px; background-color: #FFCCCC; border: 1px solid #000;"></span>
              <strong>Light Red Background (#FFCCCC)</strong>: Indicates that the item expires this year  and requires immediate attention.
            </li>
            <li>
              <span style="display: inline-block; height: 10px; width: 20px; background-color: #D0E4FF; border: 1px solid #000;"></span>
              <strong>Light Blue Background (#D0E4FF)</strong>: Shows the item will expire in 1 year  which is a safe time frame.
            </li>
            <li>
              <span style="display: inline-block; height: 10px; width: 20px; background-color: #FFD8B1; border: 1px solid #000;"></span>
              <strong>Light Orange Background (#FFD8B1)</strong>: Indicates moderate urgency, as the item will expire in 2 years 
            </li>
            <li>
              <span style="display: inline-block; height: 10px; width: 20px; background-color: #D0F5D1; border: 1px solid #000;"></span>
              <strong>Light Green Background (#D0F5D1)</strong>: Suggests that the item is safe, with more than 2 years left before expiration.
            </li>
          </ul>
        </p>
      </div>

      <div class="modal-footer" style="border: none;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




            <!-- Add Item Modal -->
            <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content">
            <div class="modal-body" id="modaladd">
            <ion-icon name="close-circle-outline" class="float-right " style="font-size: 20px;" data-bs-dismiss="modal" aria-label="Close"></ion-icon>
            <form action="" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <label for="option">Select Category</label>
            <select class="form-control" id="tableSelect" name="option">
                <?php
                $sql = "SELECT * FROM `category` WHERE c_b_id = $branchid";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Retain the selected option after form submission
                        $selected = isset($_GET['option']) && $_GET['option'] == $row["id"] ? "selected" : "";
                        ?>
                        <option value="<?php echo htmlspecialchars($row["id"]); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($row["category"]); ?>
                        </option>
                        <?php
                    }
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                ?>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="brand" class="form-label">Brand Name</label>
            <input type="text" class="form-control" id="brand" name="brand" value="<?php echo isset($_GET['brand']) ? htmlspecialchars($_GET['brand']) : ''; ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Flavor Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?php echo isset($_GET['description']) ? htmlspecialchars($_GET['description']) : ''; ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" 
       value="<?php echo isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity']) : ''; ?>" 
       oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);" 
       required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="cost" class="form-label">Cost</label>
            <input type="number" class="form-control" id="cost" name="cost" 
       value="<?php echo isset($_GET['cost']) ? htmlspecialchars($_GET['cost']) : ''; ?>" 
       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);" 
       required>

        </div>

        <div class="col-md-6 mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" class="form-control" id="price" name="price" 
       value="<?php echo isset($_GET['price']) ? htmlspecialchars($_GET['price']) : ''; ?>" 
       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);" 
       required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="image" class="form-label">Upload Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <div class="col-md-6 mb-3">
            <label for="manu" class="form-label">Manufactured Date</label>
            <input type="date" class="form-control" id="manu" name="manu" value="<?php echo isset($_GET['manu']) ? htmlspecialchars($_GET['manu']) : ''; ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="expi" class="form-label">Expiration Date</label>
            <input type="date" class="form-control" id="expi" name="expi" value="<?php echo isset($_GET['expi']) ? htmlspecialchars($_GET['expi']) : ''; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
        <div class="form-group">
            <label for="statusSelect">Select Status</label>
            <select class="form-control" id="statusSelect" name="status">
    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Active</option>
    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>On Hold</option>
</select>

        </div>
        </div>
  
        <div class="col-md-6 mb-3">
            <label for="lows" class="form-label">Alert Stock Range</label>
            <input type="number" class="form-control" id="lows" name="lows" 
       value="<?php echo isset($_GET['lows']) ? htmlspecialchars($_GET['lows']) : ''; ?>" 
       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);" 
       required>    
        </div>
       

    </div>
    <div style="margin-right:35px;float: right">
        <button type="submit" name="addflavor" class="btn btn-primary addbutton">Add Item</button>
        </div>
</form>


            </div>
        </div>
    </div>
</div>


           
    <div class="d-flex">
    <input type="text" id="search" class="form-control mb-3" placeholder="Search..."  onkeyup="filterTableSaa()" style="width:91.5%;margin-right: 20px">
    <div id="totalDisplay" class="text-center " style="font-weight: bold; " readonly></div>
                <div id="totalDisplays" style="font-weight: bold; "> <i class="fa-solid fa-peso-sign"></i><?php echo $totalPrice; ?></div>
    <button id="downloadBtn" class="btn mb-3 topbutton" onclick="downloadTable()" style="margin-right: 10px"><i class="fa-solid fa-download"></i></button>
    <button id="printBtn" class="btn mb-3 topbutton" onclick="printTable()"><i class="fa-solid fa-print"></i></button>
    </div>

    <div id="tablenaten" class="table-responsive" style="height: 570px; width: 100%; overflow: auto;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px; border-radius: 15px; padding: 0;">
    <table class="table text-center" id="userTable" style="border-radius: 15px; border-collapse: collapse; width: 100%; margin: 0;">
        <thead class="table sticky-header" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <tr>
                <th scope="col" onclick="sortTable(0)">Category</th>
                <th scope="col" onclick="sortTable(1)">Brand</th>
                <th scope="col" onclick="sortTable(2)">Flavor </th>
                <th scope="col" onclick="sortTable(3)">Description </th>
                <th scope="col" onclick="sortTable(4)">Quantity</th>
                <th scope="col" onclick="sortTable(5)">Cost</th>
                <th scope="col" onclick="sortTable(6)">Price</th>
                <th scope="col" onclick="sortTable(7)">Manufactured Date </th>
                <th scope="col" onclick="sortTable(8)">Expiration Date </th>
                <th scope="col" onclick="sortTable(9)">Status </th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="wwwwww" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
            <!-- PHP output goes here -->
        </tbody>
    </table>
   
</div>
<div id="pagination" class="pagination mt-2 float-right"></div>
<?php
// Query to fetch flavor or description from the database
$sql = "SELECT id, flavor, description FROM flavor";
$result = mysqli_query($conn, $sql);

if ($result) {
    foreach ($result as $row) {
        ?>
    
        <!-- Modal for displaying full description -->
        <div class="modal fade" id="descriptionModal<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="descriptionModalLabel<?= htmlspecialchars($row['id']) ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body" style="padding: 40px;">
       
                    <ion-icon name="close-circle-outline" class="float-right " style="font-size: 20px;" data-bs-dismiss="modal" aria-label="Close"></ion-icon>
                   <h5 style="text-decoration: none;">
                   <?= htmlspecialchars($row["description"]) ?> <!-- Display full description -->
                   </h5>
               
                  
                       
              
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
    <?php
// Query to fetch flavor or description from the database
$sql = "SELECT id, flavor, description FROM flavor";
$result = mysqli_query($conn, $sql);

if ($result) {
    foreach ($result as $row) {
        ?>
    
    <div class="modal fade" id="flavorModal<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="flavorModalLabel<?= htmlspecialchars($row['id']) ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            
            
                <!-- Display full flavor -->
          
                <div class="modal-body" style="padding: 40px;">
       
                    <ion-icon name="close-circle-outline" class="float-right " style="font-size: 20px;" data-bs-dismiss="modal" aria-label="Close"></ion-icon>
                   <h5 style="text-decoration: none;">
                   <?= htmlspecialchars($row["flavor"]) ?>
                   </h5>
               
                  
                       
              
                    </div>
            </div>
        </div>
    </div>

        <?php
    }
}
?>
            <?php
            $sql = "SELECT * FROM category INNER JOIN flavor ON category.id = flavor.rs_id";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                foreach ($result as $row):
                    ?>
                    <!-- Modal for each row -->
                    <div class="modal fade" id="editUserModal<?= htmlspecialchars($row["id"]) ?>" tabindex="-1"
     aria-labelledby="editUserModalLabel<?= htmlspecialchars($row["id"]) ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <!-- Modal Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Edit Product Details</h4>
                    <ion-icon name="close-circle-outline" data-bs-dismiss="modal" aria-label="Close" style="font-size: 30px; cursor: pointer;"></ion-icon>
                </div>

                <!-- Form Start -->
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($row["id"]) ?>">

                    <!-- Row 1: Category, Brand, and Flavor -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Category:</label>
                                <select class="form-control" id="tableSelect" name="category">
                                    <?php
                                    $categorySql = "SELECT DISTINCT id, category FROM category WHERE c_b_id = $branchid";
                                    $categoryResult = mysqli_query($conn, $categorySql);
                                    if ($categoryResult) {
                                        while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                                            ?>
                                            <option value="<?= htmlspecialchars($categoryRow["id"]); ?>"
                                                <?= ($categoryRow["id"] == $row["rs_id"]) ? "selected" : ""; ?>>
                                                <?= htmlspecialchars($categoryRow["category"]); ?>
                                            </option>
                                            <?php
                                        }
                                    } else {
                                        echo "Error: " . $categorySql . "<br>" . mysqli_error($conn);
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Brand:</label>
                                <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($row['brand']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Flavor:</label>
                                <input type="text" class="form-control" name="flavor" value="<?= htmlspecialchars($row['flavor']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Description, Quantity, and Price -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Description:</label>
                                <input type="text" class="form-control" name="description" value="<?= htmlspecialchars($row['description']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Quantity:</label>
                                <input type="number" class="form-control" name="quantity" value="<?= htmlspecialchars($row['quantity']) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Price:</label>
                                <input type="number" class="form-control" name="price"
                                       value="<?= htmlspecialchars($row['price']) ?>"
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Cost, Status, and Manufactured Date -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Cost:</label>
                                <input type="number" class="form-control" name="cost"
                                       value="<?= isset($_GET['cost']) ? htmlspecialchars($_GET['cost']) : htmlspecialchars($row['cost']) ?>"
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Status:</label>
                                <select class="form-control" name="status">
                                    <option value="0" <?= ($row["status"] == 0) ? "selected" : ""; ?>>On Hold</option>
                                    <option value="1" <?= ($row["status"] == 1) ? "selected" : ""; ?>>Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Manufactured Date:</label>
                                <input type="date" class="form-control" name="manu" value="<?= htmlspecialchars($row['manufactured_date']) ?>" >
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Expiration Date, Low Stock, and Image -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Expiration Date:</label>
                                <input type="date" class="form-control" name="expi" value="<?= htmlspecialchars($row['date']) ?>" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Low Stock Alert:</label>
                                <input type="number" class="form-control" name="lows" value="<?= htmlspecialchars($row['low']) ?>"
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Image:</label><br>
                                <img src="<?= !empty($row['image']) ? htmlspecialchars($row['image']) : '../images/27002.jpg'; ?>" alt="Current Image"
                                     class="img-thumbnail mb-2" style="max-width: 100px; max-height: 100px;">
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <input type="hidden" name="retain_image" value="1">
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="row mt-4">
                        <div class="col text-center">
                            <button type="submit" class="btn btn-success px-4" name="update">Update</button>
                            <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
                <!-- Form End -->
            </div>
        </div>
    </div>
</div>


                    <?php
                endforeach;
            }
            ?>







            <div class="modal fade" id="deleteTableModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true" >
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body" >
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right;font-size: 30px;transform:translateY(-10px)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <form method="post" action="">
    <div class="container" style="height: 350px; overflow: auto;">
        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchInput" placeholder="Search Categories..." onkeyup="filterTable()">
        </div>

        <table class="table table-hover text-center tablenangcategory" style="border-collapse: separate; border-spacing: 0; width: 100%; margin: 0; border-radius: 15px;">
            <thead class="sticky-top" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <tr>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody">
                <?php
                $sql = "SELECT * FROM `category` WHERE c_b_id = $branchid";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["category"]); ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Inventory Actions">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" title="edit" data-target="#editModal<?= $row["id"] ?>">
                                        <i class="fa-solid fa-pen-to-square" style="color: white; font-weight: bolder;"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" title="delete">
                                        <a href="delete_table.php?id=<?= htmlspecialchars($row["id"]) ?>" class="text-white text-decoration-none" onclick="return confirm('Are you sure you want to delete this item?');">
                                            <i class="fa-solid fa-trash" id="icondelete" style="color: white;"></i>
                                        </a>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal for each row -->
                        <div class="modal fade" id="editModal<?= $row["id"] ?>" tabindex="-2" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" style="transform: translateY(240px);">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form method="post" action="">
                                            <div class="form-group">
                                                <label for="editInput">Edit Item:</label>
                                                <input type="text" class="form-control" id="editInput" name="editedItem" value="<?= htmlspecialchars($row["category"]) ?>">
                                                <input type="hidden" name="itemId" value="<?= htmlspecialchars($row["id"]) ?>">
                                            </div>
                                            <input type="submit" class="btn btn-primary" name="change" id="change" value="Change">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // Handle query error
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                ?>
            </tbody>
        </table>
    </div>
</form>

                        </div>
                       
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-body">
    <!-- Close button for the modal -->
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>

    <!-- Form to update stock range -->
    <form method="post">
        <?php
        // Query to fetch flavor and low stock range from the 'flavor' table
        $sql = "SELECT id, flavor,quantity, low FROM flavor WHERE f_b_id = ? AND DATE > NOW() ";
        $stmt = $conn->prepare($sql); // Use prepared statement for better security
        $stmt->bind_param("i", $branchid); // Assuming $branchid is defined and holds the branch ID
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <!-- Input field to set stock range -->
        <div class="form-group">
            <label for="exampleInput">Enter Stock Range</label>
            <input type="number" class="form-control" id="exampleInput" name="low" aria-describedby="emailHelp"
                value="<?php echo isset($lows) ? htmlspecialchars($lows) : 1; ?>" 
                oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);"
                required>
            <small id="emailHelp" class="form-text text-muted">
                Set a stock range. If the stock falls below this range, it will be highlighted in red.
            </small>
        </div>

        <!-- Button to toggle check/uncheck all -->
        <div class="mb-3 d-flex">
        <input type="text" class="form-control" style="margin-right: 10px;" id="searchInpute" placeholder="Search Categories..." onkeyup="filterTableSa()">

        <div class="form-group">
            <button type="button" id="toggleCheckboxes" ><i class='bx bx-checkbox' ></i></button>
        </div>
</div>

        <!-- Table displaying flavors and current stock range, with a checkbox for selection -->
        <div class="table-responsive" id="tablesss" style="max-height: 100%; height: 400px; overflow-y: auto;">
            <table class="table table-striped table-bordered">
                <thead style="background-color: #BCF2F6; color: #091057;">
                    <tr>
                        <th>Flavor</th>
                        <th>Quantity</th>
                        <th>Current Stock Range</th>
                        <th>
                            <!-- Checkbox to select/deselect all items -->
                            <input type="checkbox" id="selectAllCheckbox">
                            Select
                        </th>
                    </tr>
                </thead>
                <tbody id="tabledailys">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['flavor']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['low']); ?></td>
                                <td>
                                    <input type="checkbox" name="selected_ids[]" class="itemCheckbox" value="<?php echo htmlspecialchars($row['id']); ?>">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No flavors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Submit button for the form -->
        <button type="submit" name="lowss" class="btn btn-primary addbutton float-right mt-5">Submit</button>
    </form>
</div>


                    </div>
                </div>
            </div>


            
            <?php

// Query to fetch flavors and related details
$sql = "SELECT flavor.id, category.category, flavor.flavor, flavor.quantity ,flavor.price
        FROM category 
        INNER JOIN flavor ON category.id = flavor.rs_id 
        INNER JOIN branch ON flavor.f_b_id = branch.id 
        WHERE branch.id = $branchid";

$result = mysqli_query($conn, $sql);
?>

<!-- Main Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <ion-icon name="close-circle-outline" data-bs-dismiss="modal" aria-label="Close" style="float:right; transform:translateY(-10px); font-size: 20px"></ion-icon>
                <div class="mb-3">
                    <input type="text" id="searchInputssss" onkeyup="filterTableSS()" placeholder="Search for flavors..." class="form-control" />
                </div>
            
                <div id="tablenaten" class="table-responsive" style="height: 350px; width: 100%; overflow: auto;">
                    <table class="table">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th scope="col">Category</th>
                                <th scope="col">Flavor</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="flavorTableBody">
                            <?php
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["category"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["flavor"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["quantity"]); ?></td>
                                        <td>
                                            <button 
                                                class="btn btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#transferItemModal<?php echo htmlspecialchars($row['id']); ?>" 
                                                title="Transfer Item">
                                                <i class='bx bx-transfer'></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='4'>No data found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Item Modals -->
<?php
// Fetch the flavors again for transfer modals
$result = mysqli_query($conn, $sql); 
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="modal fade" id="transferItemModal<?= htmlspecialchars($row["id"]); ?>" tabindex="-1" aria-labelledby="transferItemModalLabel<?= htmlspecialchars($row["id"]); ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <ion-icon name="close-circle-outline" data-bs-dismiss="modal" aria-label="Close" style="float:right; transform:translateY(-10px); font-size: 20px"></ion-icon>
                        <form method="post" id="transferForm<?= htmlspecialchars($row["id"]); ?>">
                            <div class="mb-3">
                                <input type="hidden" id="transferid" name="transferid" value="<?= htmlspecialchars($row["id"]); ?>">
                                <label for="productName" class="form-label">Product Name:</label>
                                <input type="text" readonly style="border:none;text-transform: uppercase;background:transparent" name="flavor" value="<?= htmlspecialchars($row["flavor"]); ?>">
                                <input type="hidden" readonly style="border:none;text-transform: uppercase;background:transparent" name="price" value="<?= htmlspecialchars($row["price"]); ?>">

                            </div>
                            <div class="form-group">
                                <label for="option">Select Branch</label>
                                <select class="form-control" id="tableSelect" name="option">
                                    <?php
                                    $branchSql = "SELECT * FROM `branch` where id != $branchid";
                                    $branchResult = mysqli_query($conn, $branchSql);
                                    if ($branchResult && mysqli_num_rows($branchResult) > 0) {
                                        while ($branchRow = mysqli_fetch_assoc($branchResult)) {
                                            ?>
                                            <option value="<?php echo htmlspecialchars($branchRow["id"]); ?>">
                                                <?php echo htmlspecialchars($branchRow["branch"]); ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="transquantity" 
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);" 
                                       required oninput="if(this.value < 1) this.value = 1;">
                            </div>
                            <button type="submit" class="btn btn-primary" name="transferitems">Transfer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>


<?php
$sql = "SELECT * FROM flavor";
$result = mysqli_query($conn, $sql);

if ($result) {
    foreach ($result as $row): ?>
        <div class="modal fade" id="addstocks<?= htmlspecialchars($row["id"]); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel<?= htmlspecialchars($row["id"]); ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Stock</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" id="idInput" name="idofstocks" value="<?= htmlspecialchars($row["id"]); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="numberofstocks">How many</label>
                                <input type="number" class="form-control" name="numberofstocks" min="1"  oninput="this.value = Math.max(this.value, '')" required>
                            </div>
                            <button type="submit" name="addstocks" class="btn btn-primary addbutton">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
}
?>
    </div>
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

    <script>
        function displayModalId(id) {
            document.getElementById('idInput').value = id;
        }
    </script>
  <?php 
 require '../include/footer.php';
 ?>
<!-- <script>
    function filterTableSaa() {
        const input = document.getElementById('searchInputeaaa');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tabledailyaas');
        const tr = table.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            const tds = tr[i].getElementsByTagName('td'); // Get all <td> elements for the current row

            // Ensure there are at least 8 columns before accessing them
            if (tds.length >= 8) {
                const txtValueCategory = tds[0].textContent || tds[0].innerText; // index 0
                const txtValueFlavor = tds[1].textContent || tds[1].innerText;   // index 1
                const txtValueAnother = tds[2].textContent || tds[2].innerText;  // index 2
                const txtValueAnother2 = tds[3].textContent || tds[3].innerText; // index 3
                const txtValueCol4 = tds[4].textContent || tds[4].innerText;     // index 4
                const txtValueCol5 = tds[5].textContent || tds[5].innerText;     // index 5
                const txtValueCol6 = tds[6].textContent || tds[6].innerText;     // index 6
                const txtValueCol7 = tds[7].textContent || tds[7].innerText;     // index 7

                // Check if any of the column values match the filter
                if (txtValueCategory.toLowerCase().indexOf(filter) > -1 || 
                    txtValueFlavor.toLowerCase().indexOf(filter) > -1 || 
                    txtValueAnother.toLowerCase().indexOf(filter) > -1 || 
                    txtValueAnother2.toLowerCase().indexOf(filter) > -1 ||
                    txtValueCol4.toLowerCase().indexOf(filter) > -1 ||
                    txtValueCol5.toLowerCase().indexOf(filter) > -1 ||
                    txtValueCol6.toLowerCase().indexOf(filter) > -1 ||
                    txtValueCol7.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Show the row
                } else {
                    tr[i].style.display = "none"; // Hide the row
                }
            }
        }
    }
</script> -->

<script>
    function filterTableSa() {
        const input = document.getElementById('searchInpute');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tabledailys');
        const tr = table.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            const tdCategory = tr[i].getElementsByTagName('td')[0]; // index 0
            const tdFlavor = tr[i].getElementsByTagName('td')[1];   // index 1
            const tdAnother = tr[i].getElementsByTagName('td')[2];  // index 2
            const tdAnother2 = tr[i].getElementsByTagName('td')[3]; // index 3
            if (tdCategory || tdFlavor || tdAnother || tdAnother2) {
                const txtValueCategory = tdCategory ? tdCategory.textContent || tdCategory.innerText : "";
                const txtValueFlavor = tdFlavor ? tdFlavor.textContent || tdFlavor.innerText : "";
                const txtValueAnother = tdAnother ? tdAnother.textContent || tdAnother.innerText : "";
                const txtValueAnother2 = tdAnother2 ? tdAnother2.textContent || tdAnother2.innerText : "";

                if (txtValueCategory.toLowerCase().indexOf(filter) > -1 || 
                    txtValueFlavor.toLowerCase().indexOf(filter) > -1 || 
                    txtValueAnother.toLowerCase().indexOf(filter) > -1 || 
                    txtValueAnother2.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Show the row
                } else {
                    tr[i].style.display = "none"; // Hide the row
                }
            }
        }
    }
</script>
<script>
    // Automatically toggle modals
    const salesModal = new bootstrap.Modal(document.getElementById('transferModal'));

    // Close Sales Modal when any Transfer Item Modal opens
    document.querySelectorAll('[id^="transferItemModal"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            salesModal.hide();
        });

        // Open Sales Modal when the Transfer Item Modal closes
        modal.addEventListener('hidden.bs.modal', function () {
            salesModal.show();
        });
    });
</script>
<script>
document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.itemCheckbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

document.getElementById('toggleCheckboxes').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.itemCheckbox');
    const allChecked = [...checkboxes].every(checkbox => checkbox.checked);
    
    checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
    
    // Update button text based on the new state
    this.innerHTML = allChecked ? '<i class="bx bx-checkbox"></i>' : '<i class="bx bx-check-square"></i>';
});

</script>
<script>
function downloadTable() {
    let table = document.getElementById("userTable");
    let rows = table.rows;
    let csvContent = "";

    // Loop through rows and extract data
    for (let i = 0; i < rows.length; i++) {
        let cols = rows[i].cells;
        let rowData = [];
        for (let j = 0; j < cols.length; j++) {
            rowData.push(cols[j].innerText.replace(/,/g, "")); // Remove commas to avoid CSV issues
        }
        csvContent += rowData.join(",") + "\n";
    }

    // Create a downloadable link
    let blob = new Blob([csvContent], { type: "text/csv" });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "table_data.csv";

    // Simulate clicking the link
    link.click();
}

function printTable() {
    let tableContents = document.getElementById("tablenaten").innerHTML;
    let printWindow = window.open("", "", "height=600,width=1000");
    printWindow.document.write("<html><head><title>Print Table</title>");
    printWindow.document.write("<style>table {width: 100%; border-collapse: collapse;} th, td {border: 1px solid #000; padding: 8px; text-align: center;}</style>");
    printWindow.document.write("</head><body>");
    printWindow.document.write(tableContents);
    printWindow.document.write("</body></html>");
    printWindow.document.close();

    // Open the print dialog
    printWindow.print();
}
</script>

<script>
    function filterTableSS() {
        const input = document.getElementById('searchInputssss');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('flavorTableBody');
        const tr = table.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            const tdCategory = tr[i].getElementsByTagName('td')[0];
            const tdFlavor = tr[i].getElementsByTagName('td')[1];
            if (tdCategory || tdFlavor) {
                const txtValueCategory = tdCategory.textContent || tdCategory.innerText;
                const txtValueFlavor = tdFlavor.textContent || tdFlavor.innerText;
                if (txtValueCategory.toLowerCase().indexOf(filter) > -1 || 
                    txtValueFlavor.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Show the row
                } else {
                    tr[i].style.display = "none"; // Hide the row
                }
            }
        }
    }
</script>
<script>
$(document).ready(function() {
    let currentQuery = '';
    let currentDate = '';
    let currentPage = 1;
    let sortOrder = new Array(10).fill(1); // Adjust array size to your column count

    // Load data function
    function loadData(query = '', date = '', page = 1, columnIndex = null) {
        currentQuery = query;
        currentDate = date;
        currentPage = page;

        $.ajax({
    url: 'fetch_inve.php', // PHP file to fetch data
    type: 'POST',
    dataType: 'json',
    data: { query, date, page, sortColumn: columnIndex, sortOrder: sortOrder[columnIndex] || 1 },
    success: function(response) {
        $('#wwwwww').html(response.content); // Update table body with response data
        $('#totalDisplay').html('<i class="fa-solid fa-peso-sign"></i> ' + response.totalSystemTally); // Display overall total
        setupPagination(response.totalPages, page);
    }
});

    }

    // Set up pagination
    function setupPagination(totalPages, currentPage) {
        let paginationHtml = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';
        
        // Previous button
        if (currentPage > 1) {
            paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="${currentPage - 1}">Previous</button></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">Previous</span></li>`;
        }

        // Determine page range
        const maxPagesToShow = 10;
        let startPage = Math.max(1, currentPage - 3);
        let endPage = Math.min(totalPages, startPage + 6);

        if (endPage - startPage < 6) {
            startPage = Math.max(1, endPage - 6);
        }

        if (startPage > 1) {
            paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="1">1</button></li>`;
            if (startPage > 2) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><button class="page-link pagination-btn" data-page="${i}">${i}</button></li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="${totalPages}">${totalPages}</button></li>`;
        }

        // Next button
        if (currentPage < totalPages) {
            paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="${currentPage + 1}">Next</button></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
        }

        paginationHtml += '</ul></nav>';
        $('#pagination').html(paginationHtml);
    }

    // Sort table based on column index
    function sortTable(columnIndex) {
        sortOrder[columnIndex] *= -1; // Toggle sorting direction
        loadData(currentQuery, currentDate, currentPage, columnIndex);
        updateSortArrows(columnIndex);
    }

    // Update sort arrows based on the active column
    function updateSortArrows(activeIndex) {
        const arrows = document.querySelectorAll('.sort-arrow');
        arrows.forEach((arrow, index) => {
            arrow.innerHTML = '&#x25B2;'; // Default to ascending
            if (index === activeIndex) {
                arrow.innerHTML = sortOrder[index] === 1 ? '&#x25B2;' : '&#x25BC;'; // Up or down
            }
        });
    }

    // Event listeners
    $('#search').on('input', function() {
        currentPage = 1;
        loadData($(this).val(), currentDate, currentPage);
    });

    $('#dateInput').on('change', function() {
        currentPage = 1;
        loadData(currentQuery, $(this).val(), currentPage);
    });

    $('#resetButton').on('click', function() {
        $('#search').val('');
        $('#dateInput').val('');
        currentPage = 1;
        loadData();
    });

    $(document).on('click', '.pagination-btn', function() {
        const page = $(this).data('page');
        loadData(currentQuery, currentDate, page);
    });

    loadData(); // Initial load
});
</script>




    <script>
        document.getElementById("editInput").addEventListener("keydown", function (event) {
            // Check if the pressed key is a space
            if (event.key === " ") {
                // Prevent the default action (typing a space)
                event.preventDefault();
            }
        });
    </script>

    <script>
        document.getElementById("beverages").addEventListener("keydown", function (event) {
            // Check if the pressed key is a space
            if (event.key === " ") {
                // Prevent the default action (typing a space)
                event.preventDefault();
            }
        });
    </script>

    <script>
        // menuToggle
        let toggle = document.querySelector('.toggle')
        let navigation = document.querySelector('.navigation')
        let main = document.querySelector('.main')

        toggle.onclick = function () {
            navigation.classList.toggle('active')
            main.classList.toggle('active')
        }
        // add hovered class in selected list item
        let list = document.querySelectorAll('.navigation li')

        function activeLink() {
            list.forEach((item) =>
                item.classList.remove('hovered'))
            this.classList.add('hovered')
        }
        list.forEach((item) =>
            item.addEventListener('mouseover', activeLink))
    </script>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
        <?php
        if (isset($_SESSION['invemessage'])) { ?>
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('<?php echo $_SESSION['invemessage']; ?>');
            <?php
            unset($_SESSION['invemessage']);
        } ?>
    </script>
    <script>
        <?php
        if (isset($_SESSION['error_inve'])) { ?>
            alertify.set('notifier', 'position', 'bottom-left');
               alertify.error(' <?php echo $_SESSION['error_inve']; ?> ');
                <?php unset($_SESSION['error_inve']);
        } ?>
    </script>

<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['errorinven'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo $_SESSION['errorinven']; ?>');

            // Show the modal
            $('#addItemModal').modal('show');

            <?php unset($_SESSION['errorinven']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function() {
          <?php if (isset($_SESSION['error_inveaaa'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo addslashes($_SESSION['error_inveaaa']); ?>'); // Escape any quotes

            // Show the modal
            $('#transferModal').modal('show');

            <?php unset($_SESSION['error_inveaaa']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['error_invea'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo $_SESSION['error_invea']; ?>');

            // Show the modal
            $('#exampleModal').modal('show');

            <?php unset($_SESSION['error_invea']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>
<script>
// JavaScript function to filter table rows based on the search input
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const tableBody = document.getElementById("categoryTableBody");
    const rows = tableBody.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        const td = rows[i].getElementsByTagName("td")[0]; // Get the category cell
        if (td) {
            const txtValue = td.textContent || td.innerText;
            rows[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>
<script>
    document.getElementById('confirmLogout').onclick = function() {
        // Redirect to logout page on confirmation
        window.location.href = "../logout.php";
    };
</script>

</body>

</html>