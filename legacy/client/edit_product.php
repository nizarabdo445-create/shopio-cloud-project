<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | تعديل منتج</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="admin-content">
    <?php
    include('config.php');
    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        // Use prepared statements ideally, but keeping existing logic structure
        $update = mysqli_query($con, "SELECT * FROM products WHERE id = $id");
        $data = mysqli_fetch_array($update);
    } else {
        header("Location: products.php");
        exit();
    }
    ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark mb-0">Update Product</h2>
                <a href="products.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Products
                </a>
            </div>

            <div class="admin-card">
                <form method="post" action="up.php" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-pencil-square fs-1 text-primary-custom"></i>
                        </div>
                        <?php if(!empty($data['prod_img'])): ?>
                            <div>
                                <img src="<?php echo htmlspecialchars($data['prod_img']); ?>" class="img-thumbnail" style="max-height: 100px;" alt="Current Product Image">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Hidden ID -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">

                    <div class="mb-3">
                        <label class="form-label fw-medium text-dark">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-tag text-muted"></i></span>
                            <input type="text" class="form-control" name="prod_name" value="<?php echo htmlspecialchars($data['prod_name']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-dark">Price</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-currency-dollar text-muted"></i></span>
                            <input type="text" class="form-control" name="prod_price" value="<?php echo htmlspecialchars($data['prod_price']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-dark">Update Product Image (Optional)</label>
                        <input class="form-control" type="file" id="file" name="prod_img">
                        <div class="form-text">Leave blank to keep the current image.</div>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary-custom w-100 py-2 fs-5">
                        <i class="bi bi-save me-2"></i> Save Changes
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
