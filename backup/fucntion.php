<?php
session_start();
$host = 'localhost';
$dbname = 'vape';
$username = 'root';
$password = '';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:\wamp64\www\crud\PHPMailer-6.9.1\PHPMailer-6.9.1\src\Exception.php';
require 'C:\wamp64\www\crud\PHPMailer-6.9.1\PHPMailer-6.9.1\src\PHPMailer.php';
require 'C:\wamp64\www\crud\PHPMailer-6.9.1\PHPMailer-6.9.1\src\SMTP.php';
if (isset($_POST["backup"])) {
    // SMTP configuration
    $smtpHost = 'smtp.gmail.com';
    $smtpPort = 587;
    $smtpUser = 'santiagojhonny607@gmail.com'; // Your Gmail address
    $smtpPass = 'hnxk ooic mnqp oovq'; // Gmail app password

    // Backup filename
    $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $errorLog = 'error_log.txt';

    // Command for mysqldump
    $command = "C:\\wamp64\\bin\\mysql\\mysql8.3.0\\bin\\mysqldump --user={$username} --password={$password} --host={$host} {$dbname} > {$backupFile}";

    // Execute command and check for errors
    exec($command, $output, $result);

    if ($result !== 0 || !file_exists($backupFile) || filesize($backupFile) === 0) {
        echo "Failed to create backup. Please check the error log for details.<br>";
        if (file_exists($errorLog)) {
            echo nl2br(file_get_contents($errorLog));
        }
        exit;
    }

    // Load PHPMailer
 

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = 'tls';
    $mail->Port = $smtpPort;

    // Email configuration
    $mail->setFrom($smtpUser, 'CLoud Keepers');
    $mail->addAddress('markcaguia123@gmail.com');
    $mail->Subject = 'Database Backup';
    $mail->isHTML(true);
    $mail->Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Backup</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
            .container { background-color: #fff; border-radius: 5px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
            h1 { color: #333; }
            p { font-size: 16px; color: #555; }
            .footer { margin-top: 20px; font-size: 12px; color: #999; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Database Backup Successful</h1>
            <p>Dear User,</p>
            <p>The database backup was created successfully. The backup file is attached for your records.</p>
            <div class="footer">&copy; ' . date('Y') . ' Your Company Name. All rights reserved.</div>
        </div>
    </body>
    </html>';

    // Attach the backup file
    if (file_exists($backupFile)) {
        $mail->addAttachment($backupFile);
    } else {
      
        $_SESSION['error'] = "Backup file does not exist. Check the backup process.";
        header("location: backup.php");
    }

    // Send the email and provide feedback
    if ($mail->send()) {
        $_SESSION['Sucessfully'] = "We Sent The Backup of your database in your Gmail";

        // Redirect to the backup page
        header("location: backup.php");
        exit;
    
    } else {
      
        $_SESSION['error'] = "Failed to send backup. Mailer Error: " . $mail->ErrorInfo;
        header("location: backup.php");
    }

    // Clean up backup and error log files
    unlink($backupFile);
    if (file_exists($errorLog)) {
        unlink($errorLog);
    }
}
?>
