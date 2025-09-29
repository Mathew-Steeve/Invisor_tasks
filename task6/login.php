<?php
session_start();
include 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
// $conn = mysqli_connect("localhost", "root", "", "new_user_db");

// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }
function sendLoginEmail($toEmail)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';            
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sampleudemy2@gmail.com';      
        $mail->Password   = 'add password';         
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('sampleudemy2@gmail.com', 'Profile Manager');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Login Notification';
        $mail->Body    = "
            <p>Hello <strong>{$toEmail}</strong>,</p>
            <p>You have successfully logged in on " . date("Y-m-d H:i:s") . ".</p>
            <p>If this wasn't you, please contact support immediately.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['pass'];

    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // if (password_verify($password, $row['password'])) 
            if ($password == $row['password']){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email'];
            sendLoginEmail($row['email']);

            header("Location: profile.php");
            exit;
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
</head>
<body>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="" method="post">
        <table width="200" border="0">
        <tr>
            <td>Email</td>
            <td><input type="text" name="email" required></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input type="password" name="pass" required></td>
        </tr>
        <tr>
            <td><button type="submit">Login</button></td>
        </tr>
        <tr>
            <td><a href="sign_up.php">Create account</a></td>
        </tr>
        </table>
    </form>
</body>
</html>
