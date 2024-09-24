<?php
session_start();

// Check if a session is not already active
if (session_status() === PHP_SESSION_NONE) {
    // Start the session
    session_start();
}

// Check if property ID is provided
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "home_rental_db";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get property details
    $sql = "SELECT * FROM properties WHERE id = $property_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
        $property_details = "Title: " . $property['title'] . "\n";
        $property_details .= "Price: KSh " . number_format($property['price'], 2, '.', ',') . "\n";
        $property_details .= "Address: " . $property['address'];
    } else {
        $property_details = "Property details not found.";
    }

    $conn->close();
} else {
    // Redirect if property ID is not provided
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            margin: 0;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Buy Home</h2>
    <form action="https://wa.me/+254782870821" method="get" target="_blank">
        <textarea name="text" rows="5"><?php echo "Hello, I want to buy:\n" . $property_details; ?></textarea><br>
        <input type="submit" value="Send">
    </form>
    <a href="javascript:history.back()" class="back-btn">Go Back</a>
</body>
</html>





