<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "tusbinright");

if ($mysqli->connect_errno) {
    echo "❌ Connection failed: " . $mysqli->connect_error;
} else {
    echo "✅ Database connection successful!";
}
?>
