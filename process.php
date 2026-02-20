<?php
include "db.php";

$correct = 0;

if($_POST['q1']=="a") $correct++;
if($_POST['q2']=="b") $correct++;
if($_POST['q3']=="b") $correct++;
if($_POST['q4']=="b") $correct++;
if($_POST['q5']=="b") $correct++;

if($correct >= 4){

    $ip = $_SERVER['REMOTE_ADDR'];
    $today = date("Y-m-d");

    $check = $conn->query("SELECT * FROM users WHERE device_ip='$ip' AND last_access='$today'");

    if($check->num_rows == 0){
        $conn->query("INSERT INTO users(device_ip,last_access) VALUES('$ip','$today')");
        header("Location: success.php?time=60");
    } else {
        echo "You already used today's free access.";
    }

}else{
    echo "Incorrect answers. Please try again.";
}
?>