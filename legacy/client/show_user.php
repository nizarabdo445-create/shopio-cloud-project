<?php
include('config.php');
$sql="SELECT * FROM users";
$print=$con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | المستخدمين</title>
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
                <h1 class="h2 fw-bold text-dark">Registered Users</h1>
                <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-box-seam"></i> Manage Products
                    </a>
                </div>
            </div>

            <div class="admin-card">
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Created At</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($print && $print->num_rows > 0) {
                                while ($row = $print->fetch_assoc()) {
                            ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td class="fw-medium text-dark">
                                    <i class="bi bi-person-circle me-2 text-primary-custom"></i>
                                    <?php echo htmlspecialchars($row['user_name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                <td>
                                    <?php if ($row['user_role'] == 1): ?>
                                        <span class="badge bg-primary">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['created_at'] ?? 'N/A'); ?></td>
                                <td class="text-end">
                                    <?php if ($row['user_role'] != 1): ?>
                                    <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>Admin</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No users found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
