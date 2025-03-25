<?php
include '../features/db.php';
header('Content-Type: application/json');

$sql = "
SELECT
  CASE WHEN pp.PlayerID IS NOT NULL THEN 'Paid' ELSE 'Unpaid' END AS PaymentStatus,
  COUNT(p.PlayerID) AS Count
FROM Player p
LEFT JOIN PaidPlayer pp ON p.PlayerID = pp.PlayerID
GROUP BY PaymentStatus;
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit;
}

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "status" => $row["PaymentStatus"],
        "count" => (int) $row["Count"]
    ];
}

echo json_encode($data);
?>