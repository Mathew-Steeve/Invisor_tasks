<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "new_user_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
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
