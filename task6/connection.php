<?php
$conn = mysqli_connect("localhost", "root", "", "new_user_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>