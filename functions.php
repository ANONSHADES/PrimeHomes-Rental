<?php
// Function to fetch property details from the database
function getPropertyDetails($property_id) {
    // Database connection parameters
    $servername = "localhost";
    $username = "root"; 
    $password = "";     
    $dbname = "home_rental_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute query
    $sql = "SELECT * FROM properties WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if property exists
    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
    } else {
        $property = false; // Property not found
    }

    // Close connection and return property details
    $stmt->close();
    $conn->close();
    return $property;
}
?>
