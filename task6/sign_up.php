<?php
$servername = "localhost";  
$username   = "root";       
$password   = "";           
$dbname     = "new_user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['pass']);

    if (empty($name) || empty($email) || empty($pass)) {
        echo "All fields are required!";
    } else {
        
        // $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $pass);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
 </head>
 <body>
    <form action="" method="post">

        <table width="200" border="0">
        <tr>
            <td>Username</td>
            <td> <input type="text" name="name" > </td>
        </tr>
        <tr>
            <td>Email</td>
            <td> <input type="text" name="email" > </td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input type="password" name="pass"></td>
        </tr>
        <tr>
            <td> <button type="submit">Register</button></td>
            <td></td>
        </tr>
        <tr>
            <td><a href="login.php">Login</a> 
        </tr>
        </table>
    </form>
 </body>
 </html>
