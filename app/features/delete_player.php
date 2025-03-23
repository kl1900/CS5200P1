<?php
include "./db.php";

// Make sure the ID is present
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch player details
    $sql = "SELECT * FROM Player WHERE PlayerID = $id";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $player = $result->fetch_assoc();
    } else {
        echo "Player not found.";
        exit;
    }
} else {
    echo "No player ID provided.";
    exit;
}
// delete sessions related to the player
// which is not affected by cascade

// delete player
// Check if ID is posted
$sql = "DELETE From Session where SessionID in (
    select * from (
    SELECT s.SessionID FROM `Session` as s
    right join PlayerGameSession as pgs on pgs.SessionID = s.SessionID
    where pgs.PlayerID = $id) as tmp
)
";
$stmt = $conn->prepare($sql);
if ($stmt->execute()) {
    echo "Sessions related to player deleted successfully.";
} else {
    echo "Error deleting Sessions related to player: " . $conn->error;
}

echo "<br>";
// Prepare and execute safely
$sql = "DELETE FROM Player WHERE PlayerID = $id";

$stmt = $conn->prepare($sql);
if ($stmt->execute()) {
    echo "Player deleted successfully.";
} else {
    echo "Error deleting player: " . $conn->error;
}

$stmt->close();


?>
<br>
<a href="/">Back to Player List</a>