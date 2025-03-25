<?php
include "./db.php";

// SQL query to calculate win ratio and fetch the top 5 players (dropping registration_date, email_address)
$sql = "SELECT PlayerID, Username, Wins, Losses, Draws, Withdraw,
               (Wins / (Wins + Losses + Draws + 0.0)) AS win_ratio
        FROM Player
        ORDER BY win_ratio DESC
        LIMIT 5";

$result = null;
$error = "";

try {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $error = mysqli_error($conn);
    }
} catch (Throwable $e) {
    $error = "Fatal error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Top 5 Players Ranking</title>
</head>
<body>
    <h1>Top 5 Players Ranking</h1>
    <?php if ($error): ?>
        <p style="color:red;">Error: <?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($result && mysqli_num_rows($result) > 0): ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>PlayerID</th>
                <th>Username</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Draws</th>
                <th>Withdraw</th>
                <th>Win Ratio</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['PlayerID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    <td><?php echo htmlspecialchars($row['Wins']); ?></td>
                    <td><?php echo htmlspecialchars($row['Losses']); ?></td>
                    <td><?php echo htmlspecialchars($row['Draws']); ?></td>
                    <td><?php echo htmlspecialchars($row['Withdraw']); ?></td>
                    <td><?php echo number_format($row['win_ratio'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($result): ?>
        <p>No results found.</p>
    <?php endif; ?>
</body>
</html>
