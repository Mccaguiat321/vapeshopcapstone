<?php

include "../database/db_conn.php";
require 'function.php';
if (empty(isset($_SESSION['user_id']))) {
    header("location: ../all_login.php");
}
if (isset($_GET['id']) && isset($_GET['branch'])) {
    $dashboardid = intval($_GET['id']);
   
    $_SESSION['branch_id'] = $dashboardid;
}

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$branch = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/bootstrap4.5.2.css">
    <link rel="stylesheet" href="../css/awe.css">
    <link rel="shortcut icon" href="favicon.ico">
    <title>Cloud Keepers
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />

    <link rel="stylesheet" href="side_bar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Paaji+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            
            body{
           
           overflow-y: hidden;
       }
 
 
       #searchInputssss{
   max-width: 100%;
   width: 720px;
   border: 1px solid #003366;
   border-radius: 10px;
   height: 43px;
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



       th,thead {
           font-family: "Baloo Paaji 2", sans-serif;
 font-optical-sizing: auto;
 font-weight: 700;
 font-style: normal;
           letter-spacing: 1px;
           font-variation-settings:
               "wdth" 100;
           font-size: 16px;
           background-color: #22092C !important;
           color: white !important;            
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
a{
   text-decoration: none !important;
}
.modal-content{
   border-radius: 30px;
   padding: 10px;
}

 .modal-body, .modal-content {
   border-radius: 40px; /* Apply border radius */
   background-color: #F4F6FF; /* Set background color */
  

 }

 #exampleModal > .modal-body {
   padding: 20px; /* Add padding */
   display: flex;
   flex-direction: column; /* Align content vertically */
   align-items: stretch; /* Make items stretch to full width */
 }

 #exampleModal > .form-group {
   margin-bottom: 20px; /* Add margin for spacing */
 }

 #exampleModal > .form-control {
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

 #exampleModal >  .modal-footer {
   border-top: none; /* Remove footer border */
   justify-content: flex-end; /* Align buttons to the right */
   padding: 10px; /* Add padding */
 }

 #exampleModal > .btn {
   transition: background-color 0.3s, transform 0.2s; /* Smooth transition */
 }

 #exampleModal > .btn-primary {
   background-color: #007bff; /* Bootstrap primary color */
 }

 #exampleModal > .btn-primary:hover {
   background-color: #0056b3; /* Darker on hover */
   transform: translateY(-2px); /* Lift effect */
 }

 #exampleModal > .btn-secondary {
   margin-left: 10px; /* Spacing between buttons */
 }

 .topbutton{
   background-color: #0B192C  !important;
   color: white !important;
   padding: 8px 10px !important;
   font-family: "Baloo Paaji 2", sans-serif;
 font-optical-sizing: auto;
 font-weight: 700;
 font-style: normal;
 }
 .topbutton:hover{
   background-color: #1230AE  !important;
   color: #fff !important;
   padding: 8px 10px !important;
 }

 .container {
   scrollbar-width: none; /* For Firefox */
}
#tablenaten::-webkit-scrollbar {
   display: none;
}


/* Hide scrollbar for Firefox */
#tablenaten {
   scrollbar-width: none; /* For Firefox */
}
.tablenangcategory{
   box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
   }
   .closee:hover{
       background-color :red;
       color: wheat;
   } 
   #totalDisplay {
    max-width: 100%;
    width: 200px;
    height: 43px;
    border: 1px solid #003366;
    border-radius: 10px;
    text-align: center; /* Horizontal center */
    line-height: 43px; /* Match the height for vertical center */
    margin-right: 10px;
}
#dateInput{
    border-radius: 10px;
    max-width: 100%;
    width: 200px;
    height: 43px;
   }

   .pagination {
    display: flex;
    justify-content: right;
    gap: 5px;
    margin-top: 5px;
}

.pagination-btn {
    padding: 5px 10px;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f0f0f0;
}

.pagination-btn.active {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
}


   </style>
</head>

<body>
   
