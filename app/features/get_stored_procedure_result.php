<?php
include "./db.php";
$procedure_text = trim($_GET['procedureText'] ?? "");

if (empty($procedure_text)) {
    echo "<p>Not a valid procedure.</p>";
    exit;
}

$result = null;
$error = "";

// Execute the query
if (!empty($procedure_text)) {
    $result = mysqli_query($conn, $procedure_text);
} else {
    echo "<p>Error: SQL query is empty.</p>";
}
if (!$result) {
    $error = mysqli_error($conn);
}

?>

<?php if ($error): ?>
        <p style="color:red;">Error: <?php echo $error; ?></p>
<?php elseif ($result && mysqli_num_rows($result) > 0): ?>
    <h3>Query Results:</h3>
    <table border="1" cellpadding="5">
        <tr>
            <?php while ($field = mysqli_fetch_field($result)) : ?>
                <th><?php echo htmlspecialchars($field->name); ?></th>
            <?php endwhile; ?>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <?php foreach ($row as $value) : ?>
                    <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($result): ?>
    <p>No results found.</p>
<?php endif; ?>
