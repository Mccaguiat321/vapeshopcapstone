<?php
session_start();
include "../database/db_conn.php";
require 'function.php';
if (isset($_GET['id']) && isset($_GET['branch'])) {
    $dashboardid = intval($_GET['id']);
   
    $_SESSION['branch_id'] = $dashboardid;
}

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
date_default_timezone_set('Asia/Manila');
// Query to get daily sales data
// Query to get daily sales data for the last 7 days
$query_daily = $conn->query("
SELECT  
    DATE(ORDER_DATE) as ORDER_DATE,
    SUM(TOTAL) as TOTAL
FROM order_details
WHERE ORDER_DATE >= CURDATE() - INTERVAL (WEEKDAY(CURDATE())) DAY 
    AND ORDER_DATE <= CURDATE() + INTERVAL (6 - WEEKDAY(CURDATE())) DAY
    AND MONTH(ORDER_DATE) = MONTH(CURDATE())
    AND YEAR(ORDER_DATE) = YEAR(CURDATE())
    AND b_id = $branchid
GROUP BY DATE(ORDER_DATE)
ORDER BY DATE(ORDER_DATE) ASC 
");

if (!$query_daily) {
    die("Error in daily sales query: " . mysqli_error($conn));
}

// Fetching daily sales data
$day = array();
$amount_daily = array();

foreach ($query_daily as $data) {
    // Convert ORDER_DATE to desired format
    $date = new DateTime($data['ORDER_DATE']);
    $formattedDate = $date->format('Y-m-d'); // Use 'Y-m-d' for easier parsing in JavaScript
    $day[] = $formattedDate;
    $amount_daily[] = (float)$data['TOTAL']; // Cast to float for consistency
}

// Convert to JSON
$json_days = json_encode($day);
$json_amounts = json_encode($amount_daily);



// Query to get monthly sales data
$query_monthly = $conn->query("
    SELECT 
        MONTHNAME(ORDER_DATE) as ORDER_MONTH,
        SUM(TOTAL) as TOTAL
    FROM order_details
    WHERE b_id = $branchid
    GROUP BY MONTH(ORDER_DATE)
    ORDER BY MONTH(ORDER_DATE) ASC
");

if (!$query_monthly) {
    die("Error in monthly sales query: " . mysqli_error($conn));
}

// Fetching monthly sales data
$month = array();
$amount_monthly = array();

foreach ($query_monthly as $data) {
    $month[] = $data['ORDER_MONTH'];
    $amount_monthly[] = $data['TOTAL'];
}


$sql = "SELECT COUNT(*) AS total_items FROM flavor WHERE f_b_id = $branchid";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_items = $row['total_items'];
} else {
    $total_items = 0;
}

$query_yearly = $conn->query("
    SELECT 
        YEAR(ORDER_DATE) as ORDER_YEAR,
        SUM(TOTAL) as TOTAL
    FROM order_details
    WHERE b_id = $branchid
    GROUP BY YEAR(ORDER_DATE)
    ORDER BY YEAR(ORDER_DATE) ASC
");

if (!$query_yearly) {
    die("Error in yearly sales query: " . mysqli_error($conn));
}

$yearly_sales = array(); // Array to store yearly sales data

// Fetch each row from the query result and store in $yearly_sales array
while ($data = $query_yearly->fetch_assoc()) {
    $yearly_sales[$data['ORDER_YEAR']] = (float)$data['TOTAL']; // Ensure TOTAL is a float for Highcharts
}

// Convert yearly sales data to JSON format
$years = json_encode(array_keys($yearly_sales));
$sales = json_encode(array_values($yearly_sales));


date_default_timezone_set('Asia/Manila');
$today_date = date("Y-m-d");
$margin = null; // Initialize $margin as null

// SQL query to get daily sales for today


// if ($total_sales === null) {
//     $total_sales = "0";
// } else {
//     $sql_margin = "SELECT SUM(MARGIN) AS daily_margin
//                    FROM order_details
//                    WHERE DATE(ORDER_DATE) = '$today_date'";

//     $result_margin = mysqli_query($conn, $sql_margin);

//     if (!$result_margin) {
//         die("Error in SQL query for daily margin: " . mysqli_error($conn));
//     }

//     $row_margin = mysqli_fetch_assoc($result_margin);

//     if ($row_margin['daily_margin'] === null) {
//         $margin = null;
//     } else {
//         $margin = $total_sales - $row_margin['daily_margin'];
//     }
// }


$margin = 0; 

$sql_sales = "SELECT SUM(TOTAL) AS total_sales
              FROM order_details where b_id =$branchid";

$result_sales = mysqli_query($conn, $sql_sales);

if (!$result_sales) {
    die("Error in SQL query for daily sales: " . mysqli_error($conn));
}

$row_sales = mysqli_fetch_assoc($result_sales);
$total_sales = $row_sales['total_sales']; 

if ($total_sales === null) {
    $total_sales = "0";
} else {
    $sql_margin = "SELECT SUM(MARGIN) AS total_margin
                   FROM order_details where b_id = $branchid";

    $result_margin = mysqli_query($conn, $sql_margin);

    if (!$result_margin) {
        die("Error in SQL query for daily margin: " . mysqli_error($conn));
    }

    $row_margin = mysqli_fetch_assoc($result_margin);

    if ($row_margin['total_margin'] === null) { 
        $margin = null;
    } else {
        $margin = $total_sales - $row_margin['total_margin']; 
    }
}



$startOfWeek = date("Y-m-d", strtotime('monday this week'));
$endOfWeek = date("Y-m-d", strtotime('sunday this week'));

$sql = "SELECT SUM(TOTAL) AS weekly_sales
        FROM order_details
        WHERE DATE(ORDER_DATE) >= '$startOfWeek' AND DATE(ORDER_DATE) <= '$endOfWeek' AND b_id =$branchid";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$totalWeeklySales = $row['weekly_sales'];

if ($totalWeeklySales === null) {
    $totalWeeklySales = 0;
}

$startOfMonth = date("Y-m-01");
$endOfMonth = date("Y-m-t");

$sql = "SELECT SUM(TOTAL) AS monthly_sales
        FROM order_details
        WHERE DATE(ORDER_DATE) >= '$startOfMonth' AND DATE(ORDER_DATE) <= '$endOfMonth' AND b_id = $branchid";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$totalMonthlySales = $row['monthly_sales'];

if ($totalMonthlySales === null) {
    $totalMonthlySales = 0;
}


$startOfYear = date('Y-01-01');
$endOfYear = date('Y-12-31');

$sql = "SELECT SUM(TOTAL) AS yearly_sales
        FROM order_details
        WHERE DATE(ORDER_DATE) >= '$startOfYear' AND DATE(ORDER_DATE) <= '$endOfYear'AND b_id = $branchid";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$totalYearlySales = $row['yearly_sales'];

if ($totalYearlySales === null) {
    $totalYearlySales = 0;
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['role']) || !isset($_SESSION['branch'])) {
    header("location: ../all_login.php");
    exit; 
}

$sql = "SELECT COUNT(*) AS total_users FROM users where b_id = $branchid ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_count = $row['total_users'];
} else {
    $user_count = 0;
}
$sql = "SELECT COUNT(*) AS transaction FROM orders where b_id = $branchid ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $transaction = $row['transaction'];
} else {
    $transaction = 0;
}

