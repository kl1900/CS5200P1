<?php
include "./db.php"; // Adjust path if needed

// Allowed columns for sorting
$allowedSort = ["PlayerID", "Username", "Week_Range", "Total_Playtime_Minutes"];

// Get sorting parameters from GET
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'PlayerID';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

// SQL query with sorting
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
    ORDER BY $sort $order
";

$result = $conn->query($sql);

if (!$result) {
    echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    exit;
}

$defaultIcon = ' <span style="color:#999;">↕</span>';
$arrow = fn($col) => $sort === $col ? ($order === 'ASC' ? ' ▲' : ' ▼') : $defaultIcon;

echo "<table border='1' cellpadding='5'>";
echo "<tr>
    <th><span class='sort-header' data-sort='PlayerID' data-order='asc' style='cursor:pointer;'>Player ID{$arrow('PlayerID')}</span></th>
    <th><span class='sort-header' data-sort='Username' data-order='asc' style='cursor:pointer;'>Username{$arrow('Username')}</span></th>
    <th><span class='sort-header' data-sort='Week_Range' data-order='asc' style='cursor:pointer;'>Week{$arrow('Week_Range')}</span></th>
    <th><span class='sort-header' data-sort='Total_Playtime_Minutes' data-order='asc' style='cursor:pointer;'>Total Playtime (minutes){$arrow('Total_Playtime_Minutes')}</span></th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['PlayerID']}</td>
        <td>{$row['Username']}</td>
        <td>{$row['Week_Range']}</td>
        <td>{$row['Total_Playtime_Minutes']}</td>
    </tr>";
}

echo "</table>";

$conn->close();
?>
