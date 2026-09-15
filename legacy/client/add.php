<?php
session_start();
 if(!isset($_SESSION['user_id'])){
    header("location:login.php");
}
elseif($_SESSION['user_role']==0){
    header("location:shop1.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | إضافة منتج</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="admin-content">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark mb-0">Add New Product</h2>
                <a href="products.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Products
                </a>
            </div>

            <div class="admin-card">
                <form method="post" action="insert_prod.php" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-box-seam fs-1 text-primary-custom"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-dark">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-tag text-muted"></i></span>
                            <input type="text" class="form-control" placeholder="Enter product name" name="prod_name" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-dark">Price</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-currency-dollar text-muted"></i></span>
                            <input type="text" class="form-control" placeholder="e.g. 999" name="prod_price" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-dark">Product Image</label>
                        <input class="form-control" type="file" id="file" name="prod_img" required>
                    </div>

                    <button type="submit" name="uplode" class="btn btn-primary-custom w-100 py-2 fs-5">
                        <i class="bi bi-cloud-arrow-up me-2"></i> Upload Product
                    </button>

                </form>
            </div>
            
            <div class="text-center mt-4">
                <a href="show_user.php" class="text-decoration-none text-muted">
                    <i class="bi bi-people me-1"></i> Manage Users
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
