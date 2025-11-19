@extends('layouts.app')

@section('content')
<div class="py-3">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h4">POS System</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Product Search and Selection Panel -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" id="search-products" placeholder="Search products..." class="form-control">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Product List -->
                        <div class="row" id="product-list">
                            <!-- Products will be loaded here via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart/Transaction Summary Panel -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h6 mb-0">Transaction Cart</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Customer</label>
                            <select id="customer-select" class="form-select">
                                <option value="">Walk-in Customer</option>
                                <!-- Customer options will be loaded dynamically -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">Items:</span>
                                <span id="items-count">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">Subtotal:</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span class="fw-medium">Discount:</span>
                                <span id="discount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                <span class="fw-bold">Total:</span>
                                <span id="total" class="fw-bold">Rp 0</span>
                            </div>
                        </div>

                        <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <!-- Cart items will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button id="clear-cart" class="btn btn-outline-secondary flex-fill">
                                Clear
                            </button>
                            <button id="complete-transaction" class="btn btn-success flex-fill">
                                Complete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Cart state
    let cart = [];
    let allProducts = [];
    let allCustomers = [];

    // DOM Elements
    const productList = document.getElementById('product-list');
    const cartItems = document.getElementById('cart-items');
    const itemsCount = document.getElementById('items-count');
    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discount');
    const totalEl = document.getElementById('total');
    const customerSelect = document.getElementById('customer-select');
    const clearCartBtn = document.getElementById('clear-cart');
    const completeTransactionBtn = document.getElementById('complete-transaction');
    const newTransactionBtn = document.getElementById('new-transaction');
    const searchProductsInput = document.getElementById('search-products');

    // Initialize the POS interface
    document.addEventListener('DOMContentLoaded', function() {
        loadProductsFromAPI();
        loadCustomersFromAPI();
        updateCartDisplay();

        // Event listeners
        clearCartBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear the cart?')) {
                cart = [];
                updateCartDisplay();
            }
        });

        completeTransactionBtn.addEventListener('click', completeTransaction);
        newTransactionBtn.addEventListener('click', function() {
            if (cart.length > 0) {
                if (confirm('Current cart will be cleared. Continue?')) {
                    cart = [];
                    updateCartDisplay();
                }
            } else {
                updateCartDisplay();
            }
        });

        searchProductsInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredProducts = allProducts.filter(product =>
                product.name.toLowerCase().includes(searchTerm) ||
                product.code.toLowerCase().includes(searchTerm)
            );

            displayProducts(filteredProducts);
        });
    });

    // Load products from API
    function loadProductsFromAPI() {
        showLoadingProducts();

        const apiToken = localStorage.getItem('api_token') || document.querySelector('meta[name="api-token"]')?.getAttribute('content');

        if (!apiToken) {
            console.error('API token not found');
            showProductsError();
            return;
        }

        fetch('/api/products', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if(response.status === 401) {
                alert('Session expired. Please log in again.');
                window.location.href = '/login';
                return;
            }
            if(!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if(data.success && Array.isArray(data.data)) {
                allProducts = data.data; // Products array
                displayProducts(allProducts);
            } else if(Array.isArray(data)) {
                // If the response is a direct array (not wrapped in data property)
                allProducts = data;
                displayProducts(allProducts);
            } else {
                console.error('Error loading products:', data.message || 'Invalid response format');
                showProductsError();
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            showProductsError();
        });
    }

    // Load customers from API
    function loadCustomersFromAPI() {
        customerSelect.innerHTML = '<option value="">Walk-in Customer</option>';

        const apiToken = localStorage.getItem('api_token') || document.querySelector('meta[name="api-token"]')?.getAttribute('content');

        if (!apiToken) {
            console.error('API token not found');
            return;
        }

        fetch('/api/customers', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if(response.status === 401) {
                alert('Session expired. Please log in again.');
                window.location.href = '/login';
                return;
            }
            if(!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if(data.success && Array.isArray(data.data)) {
                allCustomers = data.data; // Customers array
                populateCustomerSelect(allCustomers);
            } else if(Array.isArray(data)) {
                // If the response is a direct array (not wrapped in data property)
                allCustomers = data;
                populateCustomerSelect(allCustomers);
            } else {
                console.error('Error loading customers:', data.message || 'Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error loading customers:', error);
        });
    }

    // Populate customer select dropdown
    function populateCustomerSelect(customers) {
        customerSelect.innerHTML = '<option value="">Walk-in Customer</option>';
        customers.forEach(customer => {
            const option = document.createElement('option');
            option.value = customer.id;
            option.textContent = customer.name;
            customerSelect.appendChild(option);
        });
    }

    // Display products in the product list
    function displayProducts(products) {
        productList.innerHTML = '';

        if (products.length === 0) {
            productList.innerHTML = '<div class="col-12"><p class="text-center text-muted">No products found</p></div>';
            return;
        }

        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'col-md-4 col-lg-3 mb-3'; // Adjusted grid for better layout
            productCard.innerHTML = `
                <div class="card h-100 shadow-sm border">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text text-muted small">Code: ${product.code}</p>
                        <p class="card-text fw-bold text-primary">Rp ${formatRupiah(product.price)}</p>
                        <p class="card-text text-success">Stock: ${product.stock}</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-sm w-100" onclick="addToCart(${product.id})">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
            productList.appendChild(productCard);
        });
    }

    // Show loading state for products
    function showLoadingProducts() {
        productList.innerHTML = '<div class="col-12"><p class="text-center text-muted">Loading products...</p></div>';
    }

    // Show error state for products
    function showProductsError() {
        productList.innerHTML = '<div class="col-12"><p class="text-center text-danger">Error loading products</p></div>';
    }

    // Add to cart function
    function addToCart(productId) {
        const product = allProducts.find(p => p.id === productId);
        if (!product) {
            alert('Product not found!');
            return;
        }

        // Check if product is already in cart
        const existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            // Check stock availability
            if (existingItem.quantity < product.stock) {
                existingItem.quantity++;
            } else {
                alert('Not enough stock available! Current stock: ' + product.stock);
                return;
            }
        } else {
            // Add new item to cart
            if (product.stock > 0) {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1
                });
            } else {
                alert('Product out of stock!');
                return;
            }
        }

        updateCartDisplay();
    }

    // Remove from cart function
    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        updateCartDisplay();
    }

    // Update quantity in cart
    function updateQuantity(productId, change) {
        const item = cart.find(item => item.id === productId);
        if (item) {
            const newQuantity = item.quantity + change;
            const product = allProducts.find(p => p.id === productId);

            if (newQuantity > 0 && newQuantity <= product.stock) {
                item.quantity = newQuantity;
            } else if (newQuantity <= 0) {
                cart = cart.filter(i => i.id !== productId);
            } else {
                alert('Cannot add more than available stock: ' + product.stock);
                return;
            }
        }
        updateCartDisplay();
    }

    // Update cart display
    function updateCartDisplay() {
        // Clear cart items
        cartItems.innerHTML = '';

        // Add each cart item
        cart.forEach(item => {
            const product = allProducts.find(p => p.id === item.id);
            const subtotal = item.price * item.quantity;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <button onclick="updateQuantity(${item.id}, -1)" class="btn btn-sm btn-outline-secondary">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, 1)" class="btn btn-sm btn-outline-secondary">+</button>
                    </div>
                </td>
                <td>Rp ${formatRupiah(item.price)}</td>
                <td>Rp ${formatRupiah(subtotal)}</td>
                <td>
                    <button onclick="removeFromCart(${item.id})" class="btn btn-sm btn-outline-danger">Remove</button>
                </td>
            `;
            cartItems.appendChild(row);
        });

        // Calculate totals
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;

        if (subtotal > 1000000) {
            discount = subtotal * 0.15; // 15% discount
        } else if (subtotal > 500000) {
            discount = subtotal * 0.10; // 10% discount
        }

        const total = subtotal - discount;

        // Update display
        itemsCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
        subtotalEl.textContent = `Rp ${formatRupiah(subtotal)}`;
        discountEl.textContent = `Rp ${formatRupiah(discount)}`;
        totalEl.textContent = `Rp ${formatRupiah(total)}`;
    }

    // Format currency function
    function formatRupiah(angka) {
        if (typeof angka !== 'number') {
            angka = parseFloat(angka) || 0;
        }

        const reverse = angka.toString().split('').reverse().join('');
        const ribuan = reverse.match(/\d{1,3}/g);
        const hasil = ribuan.join('.').split('').reverse().join('');
        return hasil;
    }

    // Complete transaction
    function completeTransaction() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        // Prepare transaction data
        const transactionData = {
            customer_id: customerSelect.value || null,
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity
            }))
        };

        // Get API token - fallback to meta tag if not in localStorage
        const apiToken = localStorage.getItem('api_token') || document.querySelector('meta[name="api-token"]')?.getAttribute('content');

        if (!apiToken) {
            alert('Authentication required. Please log in first.');
            window.location.href = '/login';
            return;
        }

        // Send transaction to API
        fetch('/api/transactions', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(transactionData)
        })
        .then(async response => {
            if(response.status === 401) {
                alert('Session expired. Please log in again.');
                window.location.href = '/login';
                return;
            }
            if(!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                alert('Transaction completed successfully!');
                console.log('Transaction response:', data);

                // Reset cart
                cart = [];
                updateCartDisplay();
            } else {
                alert('Error completing transaction: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while completing the transaction: ' + error.message);
        });
    }
</script>

<style>
    .max-height-300 {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endsection
