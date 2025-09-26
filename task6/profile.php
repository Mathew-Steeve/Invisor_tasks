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

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
// echo"hi";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; }
        .profile-container { max-width: 500px; margin: auto; background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .profile-photo img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
        .logout { text-align: right; margin-bottom: 10px; }
        .logout a { color: #d9534f; text-decoration: none; }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>

        <h2>My Profile</h2>

        <div class="profile-photo">
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
            <?php else: ?>
                <img src="uploads/default.png" alt="Default Profile Photo">
            <?php endif; ?>
        </div>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="address">Address:</label>
            <textarea name="address" id="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>

            <label for="profile_photo">Change Profile Photo:</label>
            <input type="file" name="profile_photo" id="profile_photo">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
