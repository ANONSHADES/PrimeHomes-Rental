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

// Define variables and initialize with empty values
$service_name = $description = $contact_info = $charges = "";
$service_name_err = $description_err = $contact_info_err = $charges_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate service name
    if (empty(trim($_POST["service_name"]))) {
        $service_name_err = "Please enter the service name.";
    } else {
        $service_name = trim($_POST["service_name"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the service description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate contact info
    if (empty(trim($_POST["contact_info"]))) {
        $contact_info_err = "Please enter the contact information.";
    } else {
        $contact_info = trim($_POST["contact_info"]);
    }

    // Validate charges
    if (empty(trim($_POST["charges"]))) {
        $charges_err = "Please enter the charges for the service.";
    } else {
        $charges = trim($_POST["charges"]);
    }

    // Check input errors before updating the database
    if (empty($service_name_err) && empty($description_err) && empty($contact_info_err) && empty($charges_err)) {
        // Prepare an update statement
        $sql = "UPDATE services SET service_name=?, description=?, contact_info=?, charges=? WHERE id=?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssi", $param_service_name, $param_description, $param_contact_info, $param_charges, $param_id);

            // Set parameters
            $param_service_name = $service_name;
            $param_description = $description;
            $param_contact_info = $contact_info;
            $param_charges = $charges;
            $param_id = $_GET['id'];

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to services page
                header("location: services.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
} else {
    // Retrieve service details from the database
    $service_id = $_GET['id'];
    $sql = "SELECT * FROM services WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $service_id;
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $service_name = $row['service_name'];
                $description = $row['description'];
                $contact_info = $row['contact_info'];
                $charges = $row['charges'];
            } else {
                echo "Service not found.";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service</title>
    <link rel="icon" href="home.png" type="image/png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 0 auto;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        span.error {
            color: red;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        a {
            display: inline-block;
            background-color: #ccc;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
        }
        a:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <h2>Edit Service</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $service_id; ?>" method="post">
        <!-- Service Name -->
        <div>
            <label>Service Name</label>
            <input type="text" name="service_name" value="<?php echo $service_name; ?>">
            <span class="error"><?php echo $service_name_err; ?></span>
        </div>
        <!-- Description -->
        <div>
            <label>Description</label>
            <textarea name="description"><?php echo $description; ?></textarea>
            <span class="error"><?php echo $description_err; ?></span>
        </div>
        <!-- Contact Info -->
        <div>
            <label>Contact Info</label>
            <input type="text" name="contact_info" value="<?php echo $contact_info; ?>">
            <span class="error"><?php echo $contact_info_err; ?></span>
        </div>
        <!-- Charges -->
        <div>
            <label>Charges</label>
            <input type="text" name="charges" value="<?php echo $charges; ?>">
            <span class="error"><?php echo $charges_err; ?></span>
        </div>
        <div>
            <button type="submit">Save Changes</button>
            <a href="services.php">Cancel</a>
        </div>
    </form>
</body>
</html>
