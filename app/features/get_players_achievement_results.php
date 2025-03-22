<?php
include 'db.php';

$playerId = intval($_GET['playerId'] ?? 0);

if ($playerId <= 0) {
    echo "<p>No valid player selected.</p>";
    exit;
}

$playerName = '';
$stmt_player = $conn->prepare("SELECT Username FROM Player WHERE PlayerID = ?");
$stmt_player->bind_param("i", $playerId);
$stmt_player->execute();
$stmt_player->bind_result($playerName);
$stmt_player->fetch();
$stmt_player->close();

echo "<h3>Achievements and Related Sessions for Player: <strong>$playerName</strong></h3>";

// changes 1: link each achievement to the earliest matching session
$sql = "
SELECT
    a.name AS AchievementName,
    pa.achievement_datetime,
    s.SessionID,
    s.Session_start,
    s.Session_end
FROM
    PlayerAchievement pa
JOIN
    Achievements a ON pa.AchievementID = a.AchievementID
LEFT JOIN Session s ON
    s.SessionID = (
        SELECT s1.SessionID
        FROM Session s1
        WHERE s1.Session_start <= pa.achievement_datetime
          AND s1.Session_end >= pa.achievement_datetime
        ORDER BY s1.Session_start ASC
        LIMIT 1
    )
WHERE
    pa.PlayerID = ?
ORDER BY
    pa.achievement_datetime ASC
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
    echo '<div class="table-container">';
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>Achievement</th>
            <th>Unlocked At</th>
            <th>Session ID</th>
            <th>Session Start</th>
            <th>Session End</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        $achievementName = htmlspecialchars($row['AchievementName']);
        $achievementDateTime = htmlspecialchars($row['achievement_datetime']);
        $sessionId = $row['SessionID'] !== null ? $row['SessionID'] : 'N/A';
        $sessionStart = $row['Session_start'] !== null ? $row['Session_start'] : 'N/A';
        $sessionEnd = $row['Session_end'] !== null ? $row['Session_end'] : 'N/A';

        echo "<tr>
                <td>$achievementName</td>
                <td>$achievementDateTime</td>
                <td>$sessionId</td>
                <td>$sessionStart</td>
                <td>$sessionEnd</td>
              </tr>";
    }

    echo "</table>";
    echo '</div>';
} else {
    echo "<p>No achievements found for this player.</p>";
}

$stmt->close();
$conn->close();
?>