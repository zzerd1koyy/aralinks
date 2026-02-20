<?php
$conn = new mysqli("localhost", "root", "", "aralinks");

if ($conn->connect_error) {
    die("Connection failed");
}
?>