<header class="header">
            <div class="header__container">
              

                <p href="#" class="header__logo" style="margin-top: 12px">Void </p>

    
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
                            <a href="../inventory/peninventory.php" class="nav__link active">
                            <i class='bx bxs-package nav__icon'></i>
                            <span class="nav__name">Inventory</span>             
                            </a> 
                            <a href="../acc/accountdashboard.php" class="nav__link active">   
                            <i class='bx bx-user nav__icon'></i>
                            <span class="nav__name">Users</span>             
                            </a> 
                            <a href="#" class="nav__link active">   
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
        <!-- main -->

        
        <div class="main">
          
        <div class="d-flex">
        <div class="mb-3">
    <input type="text" id="searchInputssss" onkeyup="filterTableSS()" placeholder="Search for flavors..."  style="margin-right: 10px" class="form-control" />
</div>


<input type="date" id="dateInput" style="margin-right: 10px" >

<div id="totalDisplay" class="text-center" style="font-weight: bold; "></div>

<button id="resetButton" class="btn mb-3 topbutton"style="margin-right: 10px" title="Reset Date" ><i class="fa-solid fa-rotate"></i></button>
<button id="downloadBtn" class="btn mb-3 topbutton" onclick="downloadTable()" style="margin-right: 10px"
title="Download"
><i class="fa-solid fa-download"></i></button>
   
<button id="printBtn" class="btn mb-3 topbutton" onclick="printTable()"
title="Print"
style="margin-right: 10px"><i class="fa-solid fa-print"></i></button>


</div>
            <div id="tablenaten" class="table-responsive" style="height: 620px; width: 100%; overflow: auto;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px; border-radius: 15px; padding: 0;">
    <table class="table text-center" id="userTable" style="border-radius: 15px; border-collapse: collapse; width: 100%; margin: 0;">
        <thead class="table sticky-header" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <tr>  
            <th>ORDER NUMBER</th>
                <th>Flavor</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Date</th>
                <th>Action</th>
        </thead>
        <tbody id="tableBody" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
        </tbody>
    </table>
</div>
<div id="pagination" class="pagination"></div>


        
<?php
$sql = "SELECT order_details.id as order_details_ID,
        order_details.PID,
        order_details.OID,
        flavor.flavor,
        order_details.PRICE,
        order_details.QTY,  
        order_details.TOTAL,
        flavor.cost
FROM orders
INNER JOIN order_details ON orders.id = order_details.OID
INNER JOIN flavor ON order_details.PID = flavor.id";
        
$result = mysqli_query($conn, $sql);

if ($result) {
    foreach ($result as $row):
        ?>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal<?= htmlspecialchars($row["order_details_ID"]) ?>" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                
                    <div class="modal-body">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <form method="post" class="container mt-4">
    <h3 class="mb-4 text-center">Return Item</h3>
    
    <input type="hidden" name="order_details_id" value="<?= htmlspecialchars($row['order_details_ID']) ?>">
    <input type="hidden" class="form-control" name="product_id" readonly value="<?= htmlspecialchars($row['PID']) ?>">
    <input type="hidden" class="form-control" name="order_id" readonly value="<?= htmlspecialchars($row['OID']) ?>">
    <input type="hidden" class="form-control" name="flavor_cost" readonly value="<  ?= htmlspecialchars($row['cost']) ?>">
    <input type="hidden" class="form-control" name="product_name" readonly value="<?= htmlspecialchars($row['flavor']) ?>">
    <input type="hidden" class="form-control" name="product_price" readonly value="<?= htmlspecialchars($row['PRICE']) ?>">
    <input type="hidden" class="form-control" name="total_amount" readonly value="<?= htmlspecialchars($row['TOTAL']) ?>">

    <div class="row mb-3">
    <div class="col-md-6">
        <label for="current_quantity" class="form-label">Current Quantity</label>
        <input type="number" class="form-control" name="current_quantity" readonly value="<?= htmlspecialchars($row['QTY']) ?>">
    </div>
    <div class="col-md-6">
        <label for="input_quantity" class="form-label">New Quantity</label>
        <input type="number" class="form-control" name="input_quantity" required
            value="<?= isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity']) : ''; ?>"
            oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);">
    </div>
</div>

    <button type="submit" name="void" class="btn btn-danger btn-block">Void Item</button>
</form>

                    </div>
                </div>
            </div>
        </div>
        <?php
    endforeach;
}
?>
<?php
$sql = "SELECT order_details.id as order_details_ID,
        order_details.PID,
        order_details.OID,
        flavor.flavor,
        order_details.PRICE,
        order_details.QTY,  
        order_details.TOTAL,
        flavor.cost
