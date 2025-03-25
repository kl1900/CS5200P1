<?php
include "./db.php";
$sql = "
    SELECT 
        ProcedureText
    FROM 
        StoredProcedures
    ORDER BY 
        ProcedureID
";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $procedureText = htmlspecialchars($row['ProcedureText']);
    // Show username with win rate in dropdown
    echo "<option value='$procedureText'>$procedureText</option>";
}
?>