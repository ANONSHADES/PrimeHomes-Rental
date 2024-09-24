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

// Get properties for rent
$sql = "SELECT * FROM properties WHERE type = 'rent'";
$result = $conn->query($sql);

$rent_properties = [];
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
        $rent_properties[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Houses for Rent</title>
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
        }
        .property h2 {
            color: #008080;
            margin-bottom: 10px;
        }
        .property p {
            color: #666;
            margin-bottom: 10px;
        }
        .btn-container {
            display: flex;
            justify-content: space-around;
        }
        .btn-container button {
            background-color: #ff6f61;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-container button:hover {
            background-color: #e15244;
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
        /* Slideshow container */
        .slides-container {
            position: relative;
            max-width: 100%;
            margin: 10px 0;
        }
        .slide {
            display: none;
        }
        .slide img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
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
    </style>
</head>
<body>
    <h1>Houses for Rent</h1>
    <div id="property-list">
        <?php if (!empty($rent_properties)): ?>
            <?php foreach ($rent_properties as $property): ?>
                <div class="property">
                    <h2><?php echo htmlspecialchars($property['title']); ?></h2>
                    <div class="slides-container">
                        <?php foreach ($property['images'] as $index => $image): ?>
                            <div class="slide slide<?php echo $property['id']; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Property Image">
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($property['images']) > 1): ?>
                            <a class="prev" onclick="plusSlides(<?php echo $property['id']; ?>, -1)">&#10094;</a>
                            <a class="next" onclick="plusSlides(<?php echo $property['id']; ?>, 1)">&#10095;</a>
                        <?php endif; ?>
                    </div>
                    <p>Description: <?php echo htmlspecialchars($property['description']); ?></p>
                    <p>Price: KSh <?php echo number_format($property['price'], 2, '.', ','); ?></p>
                    <p>Address: <?php echo htmlspecialchars($property['address']); ?></p>
                    <!-- Add more property details as needed -->

                    <!-- Button to book a visit -->
                    <div class="btn-container">
                        <button onclick="bookVisit(<?php echo $property['id']; ?>)">Book Visit</button>
                        <button onclick="payDeposit(<?php echo $property['id']; ?>)">Pay Deposit</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No houses available for rent at the moment.</p>
        <?php endif; ?>
    </div>
    <a href="index.php" class="back-btn">Back</a>

    <script>
        // Function to book a visit
        function bookVisit(propertyId) {
            // Redirect to booking page with property id
            window.location.href = `book_visit.php?id=${propertyId}`;
        }

        // Function to pay deposit
        function payDeposit(propertyId) {
            // Redirect to payment page with property id
            window.location.href = `pay_deposit.php?id=${propertyId}`;
        }

        // Slide functionality
        var slideIndex = {};

        function plusSlides(propertyId, n) {
            showSlides(propertyId, slideIndex[propertyId] += n);
        }

        function currentSlide(propertyId, n) {
            showSlides(propertyId, slideIndex[propertyId] = n);
        }

        function showSlides(propertyId, n) {
            var i;
            var slides = document.getElementsByClassName(`slide${propertyId}`);
            if (!slideIndex[propertyId]) { slideIndex[propertyId] = 1; }
            if (n > slides.length) { slideIndex[propertyId] = 1; }
            if (n < 1) { slideIndex[propertyId] = slides.length; }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex[propertyId] - 1].style.display = "block";
        }
    </script>
</body>
</html>



