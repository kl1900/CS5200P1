<?php
include "./db.php";

// Allowed columns for sorting
$allowedSort = ["PlayerID", "Username", "Wins", "Losses", "Draws", "Withdraw", "win_ratio"];

// Get sorting parameters from GET
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'win_ratio';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

// SQL query to fetch top 5 players by win ratio
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

// Default icon for sorting
$defaultIcon = ' <span style="color:#999;">↕</span>';
// Arrow function to generate arrow icons based on column
$arrow = fn($col) => $sort === $col ? ($order === 'ASC' ? ' ▲' : ' ▼') : $defaultIcon;
?>

<h1>Top 5 Players Ranking</h1>
<?php if ($error): ?>
    <p style="color:red;">Error: <?php echo htmlspecialchars($error); ?></p>
<?php elseif ($result && mysqli_num_rows($result) > 0): ?>
    <?php
    // Fetch all rows into an array
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    
    // Sort the rows based on the selected column and order
    if ($sort !== 'win_ratio' || $order !== 'DESC') {
        usort($rows, function($a, $b) use ($sort, $order) {
            // Handle numeric and string comparisons
            if (is_numeric($a[$sort]) && is_numeric($b[$sort])) {
                return ($order === 'ASC') ? $a[$sort] <=> $b[$sort] : $b[$sort] <=> $a[$sort];
            } else {
                return ($order === 'ASC') ? 
                    strcmp(strtolower($a[$sort]), strtolower($b[$sort])) :
                    strcmp(strtolower($b[$sort]), strtolower($a[$sort]));
            }
        });
    }
    ?>
    <table border="1" cellpadding="5">
        <tr>
            <th><span class="sort-header" data-sort="PlayerID" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">PlayerID<?php echo $arrow('PlayerID'); ?></span></th>
            <th><span class="sort-header" data-sort="Username" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">Username<?php echo $arrow('Username'); ?></span></th>
            <th><span class="sort-header" data-sort="Wins" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">Wins<?php echo $arrow('Wins'); ?></span></th>
            <th><span class="sort-header" data-sort="Losses" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">Losses<?php echo $arrow('Losses'); ?></span></th>
            <th><span class="sort-header" data-sort="Draws" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">Draws<?php echo $arrow('Draws'); ?></span></th>
            <th><span class="sort-header" data-sort="Withdraw" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">Withdraw<?php echo $arrow('Withdraw'); ?></span></th>
            <th><span class="sort-header" data-sort="win_ratio" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">Win Ratio<?php echo $arrow('win_ratio'); ?></span></th>
        </tr>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['PlayerID']); ?></td>
                <td><?php echo htmlspecialchars($row['Username']); ?></td>
                <td><?php echo htmlspecialchars($row['Wins']); ?></td>
                <td><?php echo htmlspecialchars($row['Losses']); ?></td>
                <td><?php echo htmlspecialchars($row['Draws']); ?></td>
                <td><?php echo htmlspecialchars($row['Withdraw']); ?></td>
                <td><?php echo number_format($row['win_ratio'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- No script tag here as we'll use the main page's event handling -->
<?php elseif ($result): ?>
    <p>No results found.</p>
<?php endif; ?>