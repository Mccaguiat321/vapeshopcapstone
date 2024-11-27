<?php
include "database/db_conn.php";
?>
<!DOCTYPE html>
<html lnag="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOUD KEEPERS </title>
    <link rel="stylesheet" href="css/style111.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="preload" href="./assets/images/hero-banner.png" as="image">
    <style>
    .imgmo {
        position: absolute;
        top: 22%;
        right: 7%;
        width: 520px;
        height: 520px;
    }

    .imgmo {
        animation: bounce2 3s ease infinite;

    }
    @keyframes bounce2 {s

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-30px);
        }

        60% {
            transform: translateY(-15px);
        }
    }
    </style>
</head>

<body id="top">
    <header class="header" data-header>
        <div class="container">

            <div class="overlay" data-overlay></div>

            <a href="#" class="logo">
                <img src="images/log.png" width="180" height="50" alt="Footcap logo">
            </a>

            <button class="nav-open-btn" data-nav-open-btn aria-label="Open Menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <nav class="navbar" data-navbar>
                <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
                    <ion-icon name="close-outline"></ion-icon>
                </button>

                <a href="#" class="logo">
                    <img src="images/bg.jpeg" width="190" height="50" alt="Footcap logo">
                </a>

                <ul class="navbar-list ml-auto">
                 
                    <li class="navbar-item">
                        <a class="navbar-link" href="index.php">Home</a>
                    </li>
                    <li class="navbar-item">
                        <a class="navbar-link" href="customer/restaurants.php">Restaurants</a>
                    </li>

                    <?php
                    session_start();
                    if (empty($_SESSION["user_id"])) {
                        echo '<li class="navbar-item">';
                        echo '<a href="customer.php" class="nav-action-btn" style="transform:translateX(450px);">';
                        echo '<ion-icon name="person-outline" aria-hidden="true" style="transform:translateY(-2px);"></ion-icon>';
                        echo 'LOGIN';
                        echo '<span class="nav-action-text">Login / Register</span>';
                        echo '</a>';
                        echo '</li>';
                    } else {
                        echo '<li class="navbar-item"><a  class="navbar-link" href="customer/your_orders.php" >My Orders</a></li>';
                        echo '<li class="navbar-item"><a  class="navbar-link" href="cus_logout.php" style="transform:translateX(400px);">Logout</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </header>



    <section class="section hero" id="hero" style="background-image: url('images/white-smoke-wallpaper-abstract-desktop-background.jpg');   background-repeat: no-repeat;
  background-size: cover;
  background-position:center;height:773px;width:100%;">
        <img src="images/bgvape-removebg-preview.png" class="imgmo">
        <div class="container">

            <h2 class="h1 hero-title">
                Cloud Keepers <strong>VL Peñaranda </strong>
            </h2>

            <p class="hero-text">
                Competently expedite alternative benefits whereas leading-edge catalysts for change. Globally
                leverage
                existing an
                expanded array of leadership.
            </p>

            <button class="btn bg-primary ">
                <a href="restaurants.php"> <span style="color:white"> Shop Now</span></a>

                <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </button>

        </div>
    </section>


    <main>
        <article>








            <section class="section product">
                <div class="container">

                    <br><br>
                    <br><br>
                    <br><br>
                    <br><br>
                    <br><br>
                    <h2 class="h2 section-title">Bestsellers Products</h2>


                    <ul class="product-list">

                        <?php
                        $ress = mysqli_query($conn, "SELECT * FROM flavor ");

                        while ($rows = mysqli_fetch_array($ress)) {
                            $imageFileName = htmlspecialchars($rows["image"]);
                            $basePath = 'images/';
                            $imagePath = $basePath . $imageFileName;

                            if (file_exists($imagePath)) {
                                $imageData = base64_encode(file_get_contents($imagePath));
                                $imageSrc = 'data:' . mime_content_type($imagePath) . ';base64,' . $imageData;
                            } else {
                                echo '<p>Image not found for category ' . $rows['category'] . '</p>';
                                continue;
                            }
                            ?>
                        <li class="product-item">
                            <div class="product-card" tabindex="0">

                                <figure class="card-banner">
                                    <img src="<?= $imageSrc ?>" width="312px" height="350px" loading="lazy"
                                        alt="Simple Fabric Shoe" class="image-contain">

                                    <ul class="card-action-list">
                                        <li class="card-action-item">
                                            <button class="card-action-btn" aria-labelledby="card-label-1">
                                                <ion-icon name="cart-outline"></ion-icon>
                                            </button>
                                            <div class="card-action-tooltip" id="card-label-1">Add to Cart</div>
                                        </li>

                                        <li class="card-action-item">
                                            <button class="card-action-btn" aria-labelledby="card-label-2">
                                                <ion-icon name="heart-outline"></ion-icon>
                                            </button>
                                            <div class="card-action-tooltip" id="card-label-2">Add to Wishlist</div>
                                        </li>

                                        <li class="card-action-item">
                                            <button class="card-action-btn" aria-labelledby="card-label-3">
                                                <ion-icon name="eye-outline"></ion-icon>
                                            </button>
                                            <div class="card-action-tooltip" id="card-label-3">Quick View</div>
                                        </li>

                                        <li class="card-action-item">
                                            <button class="card-action-btn" aria-labelledby="card-label-4">
                                                <ion-icon name="repeat-outline"></ion-icon>
                                            </button>
                                            <div class="card-action-tooltip" id="card-label-4">Compare</div>
                                        </li>
                                    </ul>
                                </figure>

                                <div class="card-content">
                                    <div class="card-cat">
                                        <a href="#" class="card-cat-link">Men</a> /
                                        <a href="#" class="card-cat-link">Women</a>
                                    </div>
                                    <h3 class="h3 card-title">
                                        <a href="customer/restaurants.php">
                                            <?= $rows['flavor'] ?>
                                        </a>
                                    </h3>

                                    <a class="card-price" href="customer/restaurants.php">
                                        $
                                        <?= $rows['price'] ?>
                                    </a>
                                </div>

                            </div>
                        </li>

                        <?php
                        }
                        ?>

                    </ul>

                </div>
            </section>






            <!-- 
        - #CTA
      -->

            <section class="section cta">
                <div class="container">

                    <ul class="cta-list">

                        <li>
                            <div class="cta-card" style="background-image: url('images/bg.jpg')">
                                <p class="card-subtitle"></p>

                                <h3 class="h2 card-title"></h3>

                                <a href="#" class="btn btn-link">
                                    <span>Shop Now</span>

                                    <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
                                </a>
                            </div>
                        </li>

                        <li>
                            <div class="cta-card" style="background-image: url('images/wall.jpg')">
                                <p class="card-subtitle"></p>

                                <h3 class="h2 card-title"></h3>

                                <a href="#" class="btn btn-link">
                                    <span>Shop Now</span>

                                    <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
                                </a>
                            </div>
                        </li>

                    </ul>

                </div>
            </section>





            <!-- 
        - #SPECIAL
      -->






            <!-- 
        - #SERVICE
      -->

            <section class="section service">
                <div class="container">

                    <ul class="service-list">

                        <li class="service-item">
                            <div class="service-card">

                                <div class="card-icon">
                                    <img src="images/service-1.png" width="53" height="28" loading="lazy"
                                        alt="Service icon">

                                </div>

                                <div>
                                    <h3 class="h4 card-title"></h3>

                                    <p class="card-text">
                                        <span></span>
                                    </p>
                                </div>

                            </div>
                        </li>

                        <li class="service-item">
                            <div class="service-card">

                                <div class="card-icon">
                                    <img src="images/service-2.png" width="43" height="35" loading="lazy"
                                        alt="Service icon">
                                </div>

                                <div>
                                    <h3 class="h4 card-title"></h3>

                                    <p class="card-text">

                                    </p>
                                </div>

                            </div>
                        </li>

                        <li class="service-item">
                            <div class="service-card">

                                <div class="card-icon">
                                    <img src="images/service-3.png" width="40" height="40" loading="lazy"
                                        alt="Service icon">
                                </div>

                                <div>
                                    <h3 class="h4 card-title"></h3>

                                    <p class="card-text">

                                    </p>
                                </div>

                            </div>
                        </li>

                        <li class="service-item">
                            <div class="service-card">

                                <div class="card-icon">
                                    <img src="images/service-4.png" width="40" height="40" loading="lazy"
                                        alt="Service icon">
                                </div>

                                <div>
                                    <h3 class="h4 card-title"></h3>

                                    <p class="card-text">

                                    </p>
                                </div>

                            </div>
                        </li>

                    </ul>

                </div>
            </section>




            <!-- 
        - #INSTA POST
      -->
            <?php
            $ress = mysqli_query($conn, "SELECT * FROM flavor ");

            // Set the number of columns to display
            $numColumns = 8;

            // Fetch flavors
            $flavors = [];
            while ($row = mysqli_fetch_array($ress)) {
                $flavors[] = $row;
            }

            // Calculate the width of the container based on the number of flavors (adjust as needed)
            $containerWidth = count($flavors) * 152; // Adjust the width as needed
            
            // Start output buffering
            ob_start();

            ?>
            <div class="scroll-container"
                style="width: <?= $containerWidth ?>px; overflow-x: auto; white-space: nowrap; ">

                <?php
                // Loop through flavors
                foreach ($flavors as $flavor) {
                    $imageFileName = htmlspecialchars($flavor["image"]);
                    $basePath = 'images/';
                    $imagePath = $basePath . $imageFileName;

                    if (file_exists($imagePath)) {
                        $imageData = base64_encode(file_get_contents($imagePath));
                        $imageSrc = 'data:' . mime_content_type($imagePath) . ';base64,' . $imageData;

                        ?>
                <section class="section insta-post" style="display: inline-block;">
                    <ul class="insta-post-list has-scrollbar">
                        <li class="insta-post-item">
                            <div class="image-container">
                                <img src="<?= $imageSrc ?>" loading="lazy" alt="Instagram post"
                                    class="insta-post-banner">
                            </div>
                            <a href="#" class="insta-post-link">
                                <ion-icon name="logo-instagram"></ion-icon>
                            </a>
                        </li>
                    </ul>
                </section>
                <?php
                    } else {
                        ?>
                <p>Image not found for category
                    <?= $flavor['flavor'] ?>
                </p>
                <?php
                    }
                }

                ?>
            </div>
            <style>
            .image-container img {
                width: 150px;
                height: 150px;
                object-fit: cover;
            }
            </style>
            <?php

            // End output buffering and capture the content
            $output = ob_get_clean();

            // Output the content
            echo $output;
            ?>









            - #FOOTER
            -->

            <footer class="footer">

                <div class="footer-top section">
                    <div class="container">

                        <div class="footer-brand">

                            <a href="#" class="logo">
                                <img src="./assets/images/logo.svg" width="160" height="50" alt="Footcap logo">
                            </a>

                            <ul class="social-list">

                                <li>
                                    <a href="#" class="social-link">
                                        <ion-icon name="logo-facebook"></ion-icon>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="social-link">
                                        <ion-icon name="logo-twitter"></ion-icon>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="social-link">
                                        <ion-icon name="logo-pinterest"></ion-icon>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="social-link">
                                        <ion-icon name="logo-linkedin"></ion-icon>
                                    </a>
                                </li>

                            </ul>

                        </div>

                        <div class="footer-link-box">

                            <ul class="footer-list">

                                <li>
                                    <p class="footer-list-title">Contact Us</p>
                                </li>

                                <li>
                                    <address class="footer-link">
                                        <ion-icon name="location"></ion-icon>

                                        <span class="footer-link-text">
                                            2751 S Parker Rd, Aurora, CO 80014, United States
                                        </span>
                                    </address>
                                </li>

                                <li>
                                    <a href="tel:+557343673257" class="footer-link">
                                        <ion-icon name="call"></ion-icon>

                                        <span class="footer-link-text">+557343673257</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="mailto:footcap@help.com" class="footer-link">
                                        <ion-icon name="mail"></ion-icon>

                                        <span class="footer-link-text">footcap@help.com</span>
                                    </a>
                                </li>

                            </ul>

                            <ul class="footer-list">

                                <li>
                                    <p class="footer-list-title">My Account</p>
                                </li>

                                <li>
                                    <a href="#" class="footer-link">
                                        <ion-icon name="chevron-forward-outline"></ion-icon>

                                        <span class="footer-link-text">My Account</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="footer-link">
                                        <ion-icon name="chevron-forward-outline"></ion-icon>

                                        <span class="footer-link-text">View Cart</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="footer-link">
                                        <ion-icon name="chevron-forward-outline"></ion-icon>

                                        <span class="footer-link-text">Wishlist</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="footer-link">
                                        <ion-icon name="chevron-forward-outline"></ion-icon>

                                        <span class="footer-link-text">Compare</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="footer-link">
                                        <ion-icon name="chevron-forward-outline"></ion-icon>

                                        <span class="footer-link-text">New Products</span>
                                    </a>
                                </li>

                            </ul>

                            <div class="footer-list">

                                <p class="footer-list-title">Opening Time</p>

                                <table class="footer-table">
                                    <tbody>

                                        <tr class="table-row">
                                            <th class="table-head" scope="row">Mon - Tue:</th>

                                            <td class="table-data">8AM - 10PM</td>
                                        </tr>

                                        <tr class="table-row">
                                            <th class="table-head" scope="row">Wed:</th>

                                            <td class="table-data">8AM - 7PM</td>
                                        </tr>

                                        <tr class="table-row">
                                            <th class="table-head" scope="row">Fri:</th>

                                            <td class="table-data">7AM - 12PM</td>
                                        </tr>

                                        <tr class="table-row">
                                            <th class="table-head" scope="row">Sat:</th>

                                            <td class="table-data">9AM - 8PM</td>
                                        </tr>

                                        <tr class="table-row">
                                            <th class="table-head" scope="row">Sun:</th>

                                            <td class="table-data">Closed</td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>

                            <div class="footer-list">

                                <p class="footer-list-title">Newsletter</p>

                                <p class="newsletter-text">
                                    Authoritatively morph 24/7 potentialities with error-free partnerships.
                                </p>

                                <form action="" class="newsletter-form">
                                    <input type="email" name="email" required placeholder="Email Address"
                                        class="newsletter-input">

                                    <button type="submit" class="btn btn-primary">Subscribe</button>
                                </form>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="container">

                        <p class="copyright">
                            &copy; 2022 <a href="#" class="copyright-link">codewithsadee</a>. All Rights Reserved
                        </p>

                    </div>
                </div>

            </footer>







            <a href="#top" class="go-top-btn" data-go-top>
                <ion-icon name="arrow-up-outline"></ion-icon>
            </a>





            <!-- 
    - custom js link
  -->
            <script src="js/script.js"></script>

            <!-- 
    - ionicon link
 


</body>

</html>