FROM orders
INNER JOIN order_details ON orders.id = order_details.OID
INNER JOIN flavor ON order_details.PID = flavor.id";
        
$result = mysqli_query($conn, $sql);

if ($result) {
    foreach ($result as $row):
        ?>
        <!-- Modal -->
        <div class="modal fade" id="dispose<?= htmlspecialchars($row["order_details_ID"]) ?>" tabindex="-1"
            aria-labelledby="disposeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                
                    <div class="modal-body">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <form method="post" class="container mt-4">
    <h3 class="mb-4 text-center">Dispose Item</h3>
    <input type="hidden" name="order_details_id" value="<?= htmlspecialchars($row['order_details_ID']) ?>">
    <div class="row">
        <div class="col-md-6 mb-3">
            <input type="hidden" class="form-control" name="product_id" readonly value="<?= htmlspecialchars($row['PID']) ?>">
            <input type="hidden" class="form-control" name="order_id" readonly value="<?= htmlspecialchars($row['OID']) ?>">
            <input type="hidden" class="form-control" name="flavor_cost" readonly value="<?= htmlspecialchars($row['cost']) ?>">
            <input type="hidden" class="form-control" name="product_name" readonly value="<?= htmlspecialchars($row['flavor']) ?>">
            <input type="hidden" class="form-control" name="product_price" readonly value="<?= htmlspecialchars($row['PRICE']) ?>">
            
            <label for="current_quantity" class="form-label">Current Quantity</label>
            <input type="number" class="form-control" name="current_quantity" readonly value="<?= htmlspecialchars($row['QTY']) ?>"
            >
        </div>
        <div class="col-md-6 mb-3">
            <label for="input_quantity" class="form-label">New Quantity</label>
            <input type="number" class="form-control" name="input_quantity" required
            value="<?= isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity']) : ''; ?>"
            oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = this.value === '' ? '' : Math.max(this.value, 1);"
            >
        </div>
    </div>

    <div class="mb-3">
        <label for="reason" class="form-label">Reason for Disposal</label>
        <textarea name="reason" id="reason" class="form-control" rows="4" required></textarea>
    </div>

    <input type="hidden" class="form-control" name="total_amount" readonly value="<?= htmlspecialchars($row['TOTAL']) ?>">

    <button type="submit" name="dispose" class="btn btn-danger btn-block">Dispose Item</button>
</form>

                    </div>
                </div>
            </div>
        </div>
        <?php
    endforeach;
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

<?php 
 require '../include/footer.php';
 ?>

<script>
$(document).ready(function() {
    let currentQuery = '';
    let currentDate = '';
    let currentPage = 1;

    function loadData(query = '', date = '', page = 1) {
        currentQuery = query;
        currentDate = date;
        currentPage = page;

        $.ajax({
            url: 'navigatelive.php',
            type: 'POST',
            dataType: 'json',
            data: { query: query, date: date, page: page },
            success: function(response) {
                $('#tableBody').html(response.content);
                $('#totalDisplay').html('<i class="fa-solid fa-peso-sign"></i> ' + response.totalSystemTally);
                setupPagination(response.totalPages, page);
            }
        });
    }

    function setupPagination(totalPages, currentPage) {
        let paginationHtml = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';

        // "Previous" button
        if (currentPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <button class="page-link pagination-btn" data-page="${currentPage - 1}">Previous</button>
                </li>`;
        } else {
            paginationHtml += `
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>`;
        }

        // Display page numbers with ellipsis
        const maxPagesToShow = 10; // Max number of boxes including Previous and Next
        let startPage = Math.max(1, currentPage - 3); // Show current page +/- 3
        let endPage = Math.min(totalPages, startPage + 6); // Show 7 pages in total (including current)

        // Adjust startPage if endPage is less than totalPages
        if (endPage - startPage < 6) {
            startPage = Math.max(1, endPage - 6);
        }

        // Ensure to display the first page
        if (startPage > 1) {
            paginationHtml += `
                <li class="page-item"><button class="page-link pagination-btn" data-page="1">1</button></li>`;
            if (startPage > 2) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Page number buttons
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link pagination-btn" data-page="${i}">${i}</button>
                </li>`;
        }

        // Ensure to display the last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHtml += `
                <li class="page-item"><button class="page-link pagination-btn" data-page="${totalPages}">${totalPages}</button></li>`;
        }

        // "Next" button
        if (currentPage < totalPages) {
            paginationHtml += `
                <li class="page-item">
                    <button class="page-link pagination-btn" data-page="${currentPage + 1}">Next</button>
                </li>`;
        } else {
            paginationHtml += `
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>`;
        }

        paginationHtml += '</ul></nav>';
        $('#pagination').html(paginationHtml);
    }

    $('#searchInputssss').on('input', function() {
        currentPage = 1; // Reset to first page
        loadData($(this).val(), currentDate, currentPage);
    });

    $('#dateInput').on('change', function() {
        currentPage = 1; // Reset to first page
        loadData(currentQuery, $(this).val(), currentPage);
    });

    $('#resetButton').on('click', function() {
        $('#search').val('');
        $('#dateInput').val('');
        currentQuery = '';
        currentDate = '';
        currentPage = 1;
        loadData(); // Reload without filters
    });

    $(document).on('click', '.pagination-btn', function() {
        let page = $(this).data('page');
        loadData(currentQuery, currentDate, page);
    });

    loadData();
});
</script>

<script>
    function filterTableSS() {
        const input = document.getElementById('searchInputssss');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tableBody');
        const tr = table.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            // Get references to the first five columns (0 to 4)
            const tdCategory = tr[i].getElementsByTagName('td')[0];
            const tdFlavor = tr[i].getElementsByTagName('td')[1];
            const tdColumn2 = tr[i].getElementsByTagName('td')[2];
            const tdColumn3 = tr[i].getElementsByTagName('td')[3];
            const tdColumn4 = tr[i].getElementsByTagName('td')[4];

            if (tdCategory || tdFlavor || tdColumn2 || tdColumn3 || tdColumn4) {
                const txtValueCategory = tdCategory.textContent || tdCategory.innerText;
                const txtValueFlavor = tdFlavor.textContent || tdFlavor.innerText;
                const txtValueColumn2 = tdColumn2.textContent || tdColumn2.innerText;
                const txtValueColumn3 = tdColumn3.textContent || tdColumn3.innerText;
                const txtValueColumn4 = tdColumn4.textContent || tdColumn4.innerText;

                // If the input matches any of the five columns, show the row
                if (txtValueCategory.toLowerCase().indexOf(filter) > -1 || 
                    txtValueFlavor.toLowerCase().indexOf(filter) > -1 || 
                    txtValueColumn2.toLowerCase().indexOf(filter) > -1 || 
                    txtValueColumn3.toLowerCase().indexOf(filter) > -1 || 
                    txtValueColumn4.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Show the row
                } else {
                    tr[i].style.display = "none"; // Hide the row
                }
            }
        }
    }
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
    $(document).ready(function() {
        <?php if (isset($_SESSION['void'])): ?>
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo addslashes($_SESSION['void']); ?>');
            
            // Get the order_details_id from the URL parameter and show the specific modal
            const urlParams = new URLSearchParams(window.location.search);
            const modalID = urlParams.get('order_details_id');
            if (modalID) {
                $('#exampleModal' + modalID).modal('show');
            }

            <?php unset($_SESSION['void']); ?>
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['dispose'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.error('<?php echo addslashes($_SESSION['dispose']); ?>'); // Escape any quotes
            
            // Get parameters from the URL
            const urlParams = new URLSearchParams(window.location.search);
            const modalID = urlParams.get('order_details_id');
            
            // Show the modal if modalID is present
            if (modalID) {
                $('#dispose' + modalID).modal('show');
            }

            <?php unset($_SESSION['dispose']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>

        <script>
            <?php
            if (isset($_SESSION['voidsuccess'])) { ?>
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('<?php echo $_SESSION['voidsuccess']; ?>');
                <?php
                unset($_SESSION['voidsuccess']);
            } ?>
        </script>
     
     <script>
    document.getElementById('confirmLogout').onclick = function() {
        // Redirect to logout page on confirmation
        window.location.href = "../logout.php";
    };
</script>
</body>

</html>