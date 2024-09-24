<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_rental_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['id'];
    $new_title = $_POST['title'];
    $new_description = $_POST['description'];
    $new_price = floatval(str_replace(',', '', $_POST['price'])); // Convert to float
    $new_address = $_POST['address'];
    $new_type = $_POST['type'];
    $new_amenities = isset($_POST["amenities"]) ? implode(", ", $_POST["amenities"]) : '';
    $new_additional_info = $_POST['additional_info'];

    $stmt = $conn->prepare("UPDATE properties SET title=?, description=?, price=?, address=?, type=?, amenities=?, additional_info=? WHERE id=?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    
    $stmt->bind_param("ssdssssi", $new_title, $new_description, $new_price, $new_address, $new_type, $new_amenities, $new_additional_info, $property_id);

    if ($stmt->execute()) {
        // Redirect to index.php after successful update
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating property: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>




