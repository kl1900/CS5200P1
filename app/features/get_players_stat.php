<?php
include "./db.php";

// Allowed columns for sorting
$allowedSort = ["Username", "playtime", "achievements"];

// Get sorting parameters from GET
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'Username';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

// SQL query with dynamic sorting
$sql = " 
select p.Username, PlayerPlayTime.playtime, achievements from (
        select pgs.PlayerID, SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, g.Game_start, g.Game_end))) as playtime
        from PlayerGameSession as pgs
        left join Game as g on pgs.GameID = g.GameID
        GROUP by pgs.PlayerID
    ) as PlayerPlayTime
    left join Player as p on PlayerPlayTime.PlayerID = p.PlayerID
    left join (
    	select pa.PlayerID, COUNT(pa.AchievementID) as achievements
        from PlayerAchievement as pa group by pa.PlayerID
    ) as achieves on p.PlayerID = achieves.PlayerID
    ORDER BY $sort $order
";

$result = null;
$error = "";

try {
    // Execute the query
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

<h2>Players Statistics</h2>

<?php if ($error): ?>
    <p style="color:red;">Error: <?php echo htmlspecialchars($error); ?></p>
<?php elseif ($result && mysqli_num_rows($result) > 0): ?>
    <?php
    // Get field names for headers
    $fields = [];
    $fieldCount = mysqli_num_fields($result);
    for ($i = 0; $i < $fieldCount; $i++) {
        $field = mysqli_fetch_field_direct($result, $i);
        $fields[] = $field->name;
    }
    ?>
    
    <table border="1" cellpadding="5">
        <tr>
            <?php foreach ($fields as $field): ?>
                <th>
                    <span class="sort-header" data-sort="<?php echo htmlspecialchars($field); ?>" data-order="<?php echo strtolower($order); ?>" style="cursor:pointer;">
                        <?php echo htmlspecialchars($field); ?><?php echo $arrow($field); ?>
                    </span>
                </th>
            <?php endforeach; ?>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($result): ?>
    <p>No results found.</p>
<?php endif; ?>