<?php
include 'db.php'; 
?>

<h3>Top Players and Their Achievements</h3>

<!-- Player dropdown -->
<select id="playerDropdown" onchange="filterByPlayer()">
    <option value="">-- Select a player --</option>
    <?php
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
            WinRate DESC, Wins DESC
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $playerId = $row['PlayerID'];
        $username = htmlspecialchars($row['Username']);
        $winRate = $row['WinRate'];

        // Show username with win rate in dropdown
        echo "<option value='$playerId'>$username (Win Rate: $winRate%)</option>";
    }
    ?>
</select>

<!-- Results go here -->
<div id="playerResults">
    <p>Please select a player from the dropdown above.</p>
</div>