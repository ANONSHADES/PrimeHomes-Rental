<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_rental_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch property details
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $sql = "SELECT * FROM properties WHERE id='$property_id'";
    $result = $conn->query($sql);
    $property = $result->fetch_assoc();

    // Format the price with commas and dots
    $property['price'] = number_format($property['price'], 2, '.', ','); // 2 decimal places, dot as decimal separator, comma as thousands separator
} else {
    // Redirect to index.php if property ID is not provided
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Property</title>
    <link rel="icon" href="home.png" type="image/png">
    <style>
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
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select, input[type="checkbox"] {
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 10px;
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
    <h2>Edit Property</h2>
    <form action="update_property.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($property['id']); ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($property['description']); ?></textarea>
        <label for="price">Price (KES):</label>
        <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($property['address']); ?>" required>
        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="rent" <?php echo ($property['type'] == 'rent') ? 'selected' : ''; ?>>Rent</option>
            <option value="buy" <?php echo ($property['type'] == 'buy') ? 'selected' : ''; ?>>Buy</option>
        </select>
        <label for="amenities">Amenities:</label><br>
        <input type="checkbox" id="wifi" name="amenities[]" value="wifi" <?php echo (strpos($property['amenities'], 'wifi') !== false) ? 'checked' : ''; ?>>
        <label for="wifi">Wifi</label><br>
        <input type="checkbox" id="parking" name="amenities[]" value="parking" <?php echo (strpos($property['amenities'], 'parking') !== false) ? 'checked' : ''; ?>>
        <label for="parking">Parking</label><br>
        <input type="checkbox" id="security" name="amenities[]" value="security" <?php echo (strpos($property['amenities'], 'security') !== false) ? 'checked' : ''; ?>>
        <label for="security">Security</label><br>
        <input type="checkbox" id="water" name="amenities[]" value="water" <?php echo (strpos($property['amenities'], 'water') !== false) ? 'checked' : ''; ?>>
        <label for="water">Water</label><br>
        <input type="checkbox" id="electricity" name="amenities[]" value="electricity" <?php echo (strpos($property['amenities'], 'electricity') !== false) ? 'checked' : ''; ?>>
        <label for="electricity">Electricity</label><br>
        <label for="additional_info">Additional Info:</label>
        <textarea id="additional_info" name="additional_info"><?php echo htmlspecialchars($property['additional_info']); ?></textarea>
        <label for="files">Upload Images:</label><br>
        <input type="file" id="files" name="files[]" multiple>
        <input type="submit" value="Save Changes">
    </form>
</body>
</html>





