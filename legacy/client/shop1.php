<?php
session_start();
if(!isset($_SESSION['user_id'])){
    // header("url=login.php"); 
    // Keeping original logic, though header format is usually Location: login.php
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nizar Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include('config.php'); ?>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary-custom fs-3" href="#">Nizar Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#products">Products</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <button class="btn btn-light position-relative border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                        <i class="bi bi-cart3 fs-4 text-dark"></i>
                        <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-primary text-white py-5 mb-5">
        <div class="container py-4 text-center">
            <h1 class="display-4 fw-bold mb-3">Welcome to Nizar Store</h1>
            <p class="lead mb-0 text-white-50">Discover the best laptops and accessories at unbeatable prices.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5" id="products">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Latest Laptops</h2>
        </div>

        <div class="row g-4">
            <?php
            $result = mysqli_query($con, "SELECT * FROM products");
            while($row = mysqli_fetch_array($result)) {
            ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card">
                    <div class="img-wrapper">
                        <img src="<?php echo htmlspecialchars($row['prod_img']); ?>" alt="<?php echo htmlspecialchars($row['prod_name']); ?>">
                    </div>
                    <div class="product-card-body">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['prod_name']); ?></h3>
                        <div class="product-price">
                            <?php
                            $price = (float)preg_replace('/[^0-9.]/', '', $row['prod_price']);
                            echo number_format($price) . '$';
                            ?>
                        </div>
                        <button onclick="addToCart(<?php echo $row['id']; ?>)" class="btn btn-primary-custom btn-add mt-auto">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Cart Offcanvas -->
    <div class="offcanvas offcanvas-end offcanvas-cart" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="cartOffcanvasLabel">Shopping Cart</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cartItemsContainer" class="d-flex flex-column gap-3">
                <!-- Cart items will be injected here via JS -->
            </div>
            
            <!-- Empty Cart State -->
            <div id="emptyCartMessage" class="text-center py-5 text-muted">
                <i class="bi bi-cart-x display-1 mb-3 d-block"></i>
                <p>Your cart is empty.</p>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fs-5 fw-bold text-dark">Total:</span>
                <span id="cartTotal" class="fs-4 fw-bold text-primary-custom">0$</span>
            </div>
            <button class="btn btn-dark-custom w-100 py-2 fs-5">Checkout</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0 text-white-50">&copy; <?php echo date('Y'); ?> Nizar Store. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts -->
    <script>
        let cartItems = [];

        function addToCart(productId) {
            // Find the specific card
            const button = document.querySelector(`[onclick="addToCart(${productId})"]`);
            const cardBody = button.closest('.product-card-body');
            
            const product = {
                id: productId,
                name: cardBody.querySelector('.product-title').textContent,
                price: parseInt(cardBody.querySelector('.product-price').textContent.replace(/[^0-9]/g, '')),
                image: button.closest('.product-card').querySelector('img').src
            };

            const existingItem = cartItems.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cartItems.push({ ...product, quantity: 1 });
            }
            
            updateCart();
            showNotification(`${product.name} added to cart`);
        }

        function updateCart() {
            // Update counter
            const totalCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cartCount').textContent = totalCount;
            
            const container = document.getElementById('cartItemsContainer');
            const emptyMessage = document.getElementById('emptyCartMessage');
            
            if (cartItems.length === 0) {
                container.innerHTML = '';
                emptyMessage.style.display = 'block';
                document.getElementById('cartTotal').textContent = '0$';
                return;
            }
            
            emptyMessage.style.display = 'none';
            let totalPrice = 0;
            
            container.innerHTML = cartItems.map(item => {
                const itemTotal = item.price * item.quantity;
                totalPrice += itemTotal;
                return `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="cart-item-price">${item.price}$ x ${item.quantity}</span>
                                <button onclick="removeItem(${item.id})" class="btn btn-sm btn-outline-danger px-2 py-1">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('cartTotal').textContent = `${totalPrice}$`;
        }

        function removeItem(productId) {
            cartItems = cartItems.filter(item => item.id !== productId);
            updateCart();
        }

        function showNotification(message) {
            const toastHtml = `
                <div class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 start-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1060;">
                    <div class="d-flex">
                        <div class="toast-body fw-medium">
                            <i class="bi bi-check-circle-fill me-2"></i> ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.body.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    </script>
</body>
</html>
