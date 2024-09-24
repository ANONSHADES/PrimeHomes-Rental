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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $address = $_POST["address"];
    $type = $_POST["type"];
    $amenities = implode(", ", $_POST["amenities"]);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Upload multiple images
    $upload_dir = "uploads/";
    $uploaded_files = [];
    $total_files = count($_FILES['files']['name']);

    for ($i = 0; $i < $total_files; $i++) {
        $file_name = $_FILES['files']['name'][$i];
        $file_tmp = $_FILES['files']['tmp_name'][$i];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            $uploaded_files[] = $upload_path;
        }
    }

    // Insert property details into database
    $sql = "INSERT INTO properties (title, description, price, address, type, amenities, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssi", $title, $description, $price, $address, $type, $amenities, $user_id);

    if ($stmt->execute()) {
        // Insert property images into database
        $property_id = $conn->insert_id;
        foreach ($uploaded_files as $image_path) {
            $sql = "INSERT INTO property_images (property_id, image_path) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $property_id, $image_path);
            $stmt->execute();
        }

        echo "Property added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Property</title>
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
         /* Style for back button */
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
    <h2>Add Property</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
<label for="title">Title:</label>
<input type="text" id="title" name="title" required>
<label for="description">Description:</label>
<textarea id="description" name="description" required></textarea>
<label for="price">Price (KES):</label>
<input type="number" id="price" name="price" required>
<label for="address">Address:</label>
<input type="text" id="address" name="address" required>
<label for="type">Type:</label>
<select id="type" name="type" required>
<option value="rent">Rent</option>
<option value="buy">Buy</option>
</select>
<label for="amenities">Amenities:</label><br>
<input type="checkbox" id="wifi" name="amenities[]" value="wifi">
<label for="wifi">Wifi</label><br>
<input type="checkbox" id="parking" name="amenities[]" value="parking">
<label for="parking">Parking</label><br>
<input type="checkbox" id="security" name="amenities[]" value="security">
<label for="security">Security</label><br>
<input type="checkbox" id="water" name="amenities[]" value="water">
<label for="water">Water</label><br> <!-- Changed checkbox label to "Water" -->
<input type="checkbox" id="electricity" name="amenities[]" value="electricity">
<label for="electricity">Electricity</label><br>
<label for="additional_info">Additional Info:</label>
<textarea id="additional_info" name="additional_info"></textarea>
<label for="files">Upload Images:</label><br>
<input type="file" id="files" name="files[]" multiple required>
<input type="submit" value="Add Property">
</form>
<a href="index.php" class="back-btn">Back</a>

</body>
</html>





