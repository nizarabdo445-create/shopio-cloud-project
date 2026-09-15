<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | المنتجات</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="admin-content">

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 fw-bold text-dark">All Products</h1>
                <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                    <a href="show_user.php" class="btn btn-outline-secondary">
                        <i class="bi bi-people"></i> Show Users
                    </a>
                    <a href="add.php" class="btn btn-primary-custom">
                        <i class="bi bi-plus-lg"></i> Add New Product
                    </a>
                </div>
            </div>

            <div class="admin-card">
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col" width="10%">Image</th>
                                <th scope="col" width="30%">Product Name</th>
                                <th scope="col" width="20%">Price</th>
                                <th scope="col" width="20%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include('config.php');
                            $result = mysqli_query($con, "SELECT * FROM products");
                            while($row = mysqli_fetch_array($result)){
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($row['prod_img']); ?>" class="product-img-thumb" alt="Product Image">
                                </td>
                                <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['prod_name']); ?></td>
                                <td class="text-primary-custom fw-bold"><?php echo htmlspecialchars($row['prod_price']); ?></td>
                                <td class="text-end">
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php if(mysqli_num_rows($result) == 0): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam display-1 mb-3 d-block"></i>
                        <p>No products found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
