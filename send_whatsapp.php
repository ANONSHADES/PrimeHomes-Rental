<?php
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve message from the form
    $message = $_POST['message'];

    // Encode message for URL
    $encoded_message = urlencode($message);

    // WhatsApp URL with pre-filled message
    $whatsapp_url = "https://wa.me/?text=$encoded_message";

    // Redirect the user to WhatsApp
    header("Location: $whatsapp_url");
    exit();
} else {
    // If the form is not submitted via POST method, redirect to bookvisit.php
    header("Location: bookvisit.php");
    exit();
}
?>
