<?php
include 'db.php';

if (!isset($_GET['playerId'])) {
    echo "<p>No player selected (no playerId param).</p>";
    exit;
}

$playerId = intval($_GET['playerId']);

echo "<p>Results: </p>";

// Debug: Show playerId in the output to confirm
//echo "<p>Loading data for Player ID: $playerId</p>";

if ($playerId <= 0) {
    echo "<p style='color:red;'>Invalid player selected.</p>";
    exit;
}

$sql = "
SELECT 
    p.Username,
    a.name AS AchievementName
FROM 
    Player p
JOIN 
    PlayerAchievement pa ON p.PlayerID = pa.PlayerID
JOIN 
    Achievements a ON pa.AchievementID = a.AchievementID
WHERE 
    p.PlayerID = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "<p style='color:red;'>SQL error: " . $conn->error . "</p>";
    exit;
}

$stmt->bind_param("i", $playerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>
            <tr><th>Username</th><th>Achievement</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['Username']}</td>
                <td>{$row['AchievementName']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No achievements found for this player.</p>";
}

$stmt->close();
$conn->close();
?>