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

// Get properties for sale
$sql = "SELECT * FROM properties WHERE type = 'buy'";
$result = $conn->query($sql);

$buy_properties = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $property_id = $row['id'];
        $sql_images = "SELECT image_path FROM property_images WHERE property_id = '$property_id'";
        $result_images = $conn->query($sql_images);

        $images = [];
        while ($image_row = $result_images->fetch_assoc()) {
            $images[] = $image_row['image_path'];
        }
        $row['images'] = $images;
        $buy_properties[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homes for Sale</title>
    <link rel="icon" href="home.png" type="image/png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        #property-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .property {
            background-color: #fff;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            position: relative;
        }
        .property h2 {
            color: #008080;
            margin-bottom: 10px;
        }
        .property p {
            color: #666;
            margin-bottom: 10px;
        }
        .property img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 10px;
            object-fit: cover;
            max-height: 200px;
            display: none;
        }
        .property img.active {
            display: block;
        }
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: auto;
            padding: 16px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.8);
        }
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }
        .prev {
            left: 0;
            border-radius: 3px 0 0 3px;
        }
        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.4);
        }
        .buy-btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            display: block;
            margin: 20px auto;
        }
        .buy-btn:hover {
            background-color: #218838;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .back-btn:hover {
            background-color: #45a049;
        }
        .navigation {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>
    <h1>Homes for Sale</h1>
    <div id="property-list">
        <?php if (!empty($buy_properties)): ?>
            <?php foreach ($buy_properties as $property): ?>
                <div class="property">
                    <h2><?php echo htmlspecialchars($property['title']); ?></h2>
                    <p>Description: <?php echo htmlspecialchars($property['description']); ?></p>
                    <p>Price: KSh <?php echo number_format($property['price'], 2, '.', ','); ?></p>
                    <p>Address: <?php echo htmlspecialchars($property['address']); ?></p>
                    <div class="navigation">
                        <a class="prev" onclick="changeImage(this, -1)">&#10094;</a>
                        <a class="next" onclick="changeImage(this, 1)">&#10095;</a>
                    </div>
                    <?php foreach ($property['images'] as $key => $image): ?>
                        <img src="<?php echo $image; ?>" alt="Property Image" class="<?php echo ($key === 0) ? 'active' : ''; ?>" data-index="<?php echo $key; ?>">
                    <?php endforeach; ?>
                    <button class="buy-btn" onclick="buyHome(<?php echo $property['id']; ?>)">Buy Now</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No homes available for sale at the moment.</p>
        <?php endif; ?>
    </div>
    <a href="index.php" class="back-btn">Back</a>

    <script>
        function changeImage(btn, n) {
            var propertyDiv = btn.parentNode.parentNode;
            var currentImage = propertyDiv.querySelector('.property img.active');
            currentImage.classList.remove('active');
            var totalImages = propertyDiv.querySelectorAll('.property img').length;
            var newIndex = (parseInt(currentImage.dataset.index) + n + totalImages) % totalImages;
            var newImage = propertyDiv.querySelector('.property img[data-index="' + newIndex + '"]');
            newImage.classList.add('active');
        }

        function buyHome(propertyId) {
            window.location.href = `buy_home.php?id=${propertyId}`;
        }
    </script>
</body>
</html>



