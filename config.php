<?php
// Database connection & session start
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "cms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
?>
