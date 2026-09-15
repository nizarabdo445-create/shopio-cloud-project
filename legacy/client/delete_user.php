<?php
include('config.php');
$id=$_GET['id'];
mysqli_query($con, "DELETE FROM users WHERE user_id=$id");
header("location:show_user.php");
?>
