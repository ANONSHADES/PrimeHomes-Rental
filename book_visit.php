<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "functions.php"; // Include your functions file or define your getPropertyDetails() function here

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the message is set and not empty
    if(isset($_POST['message']) && !empty($_POST['message'])) {
        // Your message to send
        $message = $_POST['message'];
        
        // Phone number to send the message to (replace with your admin's phone number)
        $phone_number = "+254782870821";
        
        // Redirect to WhatsApp with the message
        $whatsapp_url = "https://wa.me/{$phone_number}?text=" . urlencode($message);
        header("Location: $whatsapp_url");
        exit();
    } else {
        // Handle case where message is not provided
        echo "Please enter a message.";
    }
}

// Check if the property ID is provided in the URL
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $property_id = $_GET['id'];
    // Retrieve property details
    $property_details = getPropertyDetails($property_id); // Retrieve property details
    $property_title = $property_details['title'];
    $property_price = $property_details['price'];
    $property_address = $property_details['address'];
} else {
    // Handle no property selected error
    echo "No property selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Visit</title>
    <style>
        /* Styles for the Go Back button */
        .goback-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .goback-btn:hover {
            background-color: #45a049;
        }
        /* Other styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Book Visit</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="message">Message:</label>
        <textarea id="message" name="message" placeholder="Enter your message"><?php 
            if(isset($_POST['message'])) {
                echo htmlspecialchars($_POST['message']);
            } else {
                $preloaded_message = "Hello, I want to book a visit for the property:\n";
                $preloaded_message .= "Title: " . ($property_title ?? '') . "\n";
                $preloaded_message .= "Price: " . ($property_price ?? '') . "\n";
                $preloaded_message .= "Address: " . ($property_address ?? '');
                echo $preloaded_message;
            }
        ?></textarea>
        <input type="submit" value="Send">
    </form>
    <a href="javascript:history.back()" class="goback-btn">Go Back</a>
</body>
</html>







