<?php
// Start session
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

// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_is_admin = $user_logged_in && $_SESSION['role'] === 'admin';

// Get property details
$property_id = $_GET['id'];
$sql = "SELECT * FROM properties WHERE id = '$property_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $property = $result->fetch_assoc();

    // Format the price with commas and dots
    $formatted_price = number_format($property['price'], 2, '.', ',');
} else {
    // Handle property not found
    echo "Property not found.";
    exit();
}

// Get property images
$sql_images = "SELECT image_path FROM property_images WHERE property_id = '$property_id'";
$result_images = $conn->query($sql_images);

$property_images = [];
while ($image_row = $result_images->fetch_assoc()) {
    $property_images[] = $image_row['image_path'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Details</title>
    <link rel="icon" href="home.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        h1 {
            color: #333;
            text-align: center;
            animation: slideIn 1s ease;
        }
        @keyframes slideIn {
            0% {
                transform: translateY(-50px);
            }
            100% {
                transform: translateY(0);
            }
        }
        .property-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: slideIn 1s ease;
            max-width: 600px;
            margin: 0 auto;
        }
        .property-details h2 {
            color: #008080;
            margin-bottom: 10px;
        }
        .property-details p {
            color: #666;
            margin-bottom: 5px;
        }
        .amenity-icon {
            color: #336699;
            font-size: 24px;
            margin-right: 5px;
        }
        .property-images {
            margin-top: 20px;
            text-align: center;
        }
        .property-images img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Property Details</h1>
    <div class="property-details">
        <h2><?php echo htmlspecialchars($property['title']); ?></h2>
        <p>Description: <?php echo htmlspecialchars($property['description']); ?></p>
        <p>Price: KSh <?php echo htmlspecialchars($formatted_price); ?></p>
        <p>Address: <?php echo htmlspecialchars($property['address']); ?></p>
        <h3>Amenities:</h3>
        <ul>
            <?php 
                $amenities = explode(', ', $property['amenities']);
                foreach ($amenities as $amenity): 
                    $icon_class = '';
                    switch(strtolower($amenity)) {
                        case 'wifi':
                            $icon_class = 'fas fa-wifi';
                            break;
                        case 'parking':
                            $icon_class = 'fas fa-parking';
                            break;
                        case 'security':
                            $icon_class = 'fas fa-shield-alt';
                            break;
                        case 'water':
                            $icon_class = 'fas fa-tint'; // Updated to use the correct icon for water
                            break;
                        case 'electricity':
                            $icon_class = 'fas fa-bolt';
                            break;
                        // Add more cases for other amenities if needed
                    }
                    
                    // Check if $icon_class is empty before displaying the <i> tag
                    $icon_html = $icon_class !== '' ? '<i class="' . htmlspecialchars($icon_class) . ' amenity-icon"></i> ' : '';
            ?>
            <li><?php echo $icon_html . htmlspecialchars($amenity); ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="property-images">
            <?php foreach ($property_images as $image): ?>
                <img src="<?php echo htmlspecialchars($image); ?>" alt="Property Image">
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Back button -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="text-decoration: none; background-color: #008080; color: #fff; padding: 10px 20px; border-radius: 5px;">Back to Property List</a>
    </div>
</body>
</html>
