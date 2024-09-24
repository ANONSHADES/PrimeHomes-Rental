<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_rental_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Get the property ID to delete
    $property_id = $_GET['id'];

    // Check if the logged-in user is the admin who added the property
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $property_id = $conn->real_escape_string($property_id);
        $sql = "DELETE FROM properties WHERE id='$property_id' AND admin_id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Property deleted successfully!";
        } else {
            echo "Error deleting property: " . $conn->error;
        }
    } else {
        echo "You do not have permission to delete this property.";
    }
}

$conn->close();
?>
