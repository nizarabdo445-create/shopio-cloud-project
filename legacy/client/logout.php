<?php
session_start();
session_unset();
session_destroy();
header("url=login.php");
?>
