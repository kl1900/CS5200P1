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

// Filter parameters
$filterColumns = ['Wins', 'Losses', 'Draws', 'Withdraw'];
$filters = [];
$whereClause = "";
$hasFilters = false;

// Build WHERE clause for filters
foreach ($filterColumns as $column) {
    $minParam = "min_" . strtolower($column);
    $maxParam = "max_" . strtolower($column);
    
    $min = isset($_GET[$minParam]) && is_numeric($_GET[$minParam]) ? intval($_GET[$minParam]) : null;
    $max = isset($_GET[$maxParam]) && is_numeric($_GET[$maxParam]) ? intval($_GET[$maxParam]) : null;
    
    // Store current filter values for form
    $filters[$minParam] = $min;
    $filters[$maxParam] = $max;
    
    // Add to WHERE clause if filters are set
    if ($min !== null) {
        $whereClause .= ($whereClause ? " AND " : " WHERE ") . "$column >= $min";
        $hasFilters = true;
    }
    if ($max !== null) {
        $whereClause .= ($whereClause ? " AND " : " WHERE ") . "$column <= $max";
        $hasFilters = true;
    }
}

// SQL query with filtering and sorting
$sql = "SELECT * FROM Player" . $whereClause . " ORDER BY $sort $order";

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

// Helper function to build a URL to the current page with additional parameters
function buildUrl($additionalParams = []) {
    $base = 'features/all_players.php';
    
    // Start with current GET parameters
    $params = $_GET;
    
    // Add or override with additional parameters
    foreach ($additionalParams as $key => $value) {
        if ($value === null && isset($params[$key])) {
            unset($params[$key]);
        } else if ($value !== null) {
            $params[$key] = $value;
        }
    }
    
    // Build query string
    if (!empty($params)) {
        $base .= '?' . http_build_query($params);
    }
    
    return $base;
}
?>

<h2>All Players</h2>

<!-- Display current filters if any -->
<?php if ($hasFilters): ?>
<div style="margin-bottom: 10px; padding: 5px 10px; background-color: #e8f4f8; border-radius: 3px; border-left: 3px solid #4CAF50;">
    <p style="margin: 5px 0;">Active Filters: 
    <?php 
    $filterText = [];
    foreach ($filterColumns as $column) {
        $min = $filters['min_' . strtolower($column)];
        $max = $filters['max_' . strtolower($column)];
        
        if ($min !== null || $max !== null) {
            $text = "$column: ";
            if ($min !== null && $max !== null) {
                $text .= "$min - $max";
            } elseif ($min !== null) {
                $text .= "≥ $min";
            } else {
                $text .= "≤ $max";
            }
            $filterText[] = $text;
        }
    }
    echo implode(" | ", $filterText);
    ?>
    <a href="javascript:void(0)" onclick="
        const activeTab = document.querySelector('.tab-button.active');
        loadTab('features/all_players.php?sort=<?php echo urlencode($sort); ?>&order=<?php echo urlencode(strtolower($order)); ?>', activeTab);
    " style="margin-left: 10px; color: #f44336; text-decoration: none; font-weight: bold;">✖ Clear All</a>
    </p>
</div>
<?php endif; ?>

<!-- Filter Form - Improved Layout -->
<div style="margin-bottom: 20px; padding: 10px; background-color: #f5f5f5; border-radius: 5px;">
    <div style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 10px; align-items: flex-end;">
        <?php foreach ($filterColumns as $column): ?>
            <div style="flex: 0 0 auto; margin-right: 10px;">
                <label style="font-weight: bold; display: block; margin-bottom: 3px; font-size: 0.9em;"><?php echo htmlspecialchars($column); ?>:</label>
                <div style="display: flex; gap: 3px; align-items: center;">
                    <input 
                        type="number" 
                        id="filter_min_<?php echo strtolower($column); ?>" 
                        placeholder="Min" 
                        value="<?php echo isset($filters['min_' . strtolower($column)]) ? htmlspecialchars($filters['min_' . strtolower($column)]) : ''; ?>"
                        style="width: 50px; padding: 3px;"
                        onkeypress="if(event.key === 'Enter') { document.getElementById('apply-filter-btn').click(); }"
                    >
                    <span>-</span>
                    <input 
                        type="number" 
                        id="filter_max_<?php echo strtolower($column); ?>" 
                        placeholder="Max" 
                        value="<?php echo isset($filters['max_' . strtolower($column)]) ? htmlspecialchars($filters['max_' . strtolower($column)]) : ''; ?>"
                        style="width: 50px; padding: 3px;"
                        onkeypress="if(event.key === 'Enter') { document.getElementById('apply-filter-btn').click(); }"
                    >
                </div>
            </div>
        <?php endforeach; ?>
        
        <div style="flex: 0 0 auto; margin-bottom: 3px;">
            <button id="apply-filter-btn" onclick="
                const activeTab = document.querySelector('.tab-button.active');
                let url = 'features/all_players.php?';
                let params = [];
                
                <?php foreach ($filterColumns as $column): ?>
                const min<?php echo $column; ?> = document.getElementById('filter_min_<?php echo strtolower($column); ?>').value;
                const max<?php echo $column; ?> = document.getElementById('filter_max_<?php echo strtolower($column); ?>').value;
                
                if (min<?php echo $column; ?>) {
                    params.push('min_<?php echo strtolower($column); ?>=' + encodeURIComponent(min<?php echo $column; ?>));
                }
                if (max<?php echo $column; ?>) {
                    params.push('max_<?php echo strtolower($column); ?>=' + encodeURIComponent(max<?php echo $column; ?>));
                }
                <?php endforeach; ?>
                
                // Add current sort parameters
                params.push('sort=<?php echo htmlspecialchars($sort); ?>');
                params.push('order=<?php echo htmlspecialchars(strtolower($order)); ?>');
                
                url += params.join('&');
                loadTab(url, activeTab);
            " style="padding: 3px 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Apply Filters</button>
            
            <button onclick="
                const activeTab = document.querySelector('.tab-button.active');
                loadTab('features/all_players.php?sort=<?php echo urlencode($sort); ?>&order=<?php echo urlencode(strtolower($order)); ?>', activeTab);
            " style="padding: 3px 10px; background-color: #f44336; color: white; border: none; cursor: pointer; margin-left: 5px;">Clear</button>
        </div>
    </div>
</div>

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
    
    <p style="margin: 5px 0;"><strong>Found: <?php echo mysqli_num_rows($result); ?> players</strong></p>
    
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
                <button onclick="window.open('features/edit_player.php?id=<?php echo $row['PlayerID']; ?>', '_blank')">
                    Edit
                </button>
                <td>
                    <a href="features/delete_player.php?id=<?php echo $row['PlayerID']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($result): ?>
    <p>No results found with the current filters. 
        <button onclick="
            const activeTab = document.querySelector('.tab-button.active');
            loadTab('features/all_players.php?sort=<?php echo urlencode($sort); ?>&order=<?php echo urlencode(strtolower($order)); ?>', activeTab);
        " style="padding: 3px 10px; background-color: #f44336; color: white; border: none; cursor: pointer;">Clear filters</button>
    </p>
<?php endif; ?>