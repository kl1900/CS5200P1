<?php
include '../features/db.php';
header('Content-Type: application/json');

$sql = "
    SELECT
        sub.PlayerID,
        sub.Username,
        sub.Week_Range,
        SUM(sub.Playtime_Minutes) AS Total_Playtime_Minutes
    FROM (
        SELECT
            p.PlayerID,
            p.Username,
            CONCAT(
                DATE_FORMAT(DATE_SUB(g.Game_start, INTERVAL WEEKDAY(g.Game_start) DAY), '%d/%m/%Y'),
                ' - ',
                DATE_FORMAT(DATE_ADD(g.Game_start, INTERVAL (6 - WEEKDAY(g.Game_start)) DAY), '%d/%m/%Y')
            ) AS Week_Range,
            TIMESTAMPDIFF(MINUTE, g.Game_start, g.Game_end) AS Playtime_Minutes
        FROM Player p
        JOIN PlayerGameSession pgs ON p.PlayerID = pgs.PlayerID
        JOIN Game g ON pgs.GameID = g.GameID
        WHERE g.Game_end IS NOT NULL
    ) AS sub
    GROUP BY
        sub.PlayerID,
        sub.Username,
        sub.Week_Range
    ORDER BY Total_Playtime_Minutes DESC
    LIMIT 5;
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'PlayerID' => (int) $row['PlayerID'],
        'Username' => $row['Username'],
        'Week' => $row['Week_Range'],
        'Minutes' => (int) $row['Total_Playtime_Minutes']
    ];
}

echo json_encode($data);
$conn->close();
