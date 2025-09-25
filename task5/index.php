<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "user_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['pass'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        echo "Login successful! Welcome, $email";
    } else {
        echo "Invalid email or password";
    }
}
?>