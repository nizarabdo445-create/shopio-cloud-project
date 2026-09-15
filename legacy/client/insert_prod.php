<?php
include('config.php');
if(isset($_POST['uplode'])){
    $name = mysqli_real_escape_string($con, $_POST['prod_name']);
    $price = mysqli_real_escape_string($con, $_POST['prod_price']);
    
    $img_name = $_FILES['prod_img']['name'];
    $img_tmp = $_FILES['prod_img']['tmp_name'];
    
    if (!is_dir('img')) {
        mkdir('img', 0777, true);
    }
    
    // Add unique ID to avoid overwriting files with the same name
    $img_up = "img/" . time() . "_" . basename($img_name);
    
    if(move_uploaded_file($img_tmp, $img_up)){
        $insert = "INSERT INTO products (prod_name, prod_price, prod_img) VALUES ('$name','$price','$img_up')";
        mysqli_query($con, $insert);
    }
    header("location:products.php");
    exit();
}
?>
