<?php
include '../features/db.php';
header("Content-Type: application/json");

$sql = "
        SELECT 
            PlayerID, 
            Username,
            Wins,
            Losses,
            Draws,
            Withdraw,
            ROUND(
                CASE
                    WHEN (Wins + Losses + Draws + Withdraw) = 0 THEN 0
                    ELSE (Wins / (Wins + Losses + Draws + Withdraw)) * 100
                END, 2
            ) AS WinRate
        FROM 
            Player
        ORDER BY 
            WinRate DESC
        LIMIT 5
    ";

$result = null;
$error = "";
$data = [];

try {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo json_encode(["error" => mysqli_error($conn)]);
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
} catch (Throwable $e) {
    echo json_encode(["error" => "Fatal error: " . $e->getMessage()]);
}
?>