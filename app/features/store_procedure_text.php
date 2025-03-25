<?php
include "./db.php";
$procedure_text = trim($_GET['procedureText'] ?? "");

if (empty($procedure_text)) {
    echo "<p>Not a valid procedure.</p>";
    exit;
}

$result = null;
$error = "";
if (!empty($procedure_text)) {
    $stmt = $conn->prepare("INSERT INTO StoredProcedures (ProcedureText) VALUES (?)");
    $stmt->bind_param("s", $procedure_text);

    if ($stmt->execute()) {
        echo "<p>Procedure stored successfully.</p>";
    } else {
        echo "<p style='color:red;'>Insert failed: " . $stmt->error . "</p>";
    }

    $stmt->close();
} else {
    echo "<p>Error: SQL query is empty.</p>";
}
?>
