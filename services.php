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

// Query to fetch services from the database
$sql = "SELECT * FROM services";
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services</title>
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
        .service {
            background-color: #fff;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            display: inline-block;
        }
        .service h2 {
            color: #008080;
            margin-bottom: 10px;
        }
        .service p {
            color: #666;
            margin-bottom: 10px;
        }
        /* Add more CSS styles as needed */
        .service-buttons {
            margin-top: 20px;
        }
        .service-buttons button {
            margin: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .service-buttons button.edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .service-buttons button.delete-btn {
            background-color: #f44336;
            color: white;
        }
        .service-buttons button:hover {
            background-color: #555;
        }
        #add-service-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #add-service-btn:hover {
            background-color: #45a049;
        }
        /* Style for back button */
        a.back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a.back-btn:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>
    <h1>Our Services</h1>

    <!-- Display services -->
    <div id="services-list">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="service">
                    <h2><?php echo htmlspecialchars($service['service_name']); ?></h2>
                    <p>Contacts: <?php echo htmlspecialchars($service['contact_info']); ?></p>
                    <p>Charges: <?php echo htmlspecialchars($service['charges']); ?></p>
                    <!-- Add more details as needed -->

                    <!-- Edit and Delete buttons -->
                    <div class="service-buttons">
                        <button class="edit-btn" onclick="editService(<?php echo $service['id']; ?>)">Edit</button>
                        <button class="delete-btn" onclick="deleteService(<?php echo $service['id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No services available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Add Service button -->
    <button id="add-service-btn" onclick="location.href='add_service.php'">Add Service</button>
    <a href="index.php" class="back-btn">Back</a>

    <script>
        // Function to redirect to edit_service.php with service ID
        function editService(serviceId) {
            window.location.href = `edit_service.php?id=${serviceId}`;
        }

        // Function to delete a service
        function deleteService(serviceId) {
            if (confirm("Are you sure you want to delete this service?")) {
                fetch(`delete_service.php?id=${serviceId}`)
                .then(response => response.text())
                .then(message => {
                    alert(message);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>

