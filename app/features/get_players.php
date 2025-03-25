<?php
include "./db.php";

// Determine which columns are allowed for sorting
// Fetch all column names from the Player table
$columns_query = "SHOW COLUMNS FROM Player";
$columns_result = mysqli_query($conn, $columns_query);
$allowedSort = [];

if ($columns_result) {
    while ($column = mysqli_fetch_assoc($columns_result)) {
        $allowedSort[] = $column['Field'];
    }
}

// Get sorting parameters from GET
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'PlayerID';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

// SQL query with sorting
$sql = "SELECT * FROM Player ORDER BY $sort $order";

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

<h2>All Players</h2>

<?php if ($error): ?>
    <p style="color:red;">Error: <?php echo htmlspecialchars($error); ?></p>
<?php elseif ($result && mysqli_num_rows($result) > 0): ?>
    <?php
    // Get field names and reset result pointer
    $fields = [];
    $fieldCount = mysqli_num_fields($result);
    for ($i = 0; $i < $fieldCount; $i++) {
        $field = mysqli_fetch_field_direct($result, $i);
        $fields[] = $field->name;
    }
    // Reset result pointer
    mysqli_data_seek($result, 0);
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
            
            <!-- Add extra column headers for Edit and Delete buttons -->
            <th>Edit</th>
            <th>Delete</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
                
                <!-- Add Edit and Delete links -->
                <td>
                    <a href="/features/edit_player.php?id=<?php echo $row['PlayerID']; ?>">Edit</a>
                </td>
                <td>
                    <a href="/features/delete_player.php?id=<?php echo $row['PlayerID']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($result): ?>
    <p>No results found.</p>
<?php endif; ?>