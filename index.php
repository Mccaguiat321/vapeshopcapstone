<?php
session_start();
require "database/db_conn.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\wamp64\www\crud\PHPMailer-6.9.1\PHPMailer-6.9.1\src\Exception.php';
require 'C:\wamp64\www\crud\PHPMailer-6.9.1\PHPMailer-6.9.1\src\PHPMailer.php';
require 'C:\wamp64\www\crud\PHPMailer-6.9.1\PHPMailer-6.9.1\src\SMTP.php';

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['emailInput'])) {
        $email = $_POST["emailInput"];

        // Check if the email exists
        $query = "SELECT id, token_expiry FROM users WHERE email = ?";  // Added comma between id and token_expiry
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $token_expiry);
        mysqli_stmt_fetch($stmt);
        
        $current_time = date("Y-m-d H:i:s");  // Get current time in the same format as your token_expiry
        
        if ($token_expiry >  $current_time) {
            $_SESSION['error'] = "Your link has not expired yet. Please check your email for recovering your account.";
            header("Location: index.php");
            exit();
        }
        if (empty($id)) {
            $_SESSION['error'] = "Account does not exist in our system.";
            header("Location: index.php");
            exit();
        }
        mysqli_stmt_close($stmt);

        // Generate a secure reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+5 minutes')); // Expiry set to 5 minutes from now

        // Store the token in the database
        $update_query = "UPDATE users SET recovery_token = ?, token_expiry = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Send email with reset link
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'santiagojhonny607@gmail.com';  // Your email
            $mail->Password = 'hnxk ooic mnqp oovq';         // Use app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('santiagojhonny607@gmail.com', 'CLoud Keepers Vape Lounge'); // Your sender email
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: auto;
                        background: #ffffff;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                    }
                    .header {
                        background: #003366;
                        color: #ffffff;
                        padding: 20px;
                        text-align: center;
                    }
                    .content {
                        padding: 20px;
                    }
                    .button {
                        display: inline-block;
                        background: #003366;
                        color: #f4f4f4;
                        padding: 10px 20px;
                        border-radius: 5px;
                        text-decoration: none;
                        margin-top: 10px;
                    }
                    .footer {
                        text-align: center;
                        padding: 20px;
                        font-size: 12px;
                        color: #777777;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Password Reset Request</h1>
                    </div>
                    <div class='content'>
                        <p>Hello,</p>
                        <p>We received a request to reset your password. Click the button below to reset it:</p>
                        <a href='http://127.0.0.1/cruds/recovery.php?token=$token' class='button'>Reset Password</a>
                        <p>If you didn't request a password reset, you can ignore this email.</p>
                        <p>Thank you!</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " CLoud Keepers Vape Lounge. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->send();
            $_SESSION['invemessage'] = "We Send Into your Gmail A recover your account";
            header("Location: index.php");
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <title>LOGIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap4.5.2.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php require 'updatepassword.php'; ?>
    <style>
  .modal-content{
    border-radius: 30px;
    
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


        #timer {
            font-family: "Orbitron", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        .alert {
            border-radius: 0.5rem;
            color: red;
            font-family: "Poppins", sans-serif;
            font-weight: 900;
            font-style: normal;

            text-transform: uppercase;
        }
        .card {
    width: 400px; /* Set card width to match image width */
    height: 360px; /* Set card height to match image height */
    border-top-right-radius: 20px; /* Adjust the radius value as needed */
    border-bottom-right-radius: 20px; /* Adjust the radius value as needed */
    border-top-left-radius: 0; /* Ensure top-left corner is squared */
    border-bottom-left-radius: 0; /* Ensure bottom-left corner is squared */
  
}

    .continaers{
        border-radius: 50%;
        
    }
.welcomlogin{
    font-family: "Libre Franklin", sans-serif;
  font-optical-sizing: auto;
  font-weight: 900;
  font-style: normal;
  font-size: 20px;
}
.continaers{
    background-color: white;
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
  .addbutton:hover{
    background-color: #00CCDD  !important;
    color: #0B192C !important;
    padding: 8px 10px !important;
  }
  html {
  height: 100%;
}

.login-box {
  position: absolute;
  top: 67%;
  left: 50%;
  width: 400px;
  padding: 5px 40px;
  transform: translate(-50%, -50%);
  
  box-sizing: border-box;

  border-radius: 10px;
}


.login-box .user-box {
  position: relative;
}

.login-box .user-box input {
  width: 100%;
  padding: 10px 0;
  font-size: 16px;
  color: black;
  margin-bottom: 15px;
  border: none;
  border-bottom: 1px solid #003366;
  outline: none;

}
.login-box .user-box label {
  position: absolute;
  top:0;
  left: 0;
  padding: 10px 0;
  font-size: 16px;
  color: #003366;
  pointer-events: none;
  transition: .5s;
}

.login-box .user-box input:focus ~ label,
.login-box .user-box input:valid ~ label {
  top: -20px;
  left: 0;
 color: black;
  font-size: 12px;
}

.login-box form a {
  position: relative;
  display: inline-block;
  padding: 10px 20px;
 color: black;
  font-size: 16px;
  text-decoration: none;
  text-transform: uppercase;
  overflow: hidden;
  transition: .5s;
  margin-top: 40px;
  letter-spacing: 4px
}
.modal-content {
    background-color: #f8f9fa;
    border-radius: 8px;
    
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
    
}

.modal-header {
    background-color: #007bff;
    color: white;
    padding: 15px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.modal-header h5 {
    font-weight: bold;
    margin: 0;
}

.modal-body {
    
    width: 650px;
}

.form-label {
    font-weight: bold;
    margin-bottom: 10px;
    display: inline-block;
    color: #333;
}

.input-group-text {
    background-color: #007bff;
    color: white;
    border: none;
}

.form-control {
    border: 2px solid #007bff;
    transition: border-color 0.3s ease-in-out;
}

.form-control:focus {
    border-color: #0056b3;
    box-shadow: none;
}

.modal-buttons {
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    font-size: 14px;
    text-transform: uppercase;
    font-weight: bold;
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
}

.btn-primary {
    background-color: #007bff;
    border: none;
}

.btn-primary:hover, .btn-secondary:hover {
    background-color: #0056b3;
    border: none;
}

.modal-footer {
    border-top: none;
}

    </style>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/backgroundmalala.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />

</head>


<body>
 

    </div>

    <div class="continaers">
        <div class="">
            <div class=" d-flex"> 
            <img src="images/de.png" width="350px" height="360px" alt="" style="border-top-left-radius: 20px; border-bottom-left-radius: 20px; border-top-right-radius: 0; border-bottom-right-radius: 0;">

                <div class="card">
<center style="margin-top: 8%;">
<img src="images/Picsart_24-10-07_16-25-55-381.png" class="logo">

</center>
                    <div class="text-center welcomlogin" style="letter-spacing: 2px; font-weight: 900;"><span
                            style="color:#e68a00">W</span>elcome Back!</div>
       

                    <div class="card-body">
                     
                    <div class="login-box">
 
                    <form method="post" action="login.php">
    <div class="user-box">
        <input type="text" name="uname" required>
        <label>Username</label>
    </div>
    <div class="user-box">
        <div style="position: relative;">
            <input type="password" id="passwordInput" name="password" required>
            <label>Password</label>
            <!-- Eye icon inside the password input -->
            <span id="passwordToggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-70%); cursor: pointer;">
                <i class="fas fa-eye-slash"></i>
            </span>
        </div>
    </div>
    
    <button type="submit" class="btn btn-block buttonlogin">LOGIN</button>
</form>

  <div class="text-center mt-2">
                                <p class="mb-0">Forgot Password? <a href="#" data-toggle="modal"
                                        data-target="#emailRecoveryModal" style=" color:rgba(35, 63, 62, 0.858)">Click
                                        Here</a></p>
                            </div>
</div>


                    </div>
                </div>
            </div>
        </div>
    </div>

   

    <div class="modal fade" id="emailRecoveryModal" tabindex="-1" role="dialog"
    aria-labelledby="emailRecoveryModalLabel" aria-hidden="true" style="transform:translateX(-80px)">
    <div class="modal-dialog modal-dialog-centered" role="document" >
    <div class="modal-content" >
            
            <div class="modal-body">
            <div class="row">

            <div class="col-md-6 d-flex align-items-center justify-content-center">
            <img src="images/e59e07f3-6c81-4d45-b052-a8be96bb2d25.jpg" width="220" height="250" alt="Cashier Background">
          </div>
          <div class="col-md-6" style="padding: 40px 40px 0 0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="transform: translateY(-20px);">
                    <span aria-hidden="true">&times;</span>
                </button>
                <form id="recoveryForm" action="" method="POST">
                    <div class="form-group">
                        <label for="emailInput" class="form-label">Enter your email address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" class="form-control" id="emailInput" name="emailInput"
                                aria-describedby="emailHelp" placeholder="Enter your email" required>
                        </div>
                        <small id="emailHelp" class="form-text text-muted">We will send you a recovery link.</small>
                    </div>
                    <div class="modal-buttons d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ml-2" name="email">Submit</button>
                    </div>
                </form>
                </div>

                </div>

            </div>
        </div>
    </div>
</div>


   
    <div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <?php 
                    // Assuming $id is already available in your session or passed from a previous request
                    if (isset($id)) {
                        // Prepare the SQL query to fetch the recovery code for the given user id
                        $query = "SELECT id, recovery FROM users WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "i", $id); // "i" for integer
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $userId, $recovery);
                        mysqli_stmt_fetch($stmt);
                        mysqli_stmt_close($stmt);
                    }
                    ?>

                    <div class="form-group">
                        <label for="recovery">Recovery Code:</label>
                        <input type="text" class="form-control" name="recovery" id="recovery" 
                               value="<?php echo isset($recovery) ? htmlspecialchars($recovery) : ''; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="otpsendbyme">Enter OTP:</label>
                        <input type="text" class="form-control" id="otpsendbyme" name="otpsendbyme" id="otpsendbyme" required>
                    </div>

                    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

                    <button type="submit" class="btn btn-primary" id="sub" name="otp">Submit</button>
                </form>

                <div id="timer" style="margin-top: 10px;">2 mins 0 secs</div>
                <span class="timer-text">Time Left:</span>
                <span class="timer-display"></span>
                
                <button id="expiredBtn" class="btn btn-secondary" data-toggle="modal" data-target="#emailRecoveryModal"
                        style="display: none; margin-top: 10px;">Resend</button>
            </div>
        </div>
    </div>
</div>
    </div>





 




    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Your Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="POST">

                        <div class="form-group">
                            <label for="input2">Enter Your Passowrd</label>
                            <input type="text" name="id" id="id" value="<?php echo isset($id) ? $id : ''; ?>">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="changepassword" name="changepassword"
                                    placeholder="Enter Password" aria-describedby="passwordToggle">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="passwordToggle"><i
                                            class="fas fa-eye-slash"></i></button>
                                </div>
                            </div>


                        </div>
                        <button type="submit" class="btn " name="updatepassword">Change It</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

  
    <script src="js/bootstrap4.5.2.js"></script>
    <script>
    // Initialize countdown duration
    const initialTime = 20; // 2 minutes in seconds
    let count = localStorage.getItem('countdown') ? parseInt(localStorage.getItem('countdown')) : initialTime;

    const updateTimerDisplay = () => {
        const minutes = Math.floor(count / 60);
        const seconds = count % 60;
        $('#timer').html(minutes + ' mins ' + seconds + ' secs');
    };

    // Function to start the countdown
    const startCountdown = () => {
        const timer = setInterval(function() {
            count--;
            updateTimerDisplay();
            localStorage.setItem('countdown', count); // Save the countdown in localStorage

            // Check if countdown is finished
            if (count <= 0) {
                clearInterval(timer);
                $('#timer').html('Expired'); // Display expired message
                $('#expiredBtn').show(); // Show resend button
                $('#sub').hide(); // Show resend button
                $('#otpsendbyme').hide(); // Show resend button
                localStorage.removeItem('countdown'); // Clear countdown from localStorage
            }
        }, 1000); // Update every second
    };

    // On document ready, initialize the timer display
    $(document).ready(function() {
        updateTimerDisplay();
    });

    // When the OTP modal is shown
    $('#otpModal').on('show.bs.modal', function () {
        // Reset the timer if it's expired
        if (count <= 0) {
            count = initialTime; // Reset to initial time
        }
        updateTimerDisplay(); // Update display on modal open
        startCountdown(); // Start countdown
    });

    // Clear countdown when modal is closed (optional)
    $('#otpModal').on('hidden.bs.modal', function () {
        localStorage.removeItem('countdown');
        count = initialTime; // Reset countdown
    });
    $('#expiredBtn').on('click', function () {
        $('#otpModal').modal('hide'); // Close the OTP modal
    });
</script>

    <script>
        $(document).ready(function () {
            // This script will run when the document is fully loaded
            <?php if (isset($random_letters)) { ?>
                $('#otpModal').modal('show');
            <?php } ?>
        });
    </script>
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
        <?php
        if (isset($_SESSION['error'])) { ?>
            alertify.set('notifier', 'position', 'bottom-left');
               alertify.error(' <?php echo $_SESSION['error']; ?> ');
                <?php unset($_SESSION['error']);
        } ?>
    </script>

    <script>
        // JavaScript to fade out the error message slowly
        setTimeout(function () {
            var errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 2s';
                errorMessage.style.opacity = '0';
                setTimeout(function () {
                    errorMessage.style.display = 'none';
                }, 2000); // After 2 seconds, hide the element
            }
        }, 2000); // 10 seconds (10,000 milliseconds)
    </script>

    <script>
    const passwordToggle = document.getElementById('passwordToggle');
const passwordInput = document.getElementById('passwordInput');

passwordToggle.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle the icon between eye and eye-slash
    this.querySelector('i').classList.toggle('fa-eye');
    this.querySelector('i').classList.toggle('fa-eye-slash');
});

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('passwordToggles').addEventListener('click', function () {
                var passwordField = document.getElementById('changepasswords');
                var passwordToggleBtn = document.getElementById('passwordToggles');

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    passwordToggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
                } else {
                    passwordField.type = "password";
                    passwordToggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
                }
            });
        });
    </script>


</body>

</html>