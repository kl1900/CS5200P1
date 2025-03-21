
<!-- form submission section -->
<?php
include "./db.php";

$sql = "select * from Player";
$result = null;
$error = "";
try {
    // Execute the query
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $error = mysqli_error($conn);
    }
} catch (Throwable $e){
    $error = "Fatal error: " . $e->getMessage();
}
?>
<?php if ($error): ?>
    <p style="color:red;">Error: <?php echo $error; ?></p>
<?php elseif ($result && mysqli_num_rows($result) > 0): ?>

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
