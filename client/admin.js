document.addEventListener('DOMContentLoaded', () => {
    // Auth Check
    const token = localStorage.getItem('token');
    let user = null;
    try {
        const userStr = localStorage.getItem('user');
        if (userStr) {
            user = JSON.parse(userStr);
        }
    } catch (e) { }

    if (!token || !user || user.role !== 1) {
        // Not admin or not logged in
        window.location.href = 'login.html';
        return;
    }

    document.getElementById('adminNameDisplay').textContent = user.name;
    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = 'login.html';
    });

    const globalAlert = document.getElementById('globalAlert');
    
    // Globals for state
    let productsList = [];
    let deleteConfig = { type: null, id: null };
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));

    // Navigation
    window.switchTab = function(tab) {
        document.querySelectorAll('.admin-section').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
        
        if (tab === 'products') {
            document.getElementById('productsSection').classList.add('active');
            document.querySelector('[onclick="switchTab(\'products\')"]').classList.add('active');
            loadProducts();
        } else if (tab === 'users') {
            document.getElementById('usersSection').classList.add('active');
            document.querySelector('[onclick="switchTab(\'users\')"]').classList.add('active');
            loadUsers();
        }
    };

    function showAlert(message, type = 'danger') {
        globalAlert.className = `alert alert-${type}`;
        globalAlert.textContent = message;
        globalAlert.classList.remove('d-none');
        setTimeout(() => globalAlert.classList.add('d-none'), 4000);
    }

    // ==========================================
    // PRODUCTS MANAGEMENT
    // ==========================================

    async function loadProducts() {
        const tbody = document.getElementById('productsTableBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading products...</td></tr>';
        
        try {
            const res = await fetch('/api/products');
            if (!res.ok) throw new Error('Failed to fetch products');
            const result = await res.json();
            
            if (result.status === 'success') {
                productsList = result.data;
                renderProducts(productsList);
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading products.</td></tr>';
            console.error(error);
        }
    }

    function renderProducts(products) {
        const tbody = document.getElementById('productsTableBody');
        if (products.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No products found.</td></tr>';
            return;
        }

        tbody.innerHTML = products.map(p => {
            const price = typeof p.price === 'string' ? parseFloat(p.price.replace(/[^0-9.]/g, '')) : p.price;
            const imgSrc = p.image || p.prod_img;
            // Image fallback implementation
            const imgHtml = imgSrc 
                ? `<img src="${imgSrc}" class="img-thumbnail-table rounded" onerror="this.outerHTML='<div class=\\'broken-img-placeholder\\'><i class=\\'bi bi-image text-muted\\'></i></div>'">`
                : `<div class="broken-img-placeholder"><i class="bi bi-image text-muted"></i></div>`;

            return `
            <tr>
                <td>${p.id}</td>
                <td>${imgHtml}</td>
                <td class="fw-medium">${p.name || p.prod_name}</td>
                <td>$${price}</td>
                <td class="text-muted small">${p.created_at || '-'}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(${p.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('product', ${p.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    window.openAddModal = function() {
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productModalLabel').textContent = 'Add Product';
        document.getElementById('modalAlert').classList.add('d-none');
        document.getElementById('imageUploadGroup').classList.remove('d-none');
        document.getElementById('prodImg').required = true;
    };

    window.openEditModal = function(id) {
        const product = productsList.find(p => p.id === id);
        if (!product) return;

        document.getElementById('productForm').reset();
        document.getElementById('productId').value = product.id;
        document.getElementById('prodName').value = product.name || product.prod_name;
        
        let price = product.price;
        if (typeof price === 'string') price = parseFloat(price.replace(/[^0-9.]/g, ''));
        document.getElementById('prodPrice').value = price;
        
        document.getElementById('productModalLabel').textContent = 'Edit Product';
        document.getElementById('modalAlert').classList.add('d-none');
        
        // Hide image upload in edit mode as per API limitations
        document.getElementById('imageUploadGroup').classList.add('d-none');
        document.getElementById('prodImg').required = false;
        
        productModal.show();
    };

    document.getElementById('productForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('productId').value;
        const name = document.getElementById('prodName').value;
        const price = document.getElementById('prodPrice').value;
        const fileInput = document.getElementById('prodImg');
        
        const btn = document.getElementById('saveProductBtn');
        const spinner = document.getElementById('saveSpinner');
        const mAlert = document.getElementById('modalAlert');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        mAlert.classList.add('d-none');

        try {
            let res;
            if (id) {
                // EDIT (PUT) - JSON only
                res = await fetch(`/api/products/${id}`, {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}` 
                    },
                    body: JSON.stringify({ name, price })
                });
            } else {
                // ADD (POST) - FormData with Image
                if (fileInput.files.length === 0) {
                    mAlert.textContent = 'Product image is required for new products.';
                    mAlert.classList.remove('d-none');
                    mAlert.className = 'alert alert-danger';
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    return;
                }
                const formData = new FormData();
                formData.append('name', name);
                formData.append('price', price);
                formData.append('prod_img', fileInput.files[0]);

                res = await fetch('/api/products', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });
            }

            const result = await res.json();
            
            if (!res.ok) {
                let msg = result.message || 'Error saving product';
                if (result.errors) msg = result.errors.join('<br>');
                mAlert.innerHTML = msg;
                mAlert.classList.remove('d-none');
                mAlert.className = 'alert alert-danger';
            } else {
                productModal.hide();
                showAlert(id ? 'Product updated successfully' : 'Product added successfully', 'success');
                loadProducts();
            }
        } catch (error) {
            console.error(error);
            mAlert.textContent = 'A network error occurred.';
            mAlert.classList.remove('d-none');
            mAlert.className = 'alert alert-danger';
        } finally {
            btn.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    // ==========================================
    // USERS MANAGEMENT
    // ==========================================

    async function loadUsers() {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading users...</td></tr>';
        
        try {
            const res = await fetch('/api/users', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            if (res.status === 401 || res.status === 403) {
                throw new Error('Unauthorized');
            }
            if (!res.ok) throw new Error('Failed to fetch users');
            
            const result = await res.json();
            
            if (result.status === 'success') {
                renderUsers(result.data);
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading users. (You might not have permission)</td></tr>';
            console.error(error);
        }
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No users found.</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(u => {
            const roleBadge = u.role === 1 
                ? '<span class="badge bg-danger">Admin</span>' 
                : '<span class="badge bg-secondary">User</span>';
                
            // Disable delete button for the current admin
            const isSelf = u.user_id === user.id;
            const delBtn = isSelf 
                ? `<button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete yourself"><i class="bi bi-trash"></i></button>`
                : `<button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('user', ${u.user_id})"><i class="bi bi-trash"></i></button>`;

            return `
            <tr>
                <td>${u.user_id}</td>
                <td class="fw-medium">${u.name}</td>
                <td>${u.email}</td>
                <td>${roleBadge}</td>
                <td class="text-muted small">${u.created_at || '-'}</td>
                <td class="text-end">${delBtn}</td>
            </tr>`;
        }).join('');
    }

    // ==========================================
    // SHARED DELETION LOGIC
    // ==========================================

    window.confirmDelete = function(type, id) {
        deleteConfig = { type, id };
        document.getElementById('deleteItemType').textContent = type;
        deleteModal.show();
    };

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        const { type, id } = deleteConfig;
        if (!type || !id) return;
        
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;
        btn.textContent = 'Deleting...';

        try {
            const endpoint = type === 'product' ? `/api/products/${id}` : `/api/users/${id}`;
            const res = await fetch(endpoint, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (res.ok) {
                deleteModal.hide();
                showAlert(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully.`, 'success');
                if (type === 'product') loadProducts();
                else loadUsers();
            } else {
                const data = await res.json();
                deleteModal.hide();
                showAlert(data.message || `Failed to delete ${type}.`, 'danger');
            }
        } catch (error) {
            console.error(error);
            deleteModal.hide();
            showAlert('Network error during deletion.', 'danger');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Delete';
            deleteConfig = { type: null, id: null };
        }
    });

    // Initial load
    loadProducts();
});
