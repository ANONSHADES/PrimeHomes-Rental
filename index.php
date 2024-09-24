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

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'list_properties') {
    $sql = "SELECT * FROM properties";
    $result = $conn->query($sql);

    $properties = [];
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
            $properties[] = $row;
        }
    }

    echo json_encode($properties);
    exit;
}

// Handle deleting a property
if ($action === 'delete_property' && $user_is_admin) {
    $property_id = $_GET['id'];
    $sql = "DELETE FROM properties WHERE id='$property_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Property deleted successfully!";
    } else {
        echo "Error deleting property: " . $conn->error;
    }
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PRIME HOMES</title>
    <link rel="icon" href="home.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Add Font Awesome CSS -->
    <style>
       body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
            margin: 0;
            position: relative;
            min-height: 100vh;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(5px); /* Decrease blur */
            z-index: -1;
        }
        h1 {
            color: #fff;
            text-align: center;
            animation: slideIn 1s ease;
            margin: 0;
        }
        header {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: rgba(73, 117, 156, 0.8); /* Semi-transparent background */
            border-radius: 5px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        nav {
            flex-grow: 1;
            text-align: center;
            background-color: rgba(0, 84, 119, 0.8); /* Semi-transparent background */
            padding: 10px 0;
            border-radius: 5px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        nav a:hover {
            color: #ffc107;
        }
        #property-list {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .property {
            background-color: rgba(112,128,144); /* Semi-transparent background */
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0);
            width: calc(33.33% - 20px);
            animation: slideIn 1s ease;
        }
        .property img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .delete-btn, .edit-btn, .logout-btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .delete-btn:hover, .edit-btn:hover, .logout-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
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
    <header>
        <img src="home.png" alt="Home Logo" class="logo">
        <h1>Welcome to Prime Homes</h1>
    </header>
    <nav>
        <a href="rent.php"><i class="fas fa-home"></i> Rent a House</a>
        <a href="buy.php"><i class="fas fa-store-alt"></i> Buy a House</a>
        <?php if ($user_is_admin): ?>
            <a href="add_property.php"><i class="fas fa-plus"></i> Add Property</a>
        <?php endif; ?>
        <a href="services.php"><i class="fas fa-cog"></i> Services</a>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'logout.php' : 'login.php'; ?>" class="logout-btn" style="float: right;">
            <?php echo isset($_SESSION['user_id']) ? '<i class="fas fa-sign-out-alt"></i> Logout' : '<i class="fas fa-sign-in-alt"></i> Login'; ?>
        </a>
    </nav>
    <div id="property-list"></div>
    <script>
        // Function to delete a property
        function deleteProperty(id) {
            if (confirm("Are you sure you want to delete this property?")) {
                fetch(`?action=delete_property&id=${id}`)
                .then(response => response.text())
                .then(message => {
                    alert(message);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Load properties
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action') || 'list_properties';

            fetch(`?action=${action}`)
            .then(response => response.json())
            .then(data => {
                const propertyList = document.getElementById('property-list');
                propertyList.innerHTML = ''; // Clear the list before adding new items
                if (data.length === 0) {
                    propertyList.innerHTML = '<p>No properties found.</p>';
                } else {
                    data.forEach(property => {
                        const div = document.createElement('div');
                        div.classList.add('property');
                        let imagesHtml = '';
                        property.images.forEach((image, index) => {
                            imagesHtml += `<div class="slide slide${property.id}" style="display: ${index === 0 ? 'block' : 'none'};"><img src="${image}" alt="Property Image"></div>`;
                        });
                        div.innerHTML = `
                            <h2><a href="property_details.php?id=${property.id}">${property.title}</a></h2>
                            <p>${property.description}</p>
                            <p>Price: KSh ${formatPrice(property.price)}</p>
                            <p> Address: ${property.address}</p>
                            <div class="slides-container">
                                ${imagesHtml}
                                <a class="prev" onclick="plusSlides(${property.id}, -1)">&#10094;</a>
                                <a class="next" onclick="plusSlides(${property.id}, 1)">&#10095;</a>
                            </div>
                            <?php if ($user_is_admin): ?>
                                <button class="delete-btn" onclick="deleteProperty(${property.id})">Delete</button>
                                <button class="edit-btn" onclick="editProperty(${property.id})">Edit</button>
                            <?php endif; ?>
                        `;
                        propertyList.appendChild(div);
                    });
                }
            })
            .catch(error => console.error('Error fetching properties:', error));
        });

        // Format property price with commas and dots
        function formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Function to edit a property
        function editProperty(id) {
            // Redirect to edit page with property id
            window.location.href = `edit_property.php?id=${id}`;
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


















