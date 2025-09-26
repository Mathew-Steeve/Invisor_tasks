<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "new_user_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name    = trim($_POST['name']);
$email   = trim($_POST['email']);
$address = trim($_POST['address']);

$profile_photo = null;
if (!empty($_FILES['profile_photo']['name'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["profile_photo"]["name"]);
    $target_file = $target_dir . $file_name;

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_types)) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            $profile_photo = $file_name;
        }
    }
}

if ($profile_photo) {
    $sql = "UPDATE users SET name=?, email=?, address=?, profile_photo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $address, $profile_photo, $user_id);
} else {
    $sql = "UPDATE users SET name=?, email=?, address=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $address, $user_id);
}

if ($stmt->execute()) {
    header("Location: profile.php?success=1");
} else {
    header("Location: profile.php?error=1");
}
exit();
?>
