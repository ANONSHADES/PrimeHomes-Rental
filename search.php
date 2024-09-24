<?php
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

// Retrieve search query from GET parameters
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare and execute the search for properties
$sql_properties = "SELECT * FROM properties WHERE title LIKE ? OR description LIKE ? OR address LIKE ?";
$stmt_properties = $conn->prepare($sql_properties);
$search_query = "%$query%";
$stmt_properties->bind_param("sss", $search_query, $search_query, $search_query);
$stmt_properties->execute();
$result_properties = $stmt_properties->get_result();

// Prepare and execute the search for services
$sql_services = "SELECT * FROM services WHERE service_name LIKE ? OR description LIKE ? OR contact_info LIKE ?";
$stmt_services = $conn->prepare($sql_services);
$stmt_services->bind_param("sss", $search_query, $search_query, $search_query);
$stmt_services->execute();
$result_services = $stmt_services->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wellness Homes</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            color: #333;
            padding: 20px;
            margin: 0;
        }
        h1, h2 {
            color: #fff;
            text-align: center;
            margin: 0;
        }
        header {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #333;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        nav {
            flex-grow: 1;
            text-align: center;
            background-color: #333;
            padding: 10px 0;
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
        .property, .service {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: calc(33.33% - 20px);
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
        .search-container {
            display: inline-block;
            position: relative;
            top: -10px;
        }
        .search-container input[type="text"] {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-container button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .search-container button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header>
        <img src="icon.png" alt="Home Logo" class="logo">
        <h1>Welcome to Wellness Homes</h1>
    </header>
    <nav>
        <a href="rent.php"><i class="fas fa-home"></i> Rent a House</a>
        <a href="buy.php"><i class="fas fa-store-alt"></i> Buy a House</a>
        <?php if ($user_is_admin): ?>
            <a href="add_property.php"><i class="fas fa-plus"></i> Add Property</a>
        <?php endif; ?>
        <a href="services.php"><i class="fas fa-cog"></i> Services</a>
        <form class="search-container" method="GET">
            <input type="hidden" name="action" value="list_properties">
            <input type="text" name="query" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'logout.php' : 'login.php'; ?>" class="logout-btn" style="float: right;">
            <?php echo isset($_SESSION['user_id']) ? '<i class="fas fa-sign-out-alt"></i> Logout' : '<i class="fas fa-sign-in-alt"></i> Login'; ?>
        </a>
    </nav>
    <div id="property-list">
        <h2>Search Results</h2>

        <h3>Properties</h3>
        <?php if ($result_properties->num_rows > 0): ?>
            <?php while ($row = $result_properties->fetch_assoc()): ?>
                <div class="property">
                    <h2><a href="property_details.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></h2>
                    <p><?php echo $row['description']; ?></p>
                    <p>Price: KSh <?php echo number_format($row['price'], 2); ?></p>
                    <p>Address: <?php echo $row['address']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No matching properties found.</p>
        <?php endif; ?>

        <h3>Services</h3>
        <?php if ($result_services->num_rows > 0): ?>
            <?php while ($row = $result_services->fetch_assoc()): ?>
                <div class="service">
                    <h2><?php echo $row['service_name']; ?></h2>
                    <p><?php echo $row['description']; ?></p>
                    <p>Contact: <?php echo $row['contact_info']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No matching services found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

