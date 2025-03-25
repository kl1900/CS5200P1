<?php
include '../features/db.php';
header('Content-Type: application/json');

$sql = "
  SELECT
    DATE_FORMAT(registration_date, '%Y-%m') AS Month,
    COUNT(*) AS Registrations
  FROM Player
  GROUP BY Month
  ORDER BY Month
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit;
}

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "month" => $row["Month"],
        "count" => (int) $row["Registrations"]
    ];
}

echo json_encode($data);
?>