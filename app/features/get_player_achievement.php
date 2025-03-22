<?php
include 'db.php';  // database connection
?>

<h3>Top Players and Their Achievements</h3>

<!-- Player dropdown -->
<select id="playerDropdown" onchange="filterByPlayer()">
    <option value="">-- Select a player --</option>
    <?php
    $sql = "SELECT PlayerID, Username FROM Player ORDER BY Username ASC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['PlayerID']}'>{$row['Username']}</option>";
    }
    ?>
</select>

<!-- Results go here -->
<div id="playerResults">
    <p>Please select a player from the dropdown above.</p>
</div>
