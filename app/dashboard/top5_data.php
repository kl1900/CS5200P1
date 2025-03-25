<?php
include '../features/db.php';
header('Content-Type: application/json');

$sql = "SELECT Username AS playerName, Wins, Losses, Draws,
               (Wins / (Wins + Losses + Draws + 0.0)) AS win_ratio
        FROM Player
        ORDER BY win_ratio DESC
        LIMIT 5";

$result = mysqli_query($conn, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'playerName' => $row['playerName'],
        'winRatio' => round($row['win_ratio'] * 100, 1),
        'wins' => (int) $row['Wins'],
        'losses' => (int) $row['Losses'],
        'draws' => (int) $row['Draws']
    ];
}

echo json_encode($data);