$sql_sales = "SELECT SUM(TOTAL) AS daily_sales
              FROM order_details
              WHERE DATE(ORDER_DATE) = CURDATE() AND b_id = $branchid";

$result_sales = mysqli_query($conn, $sql_sales);

if (!$result_sales) {
    die("Error in SQL query for daily sales: " . mysqli_error($conn));
}

$row_sales = mysqli_fetch_assoc($result_sales);
$total_sales = $row_sales['daily_sales'] ??0;

class OrderManager {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getOrders($branchId) {
        $query = "SELECT id, OID, PID, PRICE, QTY, TOTAL, MARGIN, ORDER_DATE, b_id, type 
                  FROM order_details 
                  WHERE b_id = ? AND DATE(ORDER_DATE) = CURDATE()";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $branchId);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        $stmt->close();
        return $orders;
    }
}


$sql = "SELECT MONTHNAME(ORDER_DATE) as ORDER_MONTH, SUM(TOTAL) as TOTAL
        FROM order_details
        WHERE b_id = $branchid
        GROUP BY MONTH(ORDER_DATE)
        ORDER BY MONTH(ORDER_DATE) ASC";

$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [$row['ORDER_MONTH'], (float)$row['TOTAL']];
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap4.5.2.css">

    <title>CLoud Keepers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Baloo+Paaji+2:wght@400..800&display=swap" rel="stylesheet">
  <style>
        /* For Chrome, Edge, and Safari */
::-webkit-scrollbar {
    width: 1px; /* Set the scrollbar width */
}

::-webkit-scrollbar-thumb {
    background-color: #003366; 
}

::-webkit-scrollbar-track {
    background-color: transparent; /* Optionally, make the track transparent */
}

        body{
            padding: 0 0 0 70px !important;
        
        }
        #searchInpute{
            max-width: 100%;
            width: 450px;
            transform: translateY(5px);
     
        padding: 9px 10px; /* Add padding for spacing */
        font-size: 12px; /* Font size */
        border: 2px solid #0B192C; /* Border color and thickness */
        border-radius: 10px; /* Rounded corners */
        background-color: #f8f9fa; /* Light background color */
        color: #333; /* Dark text color */
        transition: border-color 0.3s; /* Transition for focus effect */
        }
        #searchInpute:focus {
        border-color: #007bff; /* Change border color on focus */
        outline: none; /* Remove default outline */
    }

        #searchInpute::placeholder {
        color: #999; /* Placeholder text color */
    }
        .graphBoxs {
            position: relative;
            width: 100%;
            padding: 10px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            grid-gap: 10px;
            min-height: 100% !important;
    height: 120px !important;
        }

        .graphBoxs>.box {
            position: relative;
            background: var(--white);
            padding: 10px;
            width: 100%;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            border-radius: 20px;
        }

        .cardBox {
            position: relative;
            width: 100%;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 30px;
        }

        .cardBox .cards {
            position: relative;
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
        }

        .cardBox .cards .numbers {
            position: relative;
            font-weight: 500;
            font-size: 2.5em;
            color: var(--blue);
            font-family: "Poppins", sans-serif;

  font-style: normal;
        }

        .cardBox .cards .cardName {
            color: var(--black2);
            font-size: 1.1em;
            margin-top: 5px;
        }
        .cardName{
            transform: translateY(5px);
        }

        .cardBox .cards .iconBox {
            font-size: 3.5em;
            color: var(--black2);
        }

        /* .cardBox .cards:hover {
            background: ;
        } */

      

        .cardBox .cards:hover .numbers,
        .cardBox .cards:hover .cardName {
            color: var(--white);
        }
        th,td{
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: 14px;
font-style: normal;
border: none !important;
gap: 0 !important;
margin: 0 !important;
        }
        .title-branch {
            transform: translateY(16px);
            font-size: 25px;
            font-family: "Inconsolata", monospace;
            font-optical-sizing: auto;
            font-weight: 100;
            font-style: normal;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-variation-settings:

                "wdth" 100;

        }
        .sidebarimage{
            width: 22px;
            height: 22px;
            margin-right: 20px;
        }   
        .carding1:hover{
            background-color: #2e41ee !important;
            color: #fff !important; 
        }
        .carding1{
            border: 1px solid #384bf6;
            color: #003366 !important; 
        }
        .card2:hover{
            background-color: #fe2de9 !important;
            color: #fff !important; 
            cursor: pointer;
        }
        .card2{
            border: 1px solid #fe2de9;
            color: #003366 !important; 
        }
        .card3:hover{
            background-color: #00ee8a !important;
            color: #fff !important; 
        }
        .card3{
            border: 1px solid #69efd7;
            color: #003366 !important; 
        }
        .card1:hover{
            background-color: #f9cf3a !important;
            color: #fff !important; 
            
        }
        .card1{
            border: 1px solid #fac818;
            color: #003366 !important; 
        }
      
        .carm:hover{
            background-color: #e31010 !important;
            color: #fff !important; 
        }
        .carm{
            border: 1px solid #f41515;
            color: #003366 !important; 
        }
        .cardc:hover{
            background-color: #07f0ff !important;
            color: #fff !important; 
        }
        .cardc{
            border: 1px solid #14bbc7;
            color: #003366 !important; 
        }
        .cardt:hover{
            background-color: #9b38b8 !important;
            color: #fff !important; 
        }
        .cardt{
            border: 1px solid #930abc;
            color: #003366 !important; 
        }
        .cardi:hover{
            background-color: #f4a631 !important;
            color: #fff !important; 
        }
        .cardi{
            border: 1px solid #f5c67e;
            color: #003366 !important; 
        }





   
  .svgimage{
    transform: translateY(15px);
  }
</style>
<style>

#container {
    height: 310px;
}
/* .highcharts-figure,
.highcharts-data-table table {
    min-width: 320px;
    max-width: 800px;
    margin: 1em auto;
}



.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: red;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

.highcharts-description {
    margin: 0.3rem 10px;
} */
/* Modal container, hidden by default */
/* Modal container, hidden by default */
/* Modal container, hidden by default */

.nav__name{
    margin-left: 10px;
  
}
a{
    text-decoration: none !important;
}
.table-responsive::-webkit-scrollbar {
    width: 2px; /* Scrollbar width */
}

.table-responsive::-webkit-scrollbar-track {
    background: transparent; /* Track color */
}

