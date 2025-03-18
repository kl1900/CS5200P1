<?php
$servername = "mysql";  // The MySQL service name in docker-compose
$username = "root";
$password = "5800";
$dbname = "Practicum1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to MySQL!";
?>
