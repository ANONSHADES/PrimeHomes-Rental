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
            $param_id = $_POST['service_id'];

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
}

// Close connection
$conn->close();
?>
