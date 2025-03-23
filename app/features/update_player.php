<?php
include "./db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['PlayerID']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    // Update only username and email
    $sql = "UPDATE Player SET 
            Username = '$username', 
            email_address = '$email' 
            WHERE PlayerID = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Player updated successfully. <a href='/'>Back to Player List</a>";
    } else {
        echo "Error updating player: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
