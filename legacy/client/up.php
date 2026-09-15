<?php
include('config.php');
if(isset($_POST['update'])){
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($con, $_POST['prod_name']);
    $price = mysqli_real_escape_string($con, $_POST['prod_price']);
    
    if(!empty($_FILES['prod_img']['name'])) {
        $img_name = $_FILES['prod_img']['name'];
        $img_tmp = $_FILES['prod_img']['tmp_name'];
        
        if (!is_dir('img')) {
            mkdir('img', 0777, true);
        }
        
        $img_up = "img/" . time() . "_" . basename($img_name);
        
        if(move_uploaded_file($img_tmp, $img_up)){
            $update = "UPDATE products SET prod_name ='$name', prod_price='$price', prod_img='$img_up' WHERE id=$id";
            mysqli_query($con, $update);
        }
    } else {
        // If no new image is uploaded, only update name and price
        $update = "UPDATE products SET prod_name ='$name', prod_price='$price' WHERE id=$id";
        mysqli_query($con, $update);
    }
    
    header("location:products.php");
    exit();
}
?>