.table-responsive::-webkit-scrollbar-thumb {
    background-color: #003366; /* Scrollbar color */
    border-radius: 10px; /* Rounded corners for the scrollbar */
}
.modal-content{
    background-color: #F4F6FF;
    color: #003366;
}
#containersss {
    height:300px;
}

.highcharts-figure,
.highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
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
   .custom-input {
        max-width: 100%;
        width: 150px;
        padding: 9px 10px; /* Add padding for spacing */
        font-size: 12px; /* Font size */
        border: 2px solid #0B192C; /* Border color and thickness */
        border-radius: 10px; /* Rounded corners */
        background-color: #f8f9fa; /* Light background color */
        color: #333; /* Dark text color */
        transition: border-color 0.3s; /* Transition for focus effect */
        margin-right:10px;
    }

    .custom-input:focus {
        border-color: #007bff; /* Change border color on focus */
        outline: none; /* Remove default outline */
    }

    .custom-input::placeholder {
        color: #999; /* Placeholder text color */
    }
    .hidden {
    display: none;
}

.cards:hover #salesGif {
    display: block;
    position: absolute; /* Ensure the GIF is positioned correctly within the card */
    top: 15px; /* Adjust the position inside the card as needed */
    right: 10px; /* Adjust the position inside the card as needed */
    cursor: pointer;
}
.card3, .carding1, .card2,.cardt .cardc:hover {
  
    cursor: pointer;
}

.card2:hover #salesGifs {
    display: block;
    position: absolute; /* Ensure the GIF is positioned correctly within the card */
    top: 15px; /* Adjust the position inside the card as needed */
    right: 10px; /* Adjust the position inside the card as needed */
}
.card3:hover #salesGifss {
    display: block;
    position: absolute; /* Ensure the GIF is positioned correctly within the card */
    top: 15px; /* Adjust the position inside the card as needed */
    right: 10px; /* Adjust the position inside the card as needed */
}

.cardc:hover #salesGifsa {
    display: block;
    position: absolute; /* Ensure the GIF is positioned correctly within the card */
    top: 15px; /* Adjust the position inside the card as needed */
    right: 10px; /* Adjust the position inside the card as needed */
}

.cardt:hover #salesGifsas {
    display: block;
    position: absolute; /* Ensure the GIF is positioned correctly within the card */
    top: 7px; /* Adjust the position inside the card as needed */
    right: 10px; /* Adjust the position inside the card as needed */
}


</style>
  
    <link rel="stylesheet" href="side_bar.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
      <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/FRn5EbtxUffu+P2CgFqZVkvjgjpGF5e1xLq5jw" crossorigin="anonymous">

      <link rel="stylesheet" href="testing.css">
</head>

<body>
  
    <header class="header">
            <div class="header__container">
              

                <p href="#" class="header__logo" style="margin-top: 12px">DASHBOARD</p>

    
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
    
                            <a href="#" class="nav__link active">
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
            <div class="cardBox">
            <div class="cards carding1" data-bs-toggle="modal" data-bs-target="#salesModal">
    <div>
        <div class="numbers">
            <?php echo $total_sales; ?>
        </div>
        <div class="cardName">Today Sales</div>
    </div>
    <div class="iconBox">
        <img id="salesIcon" src="../images/dailyh.svg" class="svgimage" width="70" height="70" >
    </div>
    <img id="salesGif" src="../images/click-unscreen.gif" height="50px" width="50px" class="hidden">
</div>

                <div class="cards card2" data-bs-toggle="modal" data-bs-target="#weeklymodal">
                    <div>
                        <div class="numbers">
                            <?php echo $totalWeeklySales; ?>
                        </div>
                        <div class="cardName">This Week</div>
                    </div>
                    <div class="iconBox iconBoxs">
                    <img id="salesIcons" src="../images/weekly.svg" class="svgimage" width="70" height="70">
                    </div>
                    <img id="salesGifs" src="../images/click-unscreen.gif" height="50px" width="50px" class="hidden">
                </div>
                <div class="cards card3"  data-bs-toggle="modal" data-bs-target="#monthlymodal">
                    <div>
                        <div class="numbers">
                            <?php echo $totalMonthlySales; ?>
                        </div>
                        <div class="cardName">This Month</div>
                    </div>
                    <div class="iconBox">
                    <img id="salesIconss" src="../images/monthly.svg" class="svgimage" width="70" height="70">
                    </div>
                    <img id="salesGifss" src="../images/click-unscreen.gif" height="50px" width="50px" class="hidden">
                </div>
                <div class="cards card1">
                    <div>
                        <div class="numbers">
                            <?php echo $totalYearlySales; ?>
                        </div>
                        <div class="cardName">Total Sales</div>
                    </div>
                    <div class="iconBox">
                    <img id="salesIconsss" src="../images/total.svg" class="svgimage" width="70" height="70">
                    </div>
                </div>
                <div class="cards carm">
                    <div>
                        <div class="numbers">
                            <?php echo $margin; ?>
                        </div>
                        <div class="cardName">Margin</div>
                    </div>
                    <div class="iconBox">
                    <img id="salesIconssss" src="../images/margin.svg" class="svgimage" width="70" height="70" style="transform: translateY(-8px);">
                    </div>
                </div>
                <div class="cards cardc" data-bs-toggle="modal" data-bs-target="#manageSOTDModal">
    <div>
        <div class="numbers"><?php echo $user_count; ?></div>
        <div class="cardName">Manage SOTD</div>
    </div>
    <div class="iconBox">
        <img id="salesIconsssss" src="../images/stod.svg" class="svgimage" width="70" height="70" style="transform: translateY(-2px);">
   
    </div>
    <img id="salesGifsa" src="../images/click-unscreen.gif" height="50px" width="50px" class="hidden">

</div>  
                <div class="cards cardt  "  data-bs-toggle="modal" data-bs-target="#alltopSalesModal">
                    <div>
                        <div class="numbers"><?php  echo$transaction?></div>
                        <div class="cardName">Fast Moving Products</div>
                    </div>
                    <div class="iconBox ">
                    <img id="salesIconssssss" src="../images/transaction.svg" class="svgimage" width="70" height="70"style="transform: translateY(-8px);">

                    </div>

                    <img id="salesGifsas" src="../images/click-unscreen.gif" height="50px" width="50px" class="hidden">

                </div>
                <div class="cards cardi">
                    <div>
                        <div class="numbers">
                            <?php echo $total_items; ?>
                        </div>
                        <div class="cardName">Items</div>
                    </div>
                    <div class="iconBox">
                    <img id="salesIconsssssss" src="../images/item.svg" class="svgimage" width="70" height="70" style="transform: translateY(-8px);">
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="graphBox" style="transform:translateY(-15px)">
                <!-- <div class="box">
                    <canvas id="earning"></canvas>
                </div> -->
                 <div class="box">
    <div id="container"></div>
 </div> 
                <div class="box">
                    <!-- <canvas id="myChart"></canvas> -->
                    <div id="containersss"></div>
                </div>
                

                <!-- <div class="box">
               <canvas id="yearly"></canvas>
         
        </div> -->

        <!-- <figure class="highcharts-figure">
    <div id="containersss"></div>
