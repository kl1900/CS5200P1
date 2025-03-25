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
?>

<h2>Edit Player Profile</h2>

<form method="POST" action="./update_player.php">
    <input type="hidden" name="PlayerID" value="<?php echo $player['PlayerID']; ?>">

    Username:<br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($player['Username']); ?>" required><br><br>

    Email:<br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($player['email_address']); ?>" required><br><br>

    <input type="submit" value="Update Player">
</form>

<br>
<a href="/">Back to Player List</a>