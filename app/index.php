<?php include "./includes/header.php"?>

<!-- form submission section -->
<?php
$result = null;
$error = "";
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["sql_query"])) {
        $query = $_POST["sql_query"];

        // Execute the query
        $result = mysqli_query($conn, $query);
        if (!$result) {
            $error = mysqli_error($conn);
        }
    }
} catch (Throwable $e){
    $error = "Fatal error: " . $e->getMessage();
}
?>

<h3>SQL Query Executor</h3>
<form method="post">
    <label for="sql_query">Enter your SQL query:</label><br>
    <textarea name="sql_query" id="sql_query" rows="4" cols="60"><?php echo isset($_POST["sql_query"]) ? htmlspecialchars($_POST["sql_query"]) : ""; ?></textarea><br><br>
    <button type="submit">Execute</button>
</form>


<hr>

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

<?php include "./includes/footer.php" ?>
