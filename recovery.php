<?php 
session_start();
require 'database/db_conn.php';
require 'updatepassword.php';
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Debug: Log the token being validated
    error_log("Validating token: " . $token);

    // Validate token and expiry
    $query = "SELECT id FROM users WHERE recovery_token = ? AND token_expiry > NOW()";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id);
    mysqli_stmt_fetch($stmt);

    // Check if a user ID was found
    if ($user_id) {
        // Valid token, show update password form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Password</title>
            <!-- Bootstrap CSS -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 
            <style>
              html {
  height: 100%;
}

.login-box {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 500px;
  padding: 40px;
  transform: translate(-50%, -50%);
  background: #eaeaea;
  box-sizing: border-box;
  box-shadow: 0 5px 25px 0 rgba(200,255,255,.4);
  border-radius: 10px;
}

.login-box h2 {
  margin: 0 0 30px;
  padding: 0;
  color: #000;
  text-align: center;
}

.login-box .user-box {
  position: relative;
}

.login-box .user-box input {
  width: 100%;
  padding: 10px 0;
  font-size: 16px;
  color: #000;
  margin-bottom: 30px;
  border: none;
  border-bottom: 1px solid #000;
  outline: none;
  background: white;
}
.login-box .user-box label {
  position: absolute;
  top:0;
  left: 0;
  padding: 10px 0;
  font-size: 16px;
  color: #000;
  pointer-events: none;
  transition: .5s;
}

.login-box .user-box input:focus ~ label,
.login-box .user-box input:valid ~ label {
  top: -20px;
  left: 0;
  color: #003366;
  font-size: 12px;
}

.login-box form a {
  position: relative;
  display: inline-block;
  padding: 10px 20px;
  color: #03e9f4;
  font-size: 16px;
  text-decoration: none;
  text-transform: uppercase;
  overflow: hidden;
  transition: .5s;
  margin-top: 40px;
  letter-spacing: 4px
}
.addbutton{
    background-color: #263f8c  !important;
    border-radius: 10px;
    width: 420px;
    color: white !important;
    padding: 8px 10px !important;
    font-family: "Baloo Paaji 2", sans-serif;
  font-optical-sizing: auto;
  font-weight: 500;
  font-style: normal;
  }

  .addbutton:hover{
    background-color: #003366  !important;
    color: #ffff !important;
    padding: 8px 10px !important;
  }

  .user-box {
            position: relative;
            margin-bottom: 20px;
           
        }
        .user-box input {
            padding-right: 30px; /* Space for the eye icon */
            -webkit-appearance: none; /* Remove default appearance */
        }
        .eye-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-110%);
            cursor: pointer;
        }
        body {
        background: 
        linear-gradient(rgba(0, 0, 0, 0.650), rgba(0, 0, 0, 0.850)), /* Gradient overlay */
        url(images/bava.jpg); /* Background image */    /* Adjust the blur amount (8px is just an example) */
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-position: center;
        background-size: cover;
        width: 100%;
        background-repeat: no-repeat;
      
    }
            </style>
        </head>
        <body>
            <div class="form-container">
                
                
                <div class="login-box">
              
  <h2>Change Your Password</h2>
  <form method="POST" onsubmit="return validatePassword()">
    <input type="hidden" name="idnanguser" value="<?php echo htmlspecialchars($user_id); ?>" >
    <div class="user-box">
        <input type="password" name="password" id="password" required="" style="background-color:#eaeaea;" >
        <label>Password</label>
        <i class="fas fa-eye eye-icon" id="togglePassword" onclick="togglePasswordVisibility()"></i>
    </div>
    <div class="user-box">
        <input type="password" name="retype" id="retype" required="" style="background-color:#eaeaea;" >
        <label>Retype Password</label>
        <i class="fas fa-eye eye-icon" id="toggleRetype" onclick="toggleRetypeVisibility()"></i>
    </div>
    <button type="submit" name="updatepassword" class="addbutton float-right">Update</button>
</form>
</div>
            </div>

            <!-- Bootstrap JS and dependencies -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
       
<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function toggleRetypeVisibility() {
    const retypeInput = document.getElementById('retype');
    const toggleIcon = document.getElementById('toggleRetype');
    if (retypeInput.type === 'password') {
        retypeInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        retypeInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function validatePassword() {
    const password = document.getElementById('password').value;
    const retype = document.getElementById('retype').value;
    if (password !== retype) {
        alert('Passwords do not match. Please try again.');
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
</script>
        </body>
        </html>
        <?php 
    } else {
        // Token expired or invalid, show error message
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <!-- Bootstrap CSS -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <style>
                body {
                    background: linear-gradient(to right, #ff416c, #ff4b2b);
                    font-family: 'Arial', sans-serif;
                }
                .error-container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
                    padding: 30px;
                    max-width: 400px;
                    margin: 100px auto;
                    text-align: center;
                }
                .error-header h2 {
                    color: #ff416c;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-header">
                    <h2>Error 402</h2>
                    <p>Token is expired or invalid. Please try again.</p>
                </div>
            </div>

            <!-- Bootstrap JS and dependencies -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php 
    }
} else {
    // No token provided
    echo '402'; // Or handle as needed
}
?>
