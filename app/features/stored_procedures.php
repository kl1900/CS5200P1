<?php
include 'db.php'; 
?>

<h3>SQL Query Executor</h3>
    <form method="post">
        <label for="sql_query">Enter your SQL query:</label><br>
        <textarea name="sql_query" id="sql_query" rows="4" cols="60"><?php echo isset($_POST["sql_query"]) ? htmlspecialchars($_POST["sql_query"]) : ""; ?></textarea><br><br>
        <button type="submit" onclick="procedureExecuteFunc()">Execute</button>
        <button type="submit" onclick="procedureStoreFunc()">Store</button>
    </form>

<hr>

<h3>Stored Procedures</h3>

<select id="procedureDropDown" onchange="procedureDropDownFunc()">
    <option value="">-- Select a procedure --</option>
    <?php
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
</select>

<!-- Results go here -->
<div id="procedureResults">
    <p>Please select a stored procedure.</p>
</div>