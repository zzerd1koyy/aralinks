<?php
include "db.php";

$code = $_POST['code'];

$result = $conn->query("SELECT * FROM vouchers WHERE code='$code' AND used=0");

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $conn->query("UPDATE vouchers SET used=1 WHERE code='$code'");
    header("Location: success.php?time=".$row['duration']);
}else{
    echo "Invalid or used voucher.";
}
?>