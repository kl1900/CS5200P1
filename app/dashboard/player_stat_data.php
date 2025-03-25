<?php
include '../features/db.php';
header('Content-Type: application/json');

$sql = "
SELECT 
    p.Username, 
    PlayerPlayTime.playtime, 
    achievements 
FROM (
    SELECT 
        pgs.PlayerID, 
        SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, g.Game_start, g.Game_end))) AS playtime
    FROM PlayerGameSession AS pgs
    LEFT JOIN Game AS g ON pgs.GameID = g.GameID
    GROUP BY pgs.PlayerID
) AS PlayerPlayTime
LEFT JOIN Player AS p ON PlayerPlayTime.PlayerID = p.PlayerID
LEFT JOIN (
    SELECT 
        pa.PlayerID, 
        COUNT(pa.AchievementID) AS achievements
    FROM PlayerAchievement AS pa 
    GROUP BY pa.PlayerID
) AS achieves ON p.PlayerID = achieves.PlayerID
ORDER BY achievements DESC
LIMIT 5;
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit;
}

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'Username' => $row['Username'],
        'Playtime' => $row['playtime'],
        'Achievements' => isset($row['achievements']) ? (int) $row['achievements'] : 0
    ];
}

echo json_encode($data);
