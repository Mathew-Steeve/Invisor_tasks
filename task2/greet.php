<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data using $_POST superglobal
    $name = htmlspecialchars($_POST['name']);
    $message = htmlspecialchars($_POST['message']);

    // Display a message using the collected data
    echo "<p>Welcome " . $name . "</p>";
    echo "<p>". $message . "</p>";
} else {
    // If the script is accessed directly without a POST request
    echo "<p>Please submit the form.</p>";
}
?>
<!-- command to start server : php -S localhost:port -->
 <!-- run (http://localhost:8000/file/path) in browser not in live server -->