</figure> -->
            </div>
          
            <div class="modal fade" id="manageSOTDModal" tabindex="-1" aria-labelledby="manageSOTDModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px;padding :10px">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border:none;background: #F4F6FF;color:#091057; font-weight: 900;"><i class="fa-solid fa-x"></i></button>
                <h5 class="modal-title" id="manageSOTDModalLabel" style="margin-bottom: 20px; color:#091057">Manage Users</h5>
                <p style="color:#091057;font-size:12px;text-transform:uppercase">reset or enable again staff to use the Pos</p>
                
                <?php
                $query = "SELECT users.id, users.user_name, users.sod,users.status, users.nsod,branch.branch FROM users inner join branch on branch.id = users.b_id"; // Adjust the query as needed
                $result = $conn->query($query);

                // Check if any users were found
                $users = [];
                if ($result->num_rows > 0) {
                    // Fetch all user data into an array
                    while ($row = $result->fetch_assoc()) {
                        $users[] = $row;
                    }
                }
              
                ?>
   <div class="mb-3">
            <input type="text" class="form-control" id="searchInputssss" placeholder="Search Categories..." onkeyup="filterTableSS()">
          
        </div>
                <div class="table-responsive" style="max-height:100%px; height:300px;  overflow-y: auto;">
             

                    <table class="table table-striped table-bordered">
                        <thead class="sticky-top " style="background-color: #BCF2F6;color:#091057">
                            <tr>
                            <th>Branch</th>
                                <th>User Name</th>
                                <th>SOD</th>
                                <th>NSOD</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="flavorTableBody">
                        <?php foreach ($users as $user): ?>
                <tr>
                <td style="text-transform:uppercase"><?php echo htmlspecialchars($user['branch']); ?></td>
                    <td style="text-transform:uppercase"><?php echo htmlspecialchars($user['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['sod']); ?></td>
                    <td><?php echo htmlspecialchars($user['nsod']); ?></td>
                    <td class="text-center">
                        <button 
                            class="btn btn-primary btn-sm delete-item" 
                            data-itemid="<?= $user['id'] ?>" 
                            style="color: #091057; background: #BCF2F6; font-weight:600"
                            <?php if ($user['status'] == 0) echo 'disabled'; ?>>
                            Reset
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"    style="color: #091057; background: #BCF2F6; font-weight:600" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900;transform:translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>

                <?php
// Assuming $branchId is defined and holds the branch ID
$querys = "SELECT flavor.flavor, order_details.QTY, order_details.PRICE, order_details.TOTAL
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE order_details.b_id = ? AND DATE(order_details.ORDER_DATE) = CURDATE()";

// Prepare and execute the statement
$stmts = $conn->prepare($querys);

// Check for preparation errors
if (!$stmts) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters
$stmts->bind_param("i", $branchid); // Assuming b_id is an integer

// Execute the statement
if (!$stmts->execute()) {
    die("Execution failed: " . $stmts->error);
}

// Get the result set from the prepared statement
$results = $stmts->get_result();

// Initialize orders array
$orders = [];
if ($results && $results->num_rows > 0) {
    // Fetch all order data into the orders array
    while ($rows = $results->fetch_assoc()) {
        $orders[] = $rows;
    }
}


?>
  <div class="mb-3 d-flex ">
            <input type="text" class="form-control" style="margin-right: 10px;" id="searchInputsssss" placeholder="Search Categories..." onkeyup="filterTableSSs()">
    <button id="topSalesBtn" title="Top Sales" class="btn" style="margin-right:20px;" data-bs-toggle="modal" data-bs-target="#topSalesModal">
                        <i class="fa-solid fa-fire" style="color: red;"></i>   </button>        </div>
<div class="table-responsive" style="max-height: 100%;height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tabledaily">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found for today.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['QTY']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['TOTAL']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="topSalesModal" tabindex="-1" aria-labelledby="topSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900; transform: translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>

       
             <h5 class="modal-title mb-4">TOP SELLING ITEM</h5>
            

              
             <?php
// Assuming $branchId is defined and holds the branch ID

// Step 1: Retrieve the daily value from the top_sales_ranger table
$queryDaily = "SELECT daily FROM top_sales_ranger LIMIT 1"; // Assuming there's only one row
$dailyResult = $conn->query($queryDaily);

// Initialize daily variable
$daily = 1; // Default value in case of failure
if ($dailyResult && $dailyResult->num_rows > 0) {
    $dailyRow = $dailyResult->fetch_assoc();
    $daily = (int)$dailyRow['daily']; // Convert to integer
}

// Step 2: Use the daily value in the main query with LIMIT
$querys = "SELECT flavor.flavor, SUM(order_details.QTY) AS total_qty, 
                  order_details.PRICE, SUM(order_details.TOTAL) AS total_amount
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE order_details.b_id = ? AND DATE(order_details.ORDER_DATE) = CURDATE()
           GROUP BY order_details.PID, flavor.flavor, order_details.PRICE
           ORDER BY total_qty DESC
           LIMIT ?"; // Use a placeholder for the limit

// Prepare and execute the statement
$stmts = $conn->prepare($querys);

// Check for preparation errors
if (!$stmts) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters (assuming b_id is an integer and daily is an integer)
$stmts->bind_param("ii", $branchid, $daily); 

// Execute the statement
if (!$stmts->execute()) {
    die("Execution failed: " . $stmts->error);
}

// Get the result set from the prepared statement
$results = $stmts->get_result();

// Initialize orders array
$orders = [];
if ($results && $results->num_rows > 0) {
    // Fetch all order data into the orders array
    while ($rows = $results->fetch_assoc()) {
        $orders[] = $rows;
    }
}
?>

<div class="mb-3 d-flex">
    <input type="text" class="form-control" style="margin-right: 60px;" id="searchInpute" placeholder="Search Categories..." onkeyup="filterTableSa()">
    <div style="padding: 5px 11px; font-size: 20px;color: #0B192C; border-radius: 10px;margin-right: 10px">
    <?php echo $daily; ?>
</div>
    
  <form action="" method="post">
   <div class="d-flex">
   <input type="text" name="numberofdailyranger"class="custom-input" placeholder="Enter text here..." />
   <button type="submit" name="updatetherangedaily" class="btn" style="background-color: #003366; color: white; padding: 5px 9px; border-radius: 10px; border: none; display: flex; align-items: center; justify-content: center;">
    <i class="fa-regular fa-pen-to-square"></i>
</button>
   </div>
  </form>
</div>

<div class="table-responsive" style="max-height: 100%; height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tabledailys">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found for today.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_qty']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="alltopSalesModal" tabindex="-1" aria-labelledby="alltopSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900; transform: translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>

       
             <h5 class="modal-title mb-4">OVERALL TOP SELLING ITEM</h5>
            

              
             <?php
// Assuming $branchId is defined and holds the branch ID

// Step 1: Retrieve the daily value from the top_sales_ranger table
$queryDaily = "SELECT overall FROM top_sales_ranger LIMIT 1"; // Assuming there's only one row
$dailyResult = $conn->query($queryDaily);

// Initialize daily variable
$daily = 1; // Default value in case of failure
if ($dailyResult && $dailyResult->num_rows > 0) {
    $dailyRow = $dailyResult->fetch_assoc();
    $daily = (int)$dailyRow['overall']; // Convert to integer
}

// Step 2: Use the daily value in the main query with LIMIT
$querys = "SELECT flavor.flavor, SUM(order_details.QTY) AS total_qty, 
                  order_details.PRICE, SUM(order_details.TOTAL) AS total_amount
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE order_details.b_id = ? 
           GROUP BY order_details.PID, flavor.flavor, order_details.PRICE
           ORDER BY total_qty DESC
           LIMIT ?"; 

// Prepare and execute the statement
$stmts = $conn->prepare($querys);

// Check for preparation errors
if (!$stmts) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters (assuming b_id is an integer and daily is an integer)
$stmts->bind_param("ii", $branchid, $daily); 

// Execute the statement
if (!$stmts->execute()) {
    die("Execution failed: " . $stmts->error);
}

// Get the result set from the prepared statement
$results = $stmts->get_result();

// Initialize orders array
$orders = [];
if ($results && $results->num_rows > 0) {
    // Fetch all order data into the orders array
    while ($rows = $results->fetch_assoc()) {
        $orders[] = $rows;
    }
}
?>

<div class="mb-3 d-flex">
    <input type="text" class="form-control" style="margin-right: 60px;" id="searchInpute" placeholder="Search Categories..." onkeyup="filterTableSa()">
    <div style="padding: 5px 11px; font-size: 20px;color: #0B192C; border-radius: 10px;margin-right: 10px">
    <?php echo $daily; ?>
</div>
    
  <form action="" method="post">
   <div class="d-flex">
   <input type="text" name="numberofoverallranger"class="custom-input" placeholder="Enter text here..." />
   <button type="submit" name="overall" class="btn" style="background-color: #003366; color: white; padding: 5px 9px; border-radius: 10px; border: none; display: flex; align-items: center; justify-content: center;">
    <i class="fa-regular fa-pen-to-square"></i>
</button>
   </div>
  </form>
</div>

<div class="table-responsive" style="max-height: 100%; height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tabledailys">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found .</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_qty']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>






<div class="modal fade" id="weeklymodal" tabindex="-1" aria-labelledby="weeklymodalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900;transform:translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>

                <?php
// Assuming $branchId is defined and holds the branch ID

$startOfWeek = date("Y-m-d", strtotime('monday this week'));
$endOfWeek = date("Y-m-d", strtotime('sunday this week'));
$querys = "SELECT flavor.flavor, order_details.QTY, order_details.PRICE, order_details.TOTAL
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE order_details.b_id = ? AND  DATE(order_details.ORDER_DATE) >= '$startOfWeek' AND DATE(order_details.ORDER_DATE) <= '$endOfWeek'";

// Prepare and execute the statement
    $stmts = $conn->prepare($querys);

    // Check for preparation errors
    if (!$stmts) {
        die("Preparation failed: " . $conn->error);
    }

    // Bind parameters
    $stmts->bind_param("i", $branchid); // Assuming b_id is an integer

    // Execute the statement
    if (!$stmts->execute()) {
        die("Execution failed: " . $stmts->error);
    }

    // Get the result set from the prepared statement
    $results = $stmts->get_result();

    // Initialize orders array
    $orders = [];
    if ($results && $results->num_rows > 0) {
        // Fetch all order data into the orders array
        while ($rows = $results->fetch_assoc()) {
            $orders[] = $rows;
        }
    }


?>
  <div class="mb-3 d-flex">
            <input type="text" class="form-control" id="searchInputssssss" style="margin-right:10px;"  placeholder="Search Categories..." onkeyup="filterTableSSss()">
            <button id="topSalesBtn" title="Top Sales" class="btn" style="margin-right:20px;" data-bs-toggle="modal" data-bs-target="#wekklytopSalesModal">
                        <i class="fa-solid fa-fire" style="color: red;"></i>   </button> 
        </div>
<div class="table-responsive" style="max-height: 100%;height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="weeklytable">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found for this Week.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['QTY']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['TOTAL']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="wekklytopSalesModal" tabindex="-1" aria-labelledby="topSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900; transform: translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>
             <h5 class="modal-title mb-4">THIS WEEK TOP SELLING ITEM</h5>             
             <?php
$startOfWeek = date("Y-m-d", strtotime('monday this week'));
$endOfWeek = date("Y-m-d", strtotime('sunday this week'));

$queryDaily = "SELECT weekly FROM top_sales_ranger LIMIT 1"; 
$dailyResult = $conn->query($queryDaily);

// Initialize daily variable
$daily = 1; // Default value in case of failure
if ($dailyResult && $dailyResult->num_rows > 0) {
    $dailyRow = $dailyResult->fetch_assoc();
    $daily = (int)$dailyRow['weekly']; // Convert to integer
}

// Step 3: Use the daily value in the main query with LIMIT, filtering by the week
$querys = "SELECT flavor.flavor, SUM(order_details.QTY) AS total_qty, 
                  order_details.PRICE, SUM(order_details.TOTAL) AS total_amount
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE order_details.b_id = ? AND DATE(order_details.ORDER_DATE) >= ? AND DATE(order_details.ORDER_DATE) <= ?
           GROUP BY order_details.PID, flavor.flavor, order_details.PRICE
           ORDER BY total_qty DESC
           LIMIT ?"; // Use a placeholder for the limit

// Prepare and execute the statement
$stmts = $conn->prepare($querys);

// Check for preparation errors
if (!$stmts) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters (assuming b_id is an integer and daily is an integer)
$stmts->bind_param("issi", $branchid, $startOfWeek, $endOfWeek, $daily); 

// Execute the statement
if (!$stmts->execute()) {
    die("Execution failed: " . $stmts->error);
}

// Get the result set from the prepared statement
$results = $stmts->get_result();

// Initialize orders array
$orders = [];
if ($results && $results->num_rows > 0) {
    // Fetch all order data into the orders array
    while ($rows = $results->fetch_assoc()) {
        $orders[] = $rows;
    }
}
?>


<div class="mb-3 d-flex">
    <input type="text" class="form-control" style="margin-right: 60px;" id="searchInpute" placeholder="Search Categories..." onkeyup="filterTableSa()">
    <div style="padding: 5px 11px; font-size: 20px;color: #0B192C; border-radius: 10px;margin-right: 10px">
    <?php echo $daily; ?>
</div>
    
  <form action="" method="post">
   <div class="d-flex">
   <input type="text" name="numberofdailyranger"class="custom-input" placeholder="Enter text here..." />
   <button type="submit" name="updatetherangemonthly" class="btn" style="background-color: #003366; color: white; padding: 5px 9px; border-radius: 10px; border: none; display: flex; align-items: center; justify-content: center;">
    <i class="fa-regular fa-pen-to-square"></i>
</button>
   </div>
  </form>
</div>

<div class="table-responsive" style="max-height: 100%; height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tabledailys">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found for Week.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_qty']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="monthlymodal" tabindex="-1" aria-labelledby="monthlymodalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900;transform:translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>

                <?php


$startOfMonth = date("Y-m-01");
$endOfMonth = date("Y-m-t");

$querys = "SELECT flavor.flavor, order_details.QTY, order_details.PRICE, order_details.TOTAL
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE DATE(ORDER_DATE) >= ? AND DATE(ORDER_DATE) <= ? AND order_details.b_id = ?";

// Prepare the statement
$stmts = $conn->prepare($querys);

// Check for preparation errors
if (!$stmts) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters
$stmts->bind_param("ssi", $startOfMonth, $endOfMonth, $branchid); // Assuming b_id is an integer

// Execute the statement
if (!$stmts->execute()) {
    die("Execution failed: " . $stmts->error);
}

// Get the result set from the prepared statement
$results = $stmts->get_result();

// Initialize orders array
$orders = [];
if ($results && $results->num_rows > 0) {
    // Fetch all order data into the orders array
    while ($rows = $results->fetch_assoc()) {
        $orders[] = $rows;
    }
}

// Close the statement
$stmts->close();
?>

  <div class="mb-3 d-flex">
            <input type="text" class="form-control" id="searchInputsssssss" style="margin-right:10px;" placeholder="Search Categories..." onkeyup="filterTableSSsss()">
            <button id="topSalesBtn" title="Top Sales" class="btn" style="margin-right:20px;" data-bs-toggle="modal" data-bs-target="#monthlytopSalesModal">
                        <i class="fa-solid fa-fire" style="color: red;"></i>   </button> 
        </div>
<div class="table-responsive" style="max-height: 100%; height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="monthlytable">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found for Month.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['QTY']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['TOTAL']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="monthlytopSalesModal" tabindex="-1" aria-labelledby="topSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 30px; padding: 10px;">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="float: right; border: none; background: #F4F6FF; color: #091057; font-weight: 900; transform: translateY(-10px);">
                    <i class="fa-solid fa-x"></i>
                </button>
             <h5 class="modal-title mb-4">THIS MONTH TOP SELLING ITEM</h5>             
             <?php
$startOfMonth = date("Y-m-01");
$endOfMonth = date("Y-m-t");

$queryDaily = "SELECT monthly FROM top_sales_ranger LIMIT 1"; 
$dailyResult = $conn->query($queryDaily);

// Initialize daily variable
$daily = 1; // Default value in case of failure
if ($dailyResult && $dailyResult->num_rows > 0) {
    $dailyRow = $dailyResult->fetch_assoc();
    $daily = (int)$dailyRow['monthly']; // Convert to integer
}

// Step 3: Use the daily value in the main query with LIMIT, filtering by the week
$querys = "SELECT flavor.flavor, SUM(order_details.QTY) AS total_qty, 
                  order_details.PRICE, SUM(order_details.TOTAL) AS total_amount
           FROM order_details 
           INNER JOIN flavor ON flavor.id = order_details.PID
           WHERE order_details.b_id = ? AND DATE(order_details.ORDER_DATE) >= ? AND DATE(order_details.ORDER_DATE) <= ?
           GROUP BY order_details.PID, flavor.flavor, order_details.PRICE
           ORDER BY total_qty DESC
           LIMIT ?"; // Use a placeholder for the limit

// Prepare and execute the statement
$stmts = $conn->prepare($querys);

// Check for preparation errors
if (!$stmts) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters (assuming b_id is an integer and daily is an integer)
$stmts->bind_param("issi", $branchid, $startOfMonth, $endOfMonth, $daily); 

// Execute the statement
if (!$stmts->execute()) {
    die("Execution failed: " . $stmts->error);
}

// Get the result set from the prepared statement
$results = $stmts->get_result();

// Initialize orders array
$orders = [];
if ($results && $results->num_rows > 0) {
    // Fetch all order data into the orders array
    while ($rows = $results->fetch_assoc()) {
        $orders[] = $rows;
    }
}
?>


<div class="mb-3 d-flex">
    <input type="text" class="form-control" style="margin-right: 60px;" id="searchInpute" placeholder="Search Categories..." onkeyup="filterTableSa()">
    <div style="padding: 5px 11px; font-size: 20px;color: #0B192C; border-radius: 10px;margin-right: 10px">
    <?php echo $daily; ?>
</div>
    
  <form action="" method="post">
   <div class="d-flex">
   <input type="text" name="numberofdailyranger"class="custom-input" placeholder="Enter text here..." />
   <button type="submit" name="updatetherangeweekly" class="btn" style="background-color: #003366; color: white; padding: 5px 9px; border-radius: 10px; border: none; display: flex; align-items: center; justify-content: center;">
    <i class="fa-regular fa-pen-to-square"></i>
</button>
   </div>
  </form>
</div>

<div class="table-responsive" style="max-height: 100%; height: 300px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
        <thead class="sticky-top" style="background-color: #BCF2F6; color: #091057;">
            <tr>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tabledailys">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="4" class="text-center">No orders found for MOnth.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flavor']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_qty']); ?></td>
                        <td><?php echo htmlspecialchars($order['PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="color: #091057; background: #BCF2F6; font-weight: 600;">Close</button>
            </div>
        </div>
    </div>
</div>


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
document.addEventListener('DOMContentLoaded', function() {
    const carw = document.querySelector('.cards'); // Use carw instead of card
    const salesGif = document.getElementById('salesGif');
    let gifTimeout;

    carw.addEventListener('mouseenter', function() {
        salesGif.style.display = 'block'; // Show the GIF
        clearTimeout(gifTimeout); // Clear any existing timeout

        // Set a timeout to hide the GIF after 2 seconds
        gifTimeout = setTimeout(() => {
            salesGif.style.display = 'none'; // Hide the GIF after 2 seconds
        }, 2500);
    });

    carw.addEventListener('mouseleave', function() {
        // Hide the GIF immediately when mouse leaves
        salesGif.style.display = 'none';
        clearTimeout(gifTimeout); // Clear the timeout if mouse leaves before 2 seconds
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const carww = document.querySelector('.card2'); // Use carww instead of card
    const salesGif = document.getElementById('salesGifs');
    let gifTimeout;

    carww.addEventListener('mouseenter', function() {
        salesGif.style.display = 'block'; // Show the GIF
        clearTimeout(gifTimeout); // Clear any existing timeout

        // Set a timeout to hide the GIF after 2 seconds
        gifTimeout = setTimeout(() => {
            salesGif.style.display = 'none'; // Hide the GIF after 2 seconds
        }, 2500);
    });

    carww.addEventListener('mouseleave', function() {
        // Hide the GIF immediately when mouse leaves
        salesGif.style.display = 'none';
        clearTimeout(gifTimeout); // Clear the timeout if mouse leaves before 2 seconds
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const carwww = document.querySelector('.card3'); // Use carwww instead of card
    const salesGif = document.getElementById('salesGifss');
    let gifTimeout;

    carwww.addEventListener('mouseenter', function() {
        salesGif.style.display = 'block'; // Show the GIF
        clearTimeout(gifTimeout); // Clear any existing timeout

        // Set a timeout to hide the GIF after 2 seconds
        gifTimeout = setTimeout(() => {
            salesGif.style.display = 'none'; // Hide the GIF after 2 seconds
        }, 2500);
    });

    carwww.addEventListener('mouseleave', function() {
        // Hide the GIF immediately when mouse leaves
        salesGif.style.display = 'none';
        clearTimeout(gifTimeout); // Clear the timeout if mouse leaves before 2 seconds
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const carwww = document.querySelector('.cardc'); // Use carwww instead of card
    const salesGif = document.getElementById('salesGifsa');
    let gifTimeout;

    carwww.addEventListener('mouseenter', function() {
        salesGif.style.display = 'block'; // Show the GIF
        clearTimeout(gifTimeout); // Clear any existing timeout

        // Set a timeout to hide the GIF after 2 seconds
        gifTimeout = setTimeout(() => {
            salesGif.style.display = 'none'; // Hide the GIF after 2 seconds
        }, 2500);
    });

    carwww.addEventListener('mouseleave', function() {
        // Hide the GIF immediately when mouse leaves
        salesGif.style.display = 'none';
        clearTimeout(gifTimeout); // Clear the timeout if mouse leaves before 2 seconds
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const carwwww = document.querySelector('.cardt'); // Use carwww instead of card
    const salesGiff = document.getElementById('salesGifsas');
    let gifTimeout;

    carwwww.addEventListener('mouseenter', function() {
        salesGiff.style.display = 'block'; // Show the GIF
        clearTimeout(); // Clear any existing timeout
gifTimeout
        // Set a timeout to hide the GIF after 2 seconds
        gifTimeout = setTimeout(() => {
            salesGiff.style.display = 'none'; // Hide the GIF after 2 seconds
        }, 2500);
    });

    carwwww.addEventListener('mouseleave', function() {
        // Hide the GIF immediately when mouse leaves
        salesGiff.style.display = 'none';
        clearTimeout(gifTimeout); // Clear the timeout if mouse leaves before 2 seconds
    });
});

 </script>
<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['updateed'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.success('<?php echo $_SESSION['updateed']; ?>');
            
            // Show the modal
            $('#topSalesModal').modal('show');

            <?php unset($_SESSION['updateed']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>


<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['updateedss'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.success('<?php echo $_SESSION['updateedss']; ?>');
            
            // Show the modal
            $('#alltopSalesModal').modal('show');

            <?php unset($_SESSION['updateedss']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['updateeds'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.success('<?php echo $_SESSION['updateeds']; ?>');
            
            // Show the modal
            $('#wekklytopSalesModal').modal('show');

            <?php unset($_SESSION['updateeds']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['updateedssaw'])): ?>
            // Show alert using Alertify.js
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.success('<?php echo $_SESSION['updateedssaw']; ?>');
            
            // Show the modal
            $('#monthlytopSalesModal').modal('show');

            <?php unset($_SESSION['updateedssaw']); // Clear the session variable ?>
        <?php endif; ?>
    });
</script>



<script>
// Pass the PHP data to JavaScript
const chartData = <?php echo json_encode($data); ?>;
</script>

<script>
Highcharts.chart('containersss', {
    chart: {
        type: 'column',
        options3d: {
            enabled: true,        // Enable 3D effect
            alpha: 10,            // Tilt chart
            beta: 25,             // Rotate chart
            depth: 50,            // Depth of 3D chart
            viewDistance: 25      // Distance of view
        }
    },
    plotOptions: {
        column: {
            depth: 50,            // Depth for each column
            cylinder: {           // Apply cylinder shape
                colorByPoint: true
            }
        }
    },
    title: {
        text: 'Monthly'
    },
    xAxis: {
        categories: chartData.map(item => item[0]),
        crosshair: true,
        labels: {
            style: {
                fontSize: '14px'
            }
        },
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Total Sales'
        }
    },
    tooltip: {
        valueSuffix: ' PHP'
    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Sales',
        data: chartData.map(item => item[1]),
        showInLegend: false
    }]
});
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
    function filterTableSSs() {
        const input = document.getElementById('searchInputsssss');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tabledaily');
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
    function filterTableSSss() {
        const input = document.getElementById('searchInputssssss');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('weeklytable');
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
    function filterTableSSsss() {
        const input = document.getElementById('searchInputsssssss');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('monthlytable');
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
document.addEventListener("DOMContentLoaded", function() {
    // Add event listener for all delete-item buttons
    document.querySelectorAll('.delete-item').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const itemid = this.getAttribute('data-itemid');
            
            if (confirm('Are you sure you want to reset this user\'s status?')) {
                // Send AJAX request to reset the user status
                fetch('reset_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'itemid=' + itemid
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        // Reload the page or update the UI to reflect the reset
                        location.reload();
                    } else {
                        alert('Failed to reset user status');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
</script>
<script>
Highcharts.chart('container', {
    chart: {
        type: 'area'
    },
    accessibility: {
        description: 'A chart representing daily sales data over the current week.'
    },
    title: {
        text: 'Daily'
    },
   
    xAxis: {
        categories: <?php echo $json_days; ?>, // Daily categories
        title: {
            text: 'Date'
        }
    },
    yAxis: {
        title: {
            text: 'Sales Total'
        },
        labels: {
            formatter: function () {
                return this.value.toFixed(2); // Format the number to two decimal places
            }
        }
    },
    credits: {
        enabled: false
    },
    tooltip: {
        pointFormat: '{series.name} had total sales of <b>{point.y:,.2f}</b> on {point.x}'
    },
    series: [{
        name: 'Total Sales',
        color: '#003366', // Set the color to red
        data: <?php echo $json_amounts; ?> // Sales data
    }]
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


        let list = document.querySelectorAll('.navigation li')

        function activeLink() {
            list.forEach((item) =>
                item.classList.remove('hovered'))
            this.classList.add('hovered')
        }
        list.forEach((item) =>
            item.addEventListener('mouseover', activeLink))
    </script>

    
    <script>
        // const labels_daily = <?php echo json_encode($day) ?>;
        // const data_daily = {
        //     labels: labels_daily,
        //     datasets: [{
        //         label: 'Daily Sales',
        //         data: <?php echo json_encode($amount_daily) ?>,
        //         backgroundColor: 'rgba(54, 162, 235, 0.2)',
        //         borderColor: 'rgb(54, 162, 235)',
        //         borderWidth: 1
        //     }]
        // };

        // const config_daily = {
        //     type: 'bar',
        //     data: data_daily,
        //     options: {
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     },
        // };

        // var dailyChart = new Chart(
        //     document.getElementById('earning'),
        //     config_daily
        // );


        const labels_monthly = <?php echo json_encode($month) ?>;
        const data_monthly = {
            labels: labels_monthly,
            datasets: [{
                label: 'Monthly Sales',
                data: <?php echo json_encode($amount_monthly) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        };

        const config_monthly = {
            type: 'line',
            data: data_monthly,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };

        var monthlyChart = new Chart(
            document.getElementById('myChart'),
            config_monthly
        );


    //yearly
    </script>
    <script>
        // PHP code to fetch yearly sales data
        const labels_yearly = <?php echo json_encode(array_keys($yearly_sales)) ?>;
        const data_yearly = {
            labels: labels_yearly,
            datasets: [{
                label: 'Yearly Sales',
                data: <?php echo json_encode(array_values($yearly_sales)) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        };

        const config_yearly = {
            type: 'line',
            data: data_yearly,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };

        var yearlyChart = new Chart(
            document.getElementById('yearly'),
            config_yearly
        );
    </script>



<script>
    const card = document.querySelector('.carding1');
    const salesIcon = document.getElementById('salesIcon');

    card.addEventListener('mouseover', () => {
        salesIcon.src = '../images/daily.svg'; // Change the SVG on hover
    });

    card.addEventListener('mouseout', () => {
        salesIcon.src = '../images/dailyh.svg'; // Change the SVG on hover
    });
</script>


<script>
    const cards = document.querySelector('.card2');
    const salesIcons = document.getElementById('salesIcons');

    cards.addEventListener('mouseover', () => {
        salesIcons.src = '../images/weeklyh.svg'; // Change the SVG on hover
    });

    cards.addEventListener('mouseout', () => {
        salesIcons.src = '../images/weekly.svg'; // Change the SVG on hover
    });
</script>
<script>
    const cardss = document.querySelector('.card3');
    const salesIconss = document.getElementById('salesIconss');

    cardss.addEventListener('mouseover', () => {
        salesIconss.src = '../images/monthlyh.svg'; // Change the SVG on hover
    });

    cardss.addEventListener('mouseout', () => {
        salesIconss.src = '../images/monthly.svg'; // Change the SVG on hover
    });
</script>
<script>
    const cardsss = document.querySelector('.card1');
    const salesIconsss = document.getElementById('salesIconsss');

    cardsss.addEventListener('mouseover', () => {
        salesIconsss.src = '../images/totalh.svg'; // Change the SVG on hover
    });

    cardsss.addEventListener('mouseout', () => {
        salesIconsss.src = '../images/totalh.svg'; // Change the SVG on hover
    });
</script>
<script>
    const cardssss = document.querySelector('.carm');
    const salesIconssss = document.getElementById('salesIconssss');

    cardssss.addEventListener('mouseover', () => {
        salesIconssss.src = '../images/marginh.svg'; // Change the SVG on hover
    });

    cardssss.addEventListener('mouseout', () => {
        salesIconssss.src = '../images/margin.svg'; // Change the SVG on hover
    });
</script>
<script>
    const cardsssss = document.querySelector('.cardc');
    const salesIconsssss = document.getElementById('salesIconsssss');

    cardsssss.addEventListener('mouseover', () => {
        salesIconsssss.src = '../images/manageh.svg'; // Change the SVG on hover
    });

    cardsssss.addEventListener('mouseout', () => {
        salesIconsssss.src = '../images/stod.svg'; // Change the SVG on hover
    });
</script>
<script>
    const cardssssss = document.querySelector('.cardi');
    const salesIconsssssss = document.getElementById('salesIconsssssss');

    cardssssss.addEventListener('mouseover', () => {
        salesIconsssssss.src = '../images/itemh.svg'; // Change the SVG on hover
    });

    cardssssss.addEventListener('mouseout', () => {
        salesIconsssssss.src = '../images/item.svg'; // Change the SVG on hover
    });
</script>
<script>
        function confirmLogout() {
            // Show confirmation dialog
            var result = confirm("Are you sure you want to logout?");
            if (result) {
                // If user confirms, redirect to logout page
                window.location.href = "../logout.php";
            }
            // If user cancels, do nothing
        }
    </script>
<script>
// Automatically toggle modals
const salesModal = new bootstrap.Modal(document.getElementById('salesModal'));
const topSalesModal = new bootstrap.Modal(document.getElementById('topSalesModal'));

// When Top Sales Modal opens, close Sales Modal
document.getElementById('topSalesModal').addEventListener('shown.bs.modal', function () {
    salesModal.hide();
});

// When Top Sales Modal closes, open Sales Modal
document.getElementById('topSalesModal').addEventListener('hidden.bs.modal', function () {
    salesModal.show();
});
</script>
<script>
// Automatically toggle modals
const weeklymodal = new bootstrap.Modal(document.getElementById('weeklymodal'));
const wekklytopSalesModal = new bootstrap.Modal(document.getElementById('wekklytopSalesModal'));

// When Top Sales Modal opens, close Sales Modal
document.getElementById('wekklytopSalesModal').addEventListener('shown.bs.modal', function () {
    weeklymodal.hide();
});
document.getElementById('wekklytopSalesModal').addEventListener('hidden.bs.modal', function () {
    weeklymodal.show();
});
</script>
<script>
const monthlymodal = new bootstrap.Modal(document.getElementById('monthlymodal'));
const monthlytopSalesModal = new bootstrap.Modal(document.getElementById('monthlytopSalesModal'));

document.getElementById('monthlytopSalesModal').addEventListener('shown.bs.modal', function () {
    monthlymodal.hide();
});

document.getElementById('monthlytopSalesModal').addEventListener('hidden.bs.modal', function () {
    monthlymodal.show();
});
</script>
<script>
    document.getElementById('confirmLogout').onclick = function() {
        // Redirect to logout page on confirmation
        window.location.href = "../logout.php";
    };
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleButton = document.getElementById("header-toggle");
        const navbar = document.getElementById("navbar");

        // Toggle the sidebar
        toggleButton.addEventListener("click", () => {
            navbar.classList.toggle("show");
        });
    });
</script>


</body>

</html>