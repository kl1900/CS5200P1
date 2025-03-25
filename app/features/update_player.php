<?php
include "./db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['PlayerID']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE Player SET Username = ?, email_address = ? WHERE PlayerID = ?");
    $stmt->bind_param("ssi", $username, $email, $id);

    if ($stmt->execute()) {
        echo "Player updated successfully.";
    } else {
        echo "Error updating player: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
echo<<<HTML
<br>
<a href="/">Back to Player List</a>
HTML;