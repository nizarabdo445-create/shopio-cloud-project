document.addEventListener('DOMContentLoaded', () => {
    // Current Year for footer
    document.getElementById('currentYear').textContent = new Date().getFullYear();

    const authNavContainer = document.getElementById('authNavContainer');
    const productsContainer = document.getElementById('productsContainer');
    const loadingState = document.getElementById('loadingState');
    const apiAlert = document.getElementById('apiAlert');
    
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');

    // State
    const token = localStorage.getItem('token');
    let user = null;
    try {
        const userStr = localStorage.getItem('user');
        if (userStr) {
            user = JSON.parse(userStr);
        }
    } catch (e) {
        console.error("Error parsing user from localStorage", e);
    }

    // 1. Setup Auth UI
    if (token && user) {
        // Logged In State
        let adminLink = '';
        if (user.role === 1) {
            // Future Admin panel
            adminLink = `<a href="admin.html" class="btn btn-sm btn-outline-secondary">Admin</a>`;
        }

        authNavContainer.innerHTML = `
            <span class="text-muted small">Welcome, ${user.name}</span>
            ${adminLink}
            <button id="logoutBtn" class="btn btn-sm btn-outline-danger">Logout</button>
        `;

        document.getElementById('logoutBtn').addEventListener('click', () => {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = 'login.html';
        });

        // Load Cart from server if logged in
        loadCart();
    } else {
        // Logged Out State
        authNavContainer.innerHTML = `
            <a href="login.html" class="btn btn-sm btn-outline-primary">Login</a>
            <a href="register.html" class="btn btn-sm btn-primary-custom">Sign Up</a>
        `;
    }

    // 2. Load Products
    async function loadProducts() {
        try {
            const response = await fetch('/api/products');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            
            // Hide loading
            loadingState.style.display = 'none';

            if (result.status === 'success' && result.data && result.data.length > 0) {
                renderProducts(result.data);
            } else {
                showEmptyProducts();
            }
        } catch (error) {
            console.error("Failed to load products:", error);
            loadingState.style.display = 'none';
            apiAlert.textContent = "Failed to load products. Please try again later.";
            apiAlert.classList.add('alert-danger');
            apiAlert.classList.remove('d-none');
        }
    }

    function renderProducts(products) {
        productsContainer.innerHTML = products.map(product => {
            // Some API prices might be strings with symbols or just numbers
            const priceNum = typeof product.price === 'string' 
                ? parseFloat(product.price.replace(/[^0-9.]/g, '')) 
                : parseFloat(product.price);
            
            const formattedPrice = isNaN(priceNum) ? product.price : (priceNum + '$');

            return `
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card">
                    <div class="img-wrapper">
                        <img src="${product.image || product.prod_img}" alt="${product.name || product.prod_name}">
                    </div>
                    <div class="product-card-body">
                        <h3 class="product-title">${product.name || product.prod_name}</h3>
                        <div class="product-price">
                            ${formattedPrice}
                        </div>
                        <button onclick="addToCart(${product.id})" class="btn btn-primary-custom btn-add mt-auto">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function showEmptyProducts() {
        productsContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-box display-1 text-muted mb-3 d-block"></i>
                <p class="text-muted fs-5">No products available.</p>
            </div>
        `;
    }

    // 3. Cart Logic
    // Expose functions globally for inline onclick handlers
    window.addToCart = async function(productId) {
        if (!token) {
            showNotification('Please login to add items to your cart.', 'danger');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
            return;
        }

        try {
            const response = await fetch('/api/cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            });

            if (response.ok) {
                showNotification('Item added to cart successfully!', 'success');
                loadCart(); // Refresh cart
            } else {
                const resData = await response.json();
                showNotification(resData.message || 'Failed to add item to cart.', 'danger');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showNotification('Network error while adding to cart.', 'danger');
        }
    };

    window.removeItem = async function(cartItemId) {
        if (!token) return;

        try {
            const response = await fetch(`/api/cart/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                loadCart(); // Refresh cart
            } else {
                showNotification('Failed to remove item.', 'danger');
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
        }
    };

    async function loadCart() {
        if (!token) return;

        try {
            const response = await fetch('/api/cart', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    renderCart(result.data.items, result.data.total, result.data.item_count);
                }
            }
        } catch (error) {
            console.error('Failed to load cart:', error);
        }
    }

    function renderCart(items, total, count) {
        // Update count
        cartCount.textContent = count;
        
        if (items.length === 0) {
            cartItemsContainer.innerHTML = '';
            emptyCartMessage.style.display = 'block';
            cartTotal.textContent = '0$';
            return;
        }
        
        emptyCartMessage.style.display = 'none';
        
        cartItemsContainer.innerHTML = items.map(item => {
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
        
        cartTotal.textContent = `${total}$`;
    }

    function showNotification(message, type = 'success') {
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const toastHtml = `
            <div class="toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 start-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1060;">
                <div class="d-flex">
                    <div class="toast-body fw-medium">
                        <i class="bi ${icon} me-2"></i> ${message}
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

    // Initialize
    loadProducts();
});
