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
        $contact_info_err = "Please enter the contact information for the service.";
    } else {
        $contact_info = trim($_POST["contact_info"]);
    }

    // Validate charges
    if (empty(trim($_POST["charges"]))) {
        $charges_err = "Please enter the charges for the service.";
    } else {
        $charges = trim($_POST["charges"]);
    }

    // Check input errors before inserting into database
    if (empty($service_name_err) && empty($description_err) && empty($contact_info_err) && empty($charges_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO services (service_name, description, contact_info, charges) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssd", $param_service_name, $param_description, $param_contact_info, $param_charges);

            // Set parameters
            $param_service_name = $service_name;
            $param_description = $description;
            $param_contact_info = $contact_info;
            $param_charges = $charges;

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Service</title>
    <link rel="icon" href="icon.png" type="image/png">
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
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group .error {
            color: red;
        }
        .form-group button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Add New Service</h2>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($service_name_err)) ? 'error' : ''; ?>">
                <label>Service Name</label>
                <input type="text" name="service_name" value="<?php echo $service_name; ?>">
                <span class="error"><?php echo $service_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($description_err)) ? 'error' : ''; ?>">
                <label>Description</label>
                <textarea name="description"><?php echo $description; ?></textarea>
                <span class="error"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($contact_info_err)) ? 'error' : ''; ?>">
                <label>Contact Info</label>
                <input type="text" name="contact_info" value="<?php echo $contact_info; ?>">
                <span class="error"><?php echo $contact_info_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($charges_err)) ? 'error' : ''; ?>">
                <label>Charges</label>
                <input type="text" name="charges" value="<?php echo $charges; ?>">
                <span class="error"><?php echo $charges_err; ?></span>
            </div>
            <div class="form-group">
                <button type="submit">Add Service</button>
            </div>
        </form>
    </div>
</body>
</html>




