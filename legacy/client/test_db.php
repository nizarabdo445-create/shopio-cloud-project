<?php
include "config.php";
$result = mysqli_query($con, "DESCRIBE users");
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
?>
